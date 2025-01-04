<?php
include_once "conexao.php";
include_once "funcoes.php";

// Permitir que qualquer origem acesse este recurso
header("Access-Control-Allow-Origin: *");
//Formatar em JSON
// Cabeçalho para JSON
header('Content-Type: application/json');

$headers = getallheaders();
//echo $headers['Authorization'];

// Simulação de um endpoint de API recebendo um token no cabeçalho e o caminho de API
$token = isset($headers['Authorization']) ? $headers['Authorization'] : null;
$apiPath = !empty($_SERVER['PHP_SELF']) ? basename($_SERVER['PHP_SELF']) : null;


// LOG da REQUEST DE API

$params = $_POST;  // Parâmetros da requisição (GET) ou $_POST para POST requests
$client_ip = $_SERVER['REMOTE_ADDR'];

// Registrar a requisição na tabela api_logs
logApiRequest($conn_api,  $token, $apiPath, $params, $client_ip);



// Verifica se o token foi fornecido
if (!$token) {
  http_response_code(401); // Unauthorized
  echo json_encode(['error' => 'Token não fornecido']);
  exit;
}

// Valida o token e o acesso à API
if (validarTokenEAcesso($token, $apiPath, $conn_api)) {
                  // Se o token for válido e o usuário tiver acesso à API
                  //echo json_encode(['success' => 'Acesso permitido']);

                  
                  if (isset($_POST["assunto"], $_POST["mensagem"], $_POST["emails"])) {
                    $assunto = $_POST["assunto"];
                    $mensagem = $_POST["mensagem"];
                    $emails = $_POST["emails"];
                
                    // Verificar se os campos não estão vazios
                    if (!empty($assunto) && !empty($mensagem) && !empty($emails)) {
                       
                          $sql = "INSERT INTO mail_queue
                              ";

                          $result = $conn->prepare($sql);
                          $result->execute();

                                                if (($result) and ($result->rowCount() != 0) ){

                                                        //echo "Cliente encontrado<br>";
                                                          while ($row = $result->fetch(PDO::FETCH_ASSOC)){
                                                            $emparray[] = $row;


                                                        }
                                                        echo json_encode(['status' => 200, 'mail_queued' => true]);
                                                                                      
                                                }else{
                                                        echo json_encode(['status' => 200, 'mail_queued' => false]);
                                                }     
                      } else {
                        echo json_encode(['status' => 200,'error' => 'Campos obrigatorios não preenchidos']);
                        exit;
                      }
                  } else {
                    echo json_encode(['status' => 200,'error' => 'Campos obrigatorios não informados']);
                    exit;
                  }                                 
} else {
  // Se o token for inválido ou o usuário não tiver acesso à API
  http_response_code(403); // Forbidden
  echo json_encode(['error' => 'Acesso negado']);
  echo json_encode(['path' => $apiPath]);
}




?>
