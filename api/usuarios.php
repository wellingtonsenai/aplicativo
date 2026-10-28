<?php
require 'conexao.php';
exigirSupervisor();

// GET = listar
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    responder($pdo->query('SELECT id, matricula, nome, perfil, ativo FROM usuarios ORDER BY matricula')->fetchAll());
}

// POST = inserir, editar ou excluir (campo "acao")
$dados     = lerCorpo();
$acao      = $dados['acao'] ?? '';
$id        = (int) ($dados['id'] ?? 0);
$matricula = trim($dados['matricula'] ?? '');
$nome      = trim($dados['nome'] ?? '');
$senha     = $dados['senha'] ?? '';
$perfil    = $dados['perfil'] ?? '';

if ($acao === 'excluir') {
    $pdo->prepare('DELETE FROM usuarios WHERE id = ?')->execute([$id]);
    responder(['ok' => true]);
}

if ($acao !== 'inserir' && $acao !== 'editar') {
    responder(['erro' => 'Ação inválida.'], 400);
}
if ($matricula === '' || $nome === '') {
    responder(['erro' => 'Preencha matrícula e nome.'], 400);
}
if (!in_array($perfil, ['atendente', 'supervisor'], true)) {
    responder(['erro' => 'Perfil inválido.'], 400);
}
if ($acao === 'inserir' && $senha === '') {
    responder(['erro' => 'Informe a senha.'], 400);
}

try {
    if ($acao === 'inserir') {
        $pdo->prepare('INSERT INTO usuarios (matricula, nome, senha, perfil) VALUES (?, ?, ?, ?)')
            ->execute([$matricula, $nome, password_hash($senha, PASSWORD_DEFAULT), $perfil]);
    } elseif ($senha === '') {
        // Senha em branco na edição = manter a atual
        $pdo->prepare('UPDATE usuarios SET matricula = ?, nome = ?, perfil = ? WHERE id = ?')
            ->execute([$matricula, $nome, $perfil, $id]);
    } else {
        $pdo->prepare('UPDATE usuarios SET matricula = ?, nome = ?, perfil = ?, senha = ? WHERE id = ?')
            ->execute([$matricula, $nome, $perfil, password_hash($senha, PASSWORD_DEFAULT), $id]);
    }
} catch (PDOException $e) {
    if ($e->getCode() === '23000') {
        responder(['erro' => 'Matrícula já cadastrada.'], 409);
    }
    throw $e;
}

responder(['ok' => true]);
