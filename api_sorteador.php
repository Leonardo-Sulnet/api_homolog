<?php

//require_once __DIR__ . '/vendor/autoload.php';

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
    COALESCE(p.cpf, p.cnpj) AS documento, 
    p.nome_razaosocial AS cliente, 
    md5(replace(replace(replace(COALESCE(p.cpf, p.cnpj),'.', ''), '-', ''), ' ', '')) || ca.codcontrato::text AS hash,
    ca.codcontrato AS contrato
	FROM
    mk_pessoas p
LEFT JOIN (
    SELECT
        mk_contratos.cliente,
        mk_contratos.codcontrato,
        mk_planos_acesso.codplano,
        mk_contratos.cd_lead,
        mk_contratos.cancelado,
        mk_contratos.suspenso,
        MAX(mk_contratos.adesao) AS adesao,
        MAX(mk_contratos.dt_ativacao) AS dt_ativacao,
        MAX(mk_planos_acesso.descricao) AS descricao_plano
    FROM
        mk_contratos
    LEFT JOIN mk_planos_acesso ON mk_planos_acesso.codplano = mk_contratos.plano_acesso
    GROUP BY
        mk_contratos.cliente, mk_contratos.codcontrato, mk_planos_acesso.codplano, mk_contratos.cd_lead, mk_contratos.cancelado, mk_contratos.suspenso
) AS ca ON p.codpessoa = ca.cliente
LEFT JOIN mk_crm_leads on mk_crm_leads.codlead = ca.cd_lead

WHERE
    venda_crm IN (1,2)
    AND ca.codplano IN (833,1322,1330,1492,1501,1462,1458,1457,1456,1468,1467,1469,1501,1492,1330,1368)
    AND (ca.cancelado = 'N' AND (ca.suspenso = 'S' OR ca.suspenso = 'N' OR ca.suspenso IS NULL))
    AND md5(replace(replace(replace(COALESCE(p.cpf, p.cnpj),'.', ''), '-', ''), ' ', '')) || ca.codcontrato::text = '".$hash."' 
";





$result = $conn_mk->prepare($sql);
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
