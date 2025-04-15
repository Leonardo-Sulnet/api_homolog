<?php

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/conexao.php';
require_once __DIR__ . '/funcoes.php';

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

$headers = getallheaders();

if (isset($headers['Authorization'])) {
    $token = $headers['Authorization'];

} else if (isset($headers['authorization'])) {
    $token = $headers['authorization'];
}

$apiPath = !empty($_SERVER['PHP_SELF']) ? basename($_SERVER['PHP_SELF']) : null;

$ipClient = $_SERVER['REMOTE_ADDR'];

$params = $_GET; // Armazenando as informações do SZ.Chat


//logApiRequest($conn_api, $token, $apiPath, $params, $ipClient); //Armazena o log do uso


if (!$token) {
    http_response_code(401); 
    echo json_encode(['error' => 'Token não fornecido']);
    exit;
}


if (!validarTokenEAcesso($token, $apiPath, $conn_api)) { 
    http_response_code(403);
    echo json_encode(['error' => 'Acesso negado']);
  //  echo json_encode(['path' => $apiPath]); // opcional para debug
   exit;
}


if (!isset($_GET['equipe'], $_GET['numero_os'], $_GET['setor'])) {
    echo json_encode(['status' => 400, 'error' => 'Campos obrigatórios não enviados']);
    exit;
}

$equipe = trim($_GET['equipe']);
$numero_os = trim($_GET['numero_os']);
$setor = trim($_GET['setor']);

date_default_timezone_set('America/Sao_Paulo');
$data = date('Y-m-d H:i:s');


if (empty($equipe) || empty($numero_os) || empty($setor)) {
    echo json_encode(['status' => 400, 'error' => 'Campos obrigatórios vazios']);
    exit;
}

    $conn_reagendamento->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $insertQuery = 'INSERT INTO reagendamento (equipe, numero_os,data_insert,id_setor) VALUES (?,?,?,?)';
    $statement = $conn_reagendamento->prepare($insertQuery);
    $statement->bindValue(1, $equipe);
    $statement->bindValue(2, $numero_os);
    $statement->bindValue(3, $data);
    $statement->bindValue(4, $setor);

    if ($statement->execute()) {
        echo json_encode(['status' => 200, 'Execute' => true]);
    } else {
        echo json_encode(['status' => 200, 'Execute' => false]);
   }

