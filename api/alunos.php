<?php
require 'conexao.php';
exigirSupervisor();

// GET = listar
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    responder($pdo->query('SELECT a.id, a.matricula, a.nome, a.turma_id, t.nome AS turma, a.codigo, a.ativo
                           FROM alunos a JOIN turmas t ON t.id = a.turma_id ORDER BY a.nome')->fetchAll());
}

// POST = inserir, editar ou excluir (campo "acao")
$dados     = lerCorpo();
$acao      = $dados['acao'] ?? '';
$id        = (int) ($dados['id'] ?? 0);
$matricula = trim($dados['matricula'] ?? '');
$nome      = trim($dados['nome'] ?? '');
$turmaId   = (int) ($dados['turma_id'] ?? 0);
$ativo     = ($dados['ativo'] ?? '1') === '1' ? 1 : 0;

if ($acao === 'excluir') {
    try {
        $pdo->prepare('DELETE FROM alunos WHERE id = ?')->execute([$id]);
    } catch (PDOException $e) {
        // A chave estrangeira em entregas protege o histórico
        responder(['erro' => 'Aluno já retirou lanches. Marque como inativo em vez de excluir.'], 409);
    }
    responder(['ok' => true]);
}

if ($acao !== 'inserir' && $acao !== 'editar') {
    responder(['erro' => 'Ação inválida.'], 400);
}
if ($matricula === '' || $nome === '' || $turmaId === 0) {
    responder(['erro' => 'Preencha matrícula, nome e turma.'], 400);
}

try {
    if ($acao === 'inserir') {
        $pdo->prepare('INSERT INTO alunos (matricula, nome, turma_id, codigo, ativo) VALUES (?, ?, ?, ?, ?)')
            ->execute([$matricula, $nome, $turmaId, bin2hex(random_bytes(16)), $ativo]);
    } else {
        $pdo->prepare('UPDATE alunos SET matricula = ?, nome = ?, turma_id = ?, ativo = ? WHERE id = ?')
            ->execute([$matricula, $nome, $turmaId, $ativo, $id]);
    }
} catch (PDOException $e) {
    if ($e->getCode() === '23000') {
        // 23000 = matrícula repetida (UNIQUE) ou turma inexistente (chave estrangeira)
        responder(['erro' => 'Matrícula já cadastrada ou turma inválida.'], 409);
    }
    throw $e;
}

responder(['ok' => true]);
