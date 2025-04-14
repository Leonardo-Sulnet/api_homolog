<?php

require_once __DIR__ . '/vendor/autoload.php';

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

$headers = getallheaders();


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
    http_response_code(401); // Unauthorized
    echo json_encode(['error' => 'Token não fornecido']);
    exit;
}


//if (validarTokenEAcesso($token, $apiPath, $conn_api)) {

    if (!isset($_GET["equipe"], $_GET["numero_os"], $_GET["setor"])) {
        echo json_encode(['status' => 400, 'error' => 'Campos obrigatórios não enviados']);
        exit;
    }

    $equipe = trim($_GET["equipe"]);
    $numero_os = trim($_GET["numero_os"]);
    $setor = trim($_GET["setor"]);


    if (empty($equipe) || empty($numero_os) || empty($setor)) { 
       echo json_encode(['status' => 400, 'error' => 'Campos obrigatórios vazios']);
        exit;
    }

<<<<<<< HEAD

    echo "<h3>Dados Inseridos:</h3>";
    echo "<p><strong>API ID:</strong> $api_id</p>";
    echo "<p><strong>API Name:</strong> $api_name</p>";
    echo "<p><strong>API Path:</strong> $api_path</p>";
    echo "<p><strong>Description:</strong> $description</p>";


/*
    $insertQuery = "INSERT INTO * () VALUES ()";
    $statement = $pdo->prepare($insertQuery);
    $statement->bindValue(1, $sql);
=======
echo json_encode([
    'equipe' => $equipe,
    'numero_os' => $numero_os,
    'setor' => $setor
]);
>>>>>>> 26e7738 (ajustes)


