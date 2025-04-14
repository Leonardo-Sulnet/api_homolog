<?php

require_once __DIR__ . '/vendor/autoload.php';

$token = $_GET["token"];
$cpf_cnpj = $_GET["cpf_cnpj"];


 if ( $token == '53w53WhGHHH124gfFdd13c' AND $cpf_cnpj != null){

$sql = "
SELECT 

CODCONEXAO as CODIGO_CONEXAO,
CONCAT(
    REPLACE(REPLACE(REPLACE(mk_pessoas.cpf, '.', ''), '-', ''), '/', ''),
    REPLACE(REPLACE(REPLACE(mk_pessoas.cnpj, '.', ''), '-', ''), '/', '')
) AS CPF_CNPJ,
    CASE np.RETORNOU
        WHEN 'N' THEN 1
        ELSE 0
    END AS NOTIFICAO_PARADA,
    mk_conexoes.cep AS CEP,
    mk_cidades.cidade AS CIDADE,
    mk_bairros.bairro AS BAIRRO,
    mk_logradouros.logradouro AS LOGRADOURO, 
    mk_conexoes.numero AS NUMERO,
    CASE
        WHEN NP.DT_HR_PREV_RETORNO IS NULL THEN 'Previsão não informada'
        ELSE TO_CHAR(NP.DT_HR_PREV_RETORNO, 'DD-MM-YYYY HH24:MI:SS')
    END AS PREVISAO_RETORNO

FROM
    mk_notificacao_parada_conexoes npc
    LEFT JOIN mk_notificacao_parada np ON npc.cd_parada = np.codnotifparada
    LEFT JOIN mk_conexoes ON mk_conexoes.codconexao = npc.cd_conexao
    LEFT JOIN mk_pessoas ON mk_pessoas.CODPESSOA = mk_conexoes.CODCLIENTE
    LEFT JOIN mk_cep ON mk_cep.cep = mk_conexoes.cep
    LEFT JOIN mk_cidades ON mk_cidades.codcidade = mk_conexoes.cidade AND mk_cidades.codestado = mk_conexoes.uf
    LEFT JOIN mk_bairros ON mk_bairros.codbairro = mk_conexoes.bairro AND mk_bairros.codcidade = mk_conexoes.cidade
    LEFT JOIN mk_logradouros ON mk_logradouros.codlogradouro = mk_conexoes.logradouro
    LEFT JOIN mk_estados ON mk_estados.codestado = mk_conexoes.uf
	
	WHERE 
    np.RETORNOU = 'N'
       AND (
        REPLACE(REPLACE(REPLACE(mk_pessoas.cpf, '.', ''), '-', ''), '/', '') = REPLACE(REPLACE(REPLACE('".$cpf_cnpj."', '.', ''), '-', ''), '/', '')
        OR
        REPLACE(REPLACE(REPLACE(mk_pessoas.cnpj, '.', ''), '-', ''), '/', '') = REPLACE(REPLACE(REPLACE('".$cpf_cnpj."', '.', ''), '-', ''), '/', '')
    );
";

$result = $conn_mk->prepare($sql);
$result->execute();

                        if (($result) and ($result->rowCount() != 0) ){
                                while ($row = $result->fetch(PDO::FETCH_ASSOC)){
                                   // $emparray[] = mb_convert_encoding($row, 'UTF-8', 'ISO-8859-1');
                                   $emparray[] = $row;
                                }
                                echo json_encode(['status' => 200, 'status_notificacao' => 1, 'cliente' => $cpf_cnpj, 'conexoes_afetadas' => $emparray],JSON_UNESCAPED_UNICODE);
                                                          
                        }else{
                            echo json_encode(['status' => 200, 'status_notificacao' => 0]);
                        }       
                 
       

    }else {

            echo json_encode(['status' => 404, 'autenticacao' => "falhou"]);   

    }

?>
