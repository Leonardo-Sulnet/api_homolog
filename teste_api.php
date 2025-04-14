<?php


require_once __DIR__ . '/vendor/autoload.php';

// Carregar variáveis de ambiente do arquivo .env
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Agora as variáveis de ambiente estão carregadas
$DB_HOST = $_ENV['DB_ROLETA_HOST'];

echo $DB_HOST;

echo json_encode(['status' => 400, 'error' => 'Campos obrigatórios não enviados']);
