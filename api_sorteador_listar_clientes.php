<?php
include_once "conexao.php";
include_once "funcoes.php";

$token = $_GET["token"];
$pi = $_GET["pi"];
$pf = $_GET["pf"];


$DB_HOST="192.168.167.38";
$DB_PORT="5432";
$DB_USER="user_sortedor";
$DB_PASSWORD="0yC-:;2_%B4";
$DB_NAME="bd_roleta";


// Permitir que qualquer origem acesse este recurso
header("Access-Control-Allow-Origin: *");
//Formatar em JSON
// Cabeçalho para JSON
header('Content-Type: application/json');



// Simulação de um endpoint de API recebendo um token no cabeçalho e o caminho de API
$token = isset($token) ? $token : null;
$apiPath = $_SERVER['REQUEST_URI'];  // Caminho da API que está sendo acessada

// Verifica se o token foi fornecido
if (!$token) {
    http_response_code(401); // Unauthorized
    echo json_encode(['error' => 'Token não fornecido']);
    exit;
}

// Valida o token e o acesso à API
if (validarTokenEAcesso($token, $apiPath, $conn_api)) {
    // Se o token for válido e o usuário tiver acesso à API
    echo json_encode(['success' => 'Acesso permitido']);
} else {
    // Se o token for inválido ou o usuário não tiver acesso à API
    http_response_code(403); // Forbidden
    echo json_encode(['error' => 'Acesso negado']);
}



 if ( $token == '53w53WhGHHH124gfFdd13c' AND $pi != null){

include_once "conexao.php";

//$sql = "SELECT '12345678909' AS cpf_cnpj, '987654321' AS contrato, 'Elias Knebel' AS nome, '123' as hash; ";

$sql = "SELECT
    COALESCE(p.cpf, p.cnpj) AS Documento,
    p.nome_razaosocial AS Cliente,
    'http://192.168.16.18/Sulnet/Sulnet/Roleta_Sulnet/Roleta_Sulnet/index.html?hash=' || 
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
                 
       

    }else {
    
            //aviso de erro
            echo json_encode(['status' => 404, 'autenticacao' => "falhou"]);   

    }

?>
