<?php
require 'conexao.php';
exigirSupervisor();

// GET = listar, com a quantidade de alunos de cada turma
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    responder($pdo->query('SELECT t.id, t.nome, COUNT(a.id) AS alunos
                           FROM turmas t LEFT JOIN alunos a ON a.turma_id = t.id
                           GROUP BY t.id, t.nome ORDER BY t.nome')->fetchAll());
}

// POST = inserir, editar ou excluir (campo "acao")
$dados = lerCorpo();
$acao  = $dados['acao'] ?? '';
$id    = (int) ($dados['id'] ?? 0);
$nome  = trim($dados['nome'] ?? '');

if ($acao === 'excluir') {
    try {
        $pdo->prepare('DELETE FROM turmas WHERE id = ?')->execute([$id]);
    } catch (PDOException $e) {
        responder(['erro' => 'A turma tem alunos. Mude os alunos de turma antes de excluir.'], 409);
    }
    responder(['ok' => true]);
}

if ($acao !== 'inserir' && $acao !== 'editar') {
    responder(['erro' => 'Ação inválida.'], 400);
}
if ($nome === '') {
    responder(['erro' => 'Informe o nome da turma.'], 400);
}

try {
    if ($acao === 'inserir') {
        $pdo->prepare('INSERT INTO turmas (nome) VALUES (?)')->execute([$nome]);
    } else {
        $pdo->prepare('UPDATE turmas SET nome = ? WHERE id = ?')->execute([$nome, $id]);
    }
} catch (PDOException $e) {
    if ($e->getCode() === '23000') {
        responder(['erro' => 'Já existe uma turma com esse nome.'], 409);
    }
    throw $e;
}

responder(['ok' => true]);
