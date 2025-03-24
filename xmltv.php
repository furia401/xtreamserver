<?php

header("Access-Control-Allow-Origin: *");
// Permite métodos específicos (GET, POST, etc.)
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
// Permite cabeçalhos personalizados (se necessário)
header("Access-Control-Allow-Headers: Content-Type, Authorization");

function processarUrl($url) {
    // Analisa a URL usando parse_url
    $parsedUrl = parse_url($url);
    
    // Divide a URL para obter o caminho
    $path = $parsedUrl['path'];
    
    // Divide o caminho em partes
    $pathParts = explode('/', trim($path, '/'));
    
    // Verifica se há pelo menos 5 partes na URL
    if (count($pathParts) >= 4) {
        $domain = $parsedUrl['host'];
        $type = $pathParts[0]; // Tipo fixo "live" conforme o que foi solicitado
        $usuario = $pathParts[1];
        $senha = $pathParts[2];
        $arquivo = pathinfo($pathParts[3], PATHINFO_FILENAME);
        $extensao = pathinfo($pathParts[3], PATHINFO_EXTENSION);
        
        return [
            'dominio' => $domain,
            'type' => $type,
            'usuario' => $usuario,
            'senha' => $senha,
            'arquivo' => $arquivo,
            'extensao' => $extensao
        ];
    }
    if (count($pathParts) <= 3) {
        $domain = $parsedUrl['host'];
        $type = 'live'; // Tipo fixo "live" conforme o que foi solicitado
        $usuario = $pathParts[0];
        $senha = $pathParts[1];
        $arquivo = pathinfo($pathParts[2], PATHINFO_FILENAME);
        $extensao = "ts";
        
        return [
            'dominio' => $domain,
            'type' => $type,
            'usuario' => $usuario,
            'senha' => $senha,
            'arquivo' => $arquivo,
            'extensao' => $extensao
        ];
    } else {
        return false; // Caso a URL não tenha a estrutura esperada
    }
}
// Função para realizar a requisição com múltiplas tentativas
function baixarEPG($url, $maxTentativas = 5, $tempoEspera = 2) {
    $tentativas = 0;
    $response = "";
    $http_code = 0;

    do {
        $ch = curl_init();

        // Configuração do cURL
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true, // Retorna a resposta como string
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_USERAGENT => "XCIPTV",
            CURLOPT_HEADER => false,
            CURLOPT_TIMEOUT => 20,
        ]);

        $response = curl_exec($ch); // Executa e captura a resposta
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($http_code == 200) {
            return $response; // Retorna a string diretamente
        }

        sleep($tempoEspera);
        $tentativas++;
    } while ($tentativas < $maxTentativas);

    return false;
}


// Conecta-se ao banco de dados de usuários
require_once($_SERVER['DOCUMENT_ROOT'] . '/api/controles/db.php');

if (isset($_GET['epg'])) {
    $conexao = conectar_bd();
    // Consulta SQL para buscar streams
    $query_streams = "SELECT * FROM streams LIMIT 5";
    $stmt_streams = $conexao->prepare($query_streams);
    $stmt_streams->execute();
    $resultado_streams = $stmt_streams->fetchAll(PDO::FETCH_ASSOC);

    $ultimaUrl = ""; // Armazena a última URL processada

    foreach ($resultado_streams as $streams) {
        $dados = processarUrl($streams['link']);
        $url = "http://{$dados['dominio']}/xmltv.php?username={$dados['usuario']}&password={$dados['senha']}";

        // Atualiza a última URL processada
        $ultimaUrl = $url;

        // Faz a solicitação usando a função baixarEPG()
        $response = baixarEPG($url, '5', '2');

        // Se conseguiu obter o XML, força o download
        if ($response !== false) {
            header("Content-Type: application/xml; charset=utf-8");
            header('Content-Disposition: attachment; filename="epg.xml"');
            echo $response;
            exit();
        }
    }
    header("Location: $ultimaUrl");
    exit;
}
date_default_timezone_set('America/Sao_Paulo');
$username = isset($_GET['username']) ? $_GET['username'] : null;
$password = isset($_GET['password']) ? $_GET['password'] : null;

//Autenticação /player_api.php?username=X&password=X
if (!$username || !$password) {
    http_response_code(401); // 401 Unauthorized
   $errorResponse['user_info'] = array();
   $errorResponse['user_info']['auth'] = 0;
   $errorResponse['user_info']['msg'] = "username e password necessario!";
    echo json_encode($errorResponse);
    exit();
}

// Conexão com o banco de dados
$conexao = conectar_bd();

// Busca os dados do usuário
$query = "SELECT * FROM clientes WHERE usuario = :username AND senha = :password";
$statement = $conexao->prepare($query);
$statement->bindValue(':username', $username);
$statement->bindValue(':password', $password);
$statement->execute();
$result = $statement->fetch(PDO::FETCH_ASSOC);

if (!$result) {
    http_response_code(401); // 401 Unauthorized
    echo json_encode([["auth" => 0]]);
    exit();
}



// Consulta SQL para buscar streams
$query_streams = "SELECT * FROM streams LIMIT 5";
$stmt_streams = $conexao->prepare($query_streams);
$stmt_streams->execute();
$resultado_streams = $stmt_streams->fetchAll(PDO::FETCH_ASSOC);

$ultimaUrl = ""; 

foreach ($resultado_streams as $streams) {
    $dados = processarUrl($streams['link']);
    $url = "http://{$dados['dominio']}/xmltv.php?username={$dados['usuario']}&password={$dados['senha']}";


    $ultimaUrl = $url;


    $response = @baixarEPG($url);


    if ($response !== false) {
        header("Content-Type: application/xml; charset=utf-8");
        header('Content-Disposition: attachment; filename="epg.xml"');
        echo $response;
        exit();
        break; // Garante que o loop pare (opcional, pois o exit já encerra o script)
    }
}

header("Location: $ultimaUrl");
/*/ Se nenhuma tentativa conseguiu baixar o EPG, retorna erro em JSON
header("Content-Type: application/json");
echo json_encode([
    "error" => "Conteúdo EPG não encontrado após múltiplas tentativas.",
    "last_url" => $ultimaUrl
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);*/
exit();

