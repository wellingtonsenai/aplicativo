<?php
require 'conexao.php';

$dados = lerCorpo();

$stmt = $pdo->prepare('SELECT id, matricula, nome, senha, perfil FROM usuarios WHERE matricula = ? AND ativo = 1');
$stmt->execute([trim($dados['matricula'] ?? '')]);
$usuario = $stmt->fetch();

if (!$usuario || !password_verify($dados['senha'] ?? '', $usuario['senha'])) {
    responder(['aceito' => false, 'mensagem' => 'Matrícula ou senha incorreta.', 'usuario' => null]);
}

unset($usuario['senha']);
session_regenerate_id(true);
$_SESSION['usuario'] = $usuario;

responder(['aceito' => true, 'mensagem' => 'Bem-vindo(a), ' . $usuario['nome'] . '!', 'usuario' => $usuario]);
