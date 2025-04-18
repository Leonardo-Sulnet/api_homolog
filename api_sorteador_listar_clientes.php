<?php

//require_once __DIR__ . '/vendor/autoload.php';

$DB_HOST=$_ENV['DB_ROLETA_HOST'];
$DB_PORT=$_ENV['DB_ROLETA_PORT'];
$DB_USER=$_ENV['DB_ROLETA_USERNAME'];
$DB_PASSWORD=$_ENV['DB_ROLETA_PASSWORD'];
$DB_NAME=$_ENV['DB_ROLETA_DATABASE'];


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

   $pi = $_GET["pi"];
   $pf = $_GET["pf"];
   

    $sql = "SELECT
    ca.cd_lead AS lead,  
    p.nome_razaosocial AS cliente, 
    COALESCE(p.cpf, p.cnpj) AS documento, 
    ca.codcontrato AS contrato,
   CASE
        WHEN ca.cancelado = 'S' THEN 'Cancelado'
        WHEN ca.cancelado = 'N' AND ca.suspenso = 'S' THEN 'Suspenso'
        WHEN ca.cancelado = 'N' AND (ca.suspenso = 'N' OR ca.suspenso IS NULL) THEN 'Ativo'
        ELSE NULL
    END AS status_contrato,
    'http://sulnet.net.br/sorteador/index.html?hash=' ||
    md5(replace(replace(replace(COALESCE(p.cpf, p.cnpj),'.', ''), '-', ''), ' ', '')) || ca.codcontrato::text AS hash,
    ca.descricao_plano AS descricao_plano,
    r.documento AS documento_ganhador,
    r.contrato AS contrato_ganhador,
    r.nome AS nome_ganhador,
    r.id_premio AS id_premio,
    r.descricao_premio AS premio,
    to_char(ca.adesao,'YYYY-MM-DD') AS adesao,
    to_char(ca.dt_ativacao,'YYYY-MM-DD') AS ativacao,
    TO_CHAR(r.dt_hora_insert, 'YYYY-MM-DD') AS data_insert,
    usr_nome AS vendedor,
    usr_login AS login
    
 

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
LEFT JOIN mk_crm_operadores operadorcrm ON mk_crm_leads.operador_responsavel = operadorcrm.cd_operador
FULL JOIN fr_usuario usuario ON operadorcrm.cd_operador = usuario.usr_codigo
LEFT JOIN (
    SELECT
        id_ganhador,
        nome,
        documento,
        contrato,
        documento_contrato_hash,
        id_premio,
        descricao_premio,
        dt_hora_insert
    FROM
dblink('host=$DB_HOST port=$DB_PORT dbname=$DB_NAME user=$DB_USER password=$DB_PASSWORD', 
            'SELECT id_ganhador, nome, documento, contrato, documento_contrato_hash, ganhador.id_premio, descricao_premio, dt_hora_insert
             FROM ganhador
             LEFT JOIN premios ON premios.id_premio = ganhador.id_premio'
        ) AS roleta_data(id_ganhador INT, nome TEXT, documento TEXT, contrato TEXT, documento_contrato_hash TEXT, id_premio INT, descricao_premio TEXT, dt_hora_insert DATE)
) AS r ON r.contrato = ca.codcontrato
WHERE
    venda_crm IN (1,2)
    AND ca.codplano IN (833, 1322,1330,1492,1501,1462,1458,1457,1456,1468,1467,1469,1501,1492,1330,1368)
    AND DATE_TRUNC('day', ca.adesao) BETWEEN DATE_TRUNC('day', '$pi'::date) AND DATE_TRUNC('day', '$pf'::date)
    AND (ca.cancelado = 'N' AND (ca.suspenso = 'S' OR ca.suspenso = 'N' OR ca.suspenso IS NULL))
ORDER BY
ca.adesao DESC;";

 //   echo $sql;
/*
    $sql = "SELECT
    COALESCE(p.cpf, p.cnpj) AS Documento,
    p.nome_razaosocial AS Cliente,
    'http://sulnet.net.br/sorteador/index.html?hash=' || 
    md5(
        replace(replace(replace(
            COALESCE(p.cpf, p.cnpj),
            '.', ''), '-', ''), ' ', '')
    ) || ca.codcontrato::text AS Hash,
    ca.codcontrato AS Contrato,
    ca.descricao_plano AS Descricao_Plano,
    to_char(ca.dt_ativacao,'YYYY-MM-DD') AS Ativacao,
    CASE
        WHEN ca.contrato_ativo = 1 THEN 'Ativo'
        ELSE 'Não Ativo'
    END AS Status,
    r.documento AS Documento_Ganhador,
    r.contrato AS Contrato_Ganhador,
    r.nome AS Nome_Ganhador,
    r.id_premio AS ID_Premio,
    r.descricao_premio AS Premio
FROM
    mk_pessoas p
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
) AS ca ON p.codpessoa = ca.cliente
LEFT JOIN (
    SELECT 
        id_ganhador, 
        nome, 
        documento, 
        contrato, 
        documento_contrato_hash, 
        id_premio,
        descricao_premio
    FROM 
        dblink('host=$DB_HOST port=$DB_PORT dbname=$DB_NAME user=$DB_USER password=$DB_PASSWORD', 
                'SELECT id_ganhador, nome, documento, contrato, documento_contrato_hash, ganhador.id_premio, descricao_premio
                
                 FROM ganhador  
                 left join premios on premios.id_premio = ganhador.id_premio
                '
                ) 
    AS roleta_data(id_ganhador INT, nome TEXT, documento TEXT, contrato TEXT, documento_contrato_hash TEXT, id_premio INT, descricao_premio TEXT)
) AS r ON r.contrato = ca.codcontrato
WHERE
    ca.contrato_ativo = 1
    AND ca.codplano IN (833, 1322, 1330, 1456, 1458, 1467, 1457, 1468, 1469, 1492, 1501)
    AND DATE_TRUNC('day', ca.dt_ativacao) BETWEEN DATE_TRUNC('day', '$pi'::date) AND DATE_TRUNC('day', '$pf'::date);

";
*/


$result = $conn_mk->prepare($sql);
$result->execute();

                        if (($result) and ($result->rowCount() != 0) ){

                                         

                                while ($row = $result->fetch(PDO::FETCH_ASSOC)){
                                   // $emparray[] = mb_convert_encoding($row, 'UTF-8', 'ISO-8859-1');
                                   $emparray[] = $row;


                                }
                                echo json_encode(['cliente' => $emparray],JSON_UNESCAPED_UNICODE);                             
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
