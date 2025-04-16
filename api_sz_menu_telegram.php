<?php

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


logApiRequest($conn_api, $token, $apiPath, $params, $ipClient); //Armazena o log do uso


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


if (!isset($_GET['protocolo'], $_GET['opcao_menu'], $_GET['agente'],$_GET['date'], $_GET['entrada_dados'], $_GET['contato'], $_GET['canal'])) {
    echo json_encode(['status' => 400, 'error' => 'Campos obrigatórios não enviados']);
    exit;
}

$protocolo = trim($_GET['protocolo']);
$opcao_menu = trim($_GET['opcao_menu']);
$agente = trim($_GET['agente']);
$date = trim($_GET['date']);
$entrada_dados = trim($_GET['entrada_dados']);
$contato = trim($_GET['contato']);
$canal = trim($_GET['canal']);


if (empty($protocolo) || empty($opcao_menu) || empty($agente) || empty($date) || empty($entrada_dados) || empty($contato)|| empty($canal)) {
    echo json_encode(['status' => 400, 'error' => 'Campos obrigatórios vazios']);
    exit;
}

$conn_reagendamento->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$insertQuery = 'INSERT INTO reagendamento (protocolo, opcao_menu, agente, date, entrada_dados, contato, canal) VALUES (?,?,?,?,?,?,?)';

$statement = $conn_reagendamento->prepare($insertQuery);
$statement->bindValue(1, $protocolo);
$statement->bindValue(2, $opcao_menu);
$statement->bindValue(3, $agente);
$statement->bindValue(4, $date);
$statement->bindValue(5, $entrada_dados);
$statement->bindValue(6, $contato);
$statement->bindValue(7, $canal);

if ($statement->execute()) {
   echo json_encode(['status' => 200, 'Execute' => true]);
} else {
   echo json_encode(['status' => 200, 'Execute' => false]);
}
