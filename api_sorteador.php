<?php
include_once "conexao.php";
include_once "funcoes.php";


//$token = $_GET["token"];


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
//$user_id = 1;  // Exemplo de ID de usuário autenticado
$params = $_GET;  // Parâmetros da requisição (GET) ou $_POST para POST requests
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
 // echo json_encode(['success' => 'Acesso permitido']);

 $hash = $_GET["hash"];


 $sql = "SELECT
 REPLACE(REPLACE(REPLACE(REPLACE(COALESCE(cpf, cnpj), '.', ''), '-', ''), '/', ''), ' ', '') AS cpf_cnpj,
 nome_razaosocial AS nome,
 md5(
   replace(replace(replace(
     COALESCE(cpf, cnpj),
     '.', ''), '-', ''), ' ', '')
 ) || contratos_ativos.codcontrato::text AS hash,
 contratos_ativos.codcontrato AS contrato
FROM
 mk_pessoas
LEFT JOIN (
 SELECT
   mk_contratos.cliente,
   mk_contratos.codcontrato,
   mk_planos_acesso.codplano,
   MAX(
     CASE
       WHEN mk_contratos.cancelado = 'N' AND (mk_contratos.suspenso = 'N' OR mk_contratos.suspenso IS NULL) THEN 1
       ELSE 0
     END
   ) AS contrato_ativo,
   MAX(mk_contratos.dt_ativacao) AS dt_ativacao,
   MAX(mk_planos_acesso.descricao) AS descricao_plano
 FROM
   mk_contratos
 LEFT JOIN mk_planos_acesso ON mk_planos_acesso.codplano = mk_contratos.plano_acesso
 GROUP BY
   mk_contratos.cliente, mk_contratos.codcontrato, mk_planos_acesso.codplano
 HAVING
   MAX(
     CASE
       WHEN mk_contratos.cancelado = 'N' AND (mk_contratos.suspenso = 'N' OR mk_contratos.suspenso IS NULL) THEN 1
       ELSE 0
     END
   ) = 1
) AS contratos_ativos ON mk_pessoas.codpessoa = contratos_ativos.cliente
WHERE
 
contratos_ativos.contrato_ativo = 1
 AND contratos_ativos.codplano IN (833, 1322, 1330, 1456, 1458, 1467, 1457, 1468, 1469, 1492, 1501)

 AND md5(
   replace(replace(replace(
     COALESCE(cpf, cnpj),
     '.', ''), '-', ''), ' ', '')
 ) || contratos_ativos.codcontrato::text = '".$hash."' 
";


$result = $conn->prepare($sql);
$result->execute();

                       if (($result) and ($result->rowCount() != 0) ){

                               //echo "Cliente encontrado<br>";
                              

                               while ($row = $result->fetch(PDO::FETCH_ASSOC)){
                                  // $emparray[] = mb_convert_encoding($row, 'UTF-8', 'ISO-8859-1');
                                  $emparray[] = $row;


                               }
                               echo json_encode(['status' => 200, 'hash_status' => 1, 'cliente' => $emparray],JSON_UNESCAPED_UNICODE);
                               //echo json_encode(['status' => 200, 'hash_status' => 1, 'cpf_cnpj' => $cpf_cnpj, 'contrato' => $contrato,'nome' => $nome, 'hash' => $hash ]);
                              // echo json_encode($emparray, true);
                               
                       }else{

                              // echo "Nenhum cliente encontrado<br>";
                               echo json_encode(['status' => 200, 'hash_status' => 0]);
                       }       
                
      





} else {
  // Se o token for inválido ou o usuário não tiver acesso à API
  http_response_code(403); // Forbidden
  echo json_encode(['error' => 'Acesso negado']);
  echo json_encode(['path' => $apiPath]);
}







?>
