<?php
header('Content-Type: application/json; charset=utf-8');

function responder($dados, $status = 200) {
    http_response_code($status);
    echo json_encode($dados);
    exit;
}

// Qualquer erro não tratado vira JSON, para o fetch() conseguir ler
set_exception_handler(function () {
    responder(['erro' => 'Erro no servidor.'], 500);
});

$pdo = new PDO('mysql:host=localhost;dbname=cantina;charset=utf8mb4', 'root', '', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);

session_start();

function exigirLogin() {
    if (!isset($_SESSION['usuario'])) {
        responder(['erro' => 'Faça login para continuar.'], 401);
    }
}

function exigirSupervisor() {
    if (($_SESSION['usuario']['perfil'] ?? '') !== 'supervisor') {
        responder(['erro' => 'Acesso permitido só para supervisor logado.'], 401);
    }
}

function lerCorpo() {
    return json_decode(file_get_contents('php://input'), true) ?? [];
}
