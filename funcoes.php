<?php


//Função para validar emaisl
require_once __DIR__ . '/conexao.php';
function validarEmails($emailString) {
    // Remove espaços em branco antes e depois
    $emailString = trim($emailString);

    // Divide a string pelos ";"
    $emails = explode(';', $emailString);

    // Verifica se cada email é válido
    foreach ($emails as $email) {
        $email = trim($email); // Remove espaços extras
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false; // Se algum email for inválido, retorna falso
        }
    }

    return true; // Todos os emails são válidos
}


/**
 * Valida o token e verifica se o usuário tem acesso à API especificada
 *
 * @param string $token O token de autenticação fornecido
 * @param string $apiPath O caminho da API que o usuário está tentando acessar
 * @param PDO $pdo Conexão ao banco de dados
 * @return bool Retorna true se o token for válido e tiver acesso, false caso contrário
 */
function validarTokenEAcesso($token, $apiPath, $conn_api) {
    // Verifica se o token está ativo e não expirado
    $sql = "SELECT t.token_id, t.user_id 
            FROM api_tokens t 
            WHERE t.token = :token 
            AND t.is_active = true 
            AND (t.expires_at IS NULL OR t.expires_at > NOW())";
    
    $stmt = $conn_api->prepare($sql);
    $stmt->bindParam(':token', $token, PDO::PARAM_STR);
    $stmt->execute();
    $tokenData = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$tokenData) {
        return false; // Token inválido ou expirado
    }

    // Verifica se o token tem permissão para acessar a API especificada
    $sql = "SELECT a.api_id 
            FROM api_apis a 
            INNER JOIN api_token_access ta ON a.api_id = ta.api_id
            WHERE a.api_path = :api_path 
            AND ta.token_id = :token_id 
            AND ta.access_granted = true";

    $stmt = $conn_api->prepare($sql);
    $stmt->bindParam(':api_path', $apiPath, PDO::PARAM_STR);
    $stmt->bindParam(':token_id', $tokenData['token_id'], PDO::PARAM_INT);
    $stmt->execute();
    $apiAccess = $stmt->fetch(PDO::FETCH_ASSOC);
    return $apiAccess ? true : false;
}

function logApiRequest($conn_api, $token, $endpoint, $params, $client_ip) {
        $params = json_encode($params);

        try {
            // Ativar o modo de erros para lançar exceções
           // $conn_api->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

           $sql = "INSERT INTO public.api_logs (token, api_endpoint, request_params, client_ip)
                   VALUES (:token, :endpoint, :params, :client_ip)";
           //$sql = "SELECT * FROM public.api_logs ORDER BY id DESC LIMIT 100";
        
            // Preparar a declaração
            $stmt = $conn_api->prepare($sql);

            // Vincular os parâmetros
            $stmt->bindParam(':token', $token);
            $stmt->bindParam(':endpoint', $endpoint);
            $stmt->bindParam(':params', $params);
            $stmt->bindParam(':client_ip', $client_ip);

            $stmt->execute();
         
           
            
    } catch (PDOException $e) {
        // Tratar erros de conexão ou inserção no banco
        error_log('Erro ao registrar log da API: ' . $e->getMessage());
    }
}



?>
