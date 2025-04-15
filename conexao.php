<?php

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . 'conexao.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

//Acessso ao servidor de replicação do MK

$servidor_mk = $_ENV['DB_MK_HOST'];
$porta_mk = $_ENV['DB_MK_PORT'];
$banco_mk = $_ENV['DB_MK_DATABASE'];
$usuario_mk = $_ENV['DB_MK_USERNAME'];
$senha_mk = $_ENV['DB_MK_PASSWORD'];

try {
    $dsn_mk = "pgsql:host=$servidor_mk;port=$porta_mk;dbname=$banco_mk";
    $conn_mk = new PDO($dsn_mk, $usuario_mk, $senha_mk);
    $conn_mk->exec("SET NAMES 'utf8'");
} catch (PDOException $e) {
    die($e->getMessage());
}

//Acessso ao servidor de API

$servidor_api = $_ENV['DB_API_HOST'];
$porta_api = $_ENV['DB_API_PORT'];
$banco_api = $_ENV['DB_API_DATABASE'];
$usuario_api = $_ENV['DB_API_USERNAME'];
$senha_api = $_ENV['DB_API_PASSWORD'];

try {
    $dsn_api = "pgsql:host=$servidor_api;port=$porta_api;dbname=$banco_api";
    $conn_api = new PDO($dsn_api, $usuario_api, $senha_api);
    $conn_api->exec("SET NAMES 'utf8'");
} catch (PDOException $e) {
    die($e->getMessage());
}


$servidor_reagendamento = $_ENV['DB_REAGENDAMENTO_HOST'];
$porta_reagendamento = $_ENV['DB_REAGENDAMENTO_PORT'];
$banco_reagendamento = $_ENV['DB_REAGENDAMENTO_DATABASE'];
$usuario_reagendamento = $_ENV['DB_REAGENDAMENTO_USERNAME'];
$senha_reagendamento = $_ENV['DB_REAGENDAMENTO_PASSWORD'];

try {
    $dsn_reagendamento = "pgsql:host=$servidor_reagendamento;port=$porta_reagendamento;dbname=$banco_reagendamento";
    $conn_reagendamento = new PDO($dsn_reagendamento, $usuario_reagendamento, $senha_reagendamento);
    $conn_reagendamento->exec("SET NAMES 'utf8'");
} catch (PDOException $e) {
    die($e->getMessage());
}



function conDBIntranet()
{

    $servidor_intranet = $_ENV['DB_INTRANET_HOST'];
    $porta_intranet = $_ENV['DB_INTRANET_PORT'];
    $banco_intranet = $_ENV['DB_INTRANET_DATABASE'];
    $usuario_intranet = $_ENV['DB_INTRANET_USERNAME'];
    $senha_intranet = $_ENV['DB_INTRANET_PASSWORD'];
    // Criar conexão
    try {
        return new mysqli($$servidor_intranet, $usuario_intranet, $senha_intranet, $banco_intranet);
    } catch (Exception $e) {
        die('Erro ao conectar no banco de dados: ' . $e->getMessage());
    }
}
