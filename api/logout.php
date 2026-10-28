<?php
require 'conexao.php';

$_SESSION = [];
session_destroy();

responder(['ok' => true]);
