<?php
include_once "conexao.php";


$token = $_GET["token"];
$hash = $_GET["hash"];


// Permitir que qualquer origem acesse este recurso
header("Access-Control-Allow-Origin: *");
//Formatar em JSON
// Cabeçalho para JSON
header('Content-Type: application/json');

 if ( $token == '53w53WhGHHH124gfFdd13c' AND $hash != null){




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
                 
       

    }else {
    
            //aviso de erro
            echo json_encode(['status' => 404, 'autenticacao' => "falhou"]);   

    }

?>
