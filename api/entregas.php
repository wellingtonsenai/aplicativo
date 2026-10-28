<?php
require 'conexao.php';
exigirLogin();

// GET = relatório de entregues, filtrando por dia e (opcional) turma
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    exigirSupervisor();
    $data = $_GET['data'] ?? '';
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $data)) {
        $data = date('Y-m-d');
    }
    $sql = 'SELECT DATE_FORMAT(e.entregue_em, "%H:%i") AS hora, a.matricula, a.nome, t.nome AS turma, u.nome AS atendente
            FROM entregas e
            JOIN alunos a ON a.id = e.aluno_id
            JOIN turmas t ON t.id = a.turma_id
            LEFT JOIN usuarios u ON u.id = e.usuario_id
            WHERE e.data = ?';
    $params = [$data];
    if (!empty($_GET['turma_id'])) {
        $sql .= ' AND a.turma_id = ?';
        $params[] = (int) $_GET['turma_id'];
    }
    $stmt = $pdo->prepare($sql . ' ORDER BY e.entregue_em');
    $stmt->execute($params);
    responder($stmt->fetchAll());
}

$codigo = trim(lerCorpo()['codigo'] ?? '');

$stmt = $pdo->prepare('SELECT a.id, a.matricula, a.nome, t.nome AS turma FROM alunos a JOIN turmas t ON t.id = a.turma_id WHERE a.codigo = ? AND a.ativo = 1');
$stmt->execute([$codigo]);
$aluno = $stmt->fetch();

if (!$aluno) {
    responder(['erro' => 'QR Code inválido ou aluno inativo.'], 404);
}

try {
    $pdo->prepare('INSERT INTO entregas (aluno_id, usuario_id, data) VALUES (?, ?, CURDATE())')
        ->execute([$aluno['id'], $_SESSION['usuario']['id']]);
} catch (PDOException $e) {
    if ($e->getCode() !== '23000') {
        throw $e;
    }
    $stmt = $pdo->prepare('SELECT DATE_FORMAT(entregue_em, "%H:%i") FROM entregas WHERE aluno_id = ? AND data = CURDATE()');
    $stmt->execute([$aluno['id']]);
    responder(['erro' => $aluno['nome'] . ' já retirou o lanche hoje às ' . $stmt->fetchColumn() . '.'], 409);
}

responder(['ok' => true, 'aluno' => $aluno]);
