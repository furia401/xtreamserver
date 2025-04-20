<?php
// Conecta-se ao banco de dados de usuários
require_once('./api/controles/db.php');
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
        //$type = $pathParts[0]; // Tipo fixo "live" conforme o que foi solicitado
        $usuario = $pathParts[1];
        $senha = $pathParts[2];
        $arquivo = pathinfo($pathParts[3], PATHINFO_FILENAME);
    }elseif (count($pathParts) <= 3) {
        $domain = $parsedUrl['host'];
        $usuario = $pathParts[0];
        $senha = $pathParts[1];
        $arquivo = pathinfo($pathParts[2], PATHINFO_FILENAME);
    } else {
        return false; // Caso a URL não tenha a estrutura esperada
    }

     // Se $arquivo for vazio ou não for um número, retorna false
    if (empty($arquivo) || !is_numeric($arquivo)) {
        return false;
    }

    return [
        'dominio' => $domain,
        'usuario' => $usuario,
        'senha' => $senha,
        'arquivo' => $arquivo,
    ];
}
function getHeadersAsJson($url) {
    // Configuração do cabeçalho User-Agent
    $options = [
        'http' => [
            'method' => 'GET',
            'header' => "User-Agent: XCIPTV\r\n"
        ]
    ];

    // Criação do contexto de transmissão
    $context = stream_context_create($options);

    // Obtendo os cabeçalhos da URL usando o contexto de transmissão
    $headers = @get_headers($url, 1, $context);

    // Verifica se foi possível obter os cabeçalhos
    if ($headers === false) {
        return json_encode(["error" => "Não foi possível obter os cabeçalhos"], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }

    // Verifica se existe o cabeçalho Location
    if (isset($headers['Location'])) {
        $locations = is_array($headers['Location']) ? $headers['Location'] : [$headers['Location']];

        // Filtra URLs que contêm "token="
        $urlsComToken = array_filter($locations, function($location) {
            return strpos($location, 'token=') !== false;
        });

        // Se encontrou URLs com "token=", retorna essas URLs
        if (!empty($urlsComToken)) {
            return json_encode(["URLsComToken" => array_values($urlsComToken)], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        } 
        
        // Caso contrário, retorna o valor de Location original
        return json_encode(["Location" => $locations], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }

    // Caso não tenha o cabeçalho Location, retorna todos os cabeçalhos
    return json_encode($headers, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
}
function getLocationFromURL($url) {
    $visitedUrls = []; // Array para evitar loops de redirecionamento
    $maxRedirects = 10; // Limite de redirecionamentos para evitar loops infinitos
    $redirectCount = 0;

    while ($redirectCount < $maxRedirects) {
        // Configuração do cabeçalho User-Agent
        $options = [
            'http' => [
                'method' => 'GET',
                'header' => "User-Agent: XCIPTV\r\n"
            ]
        ];

        // Criação do contexto de transmissão
        $context = stream_context_create($options);

        // Obtendo os cabeçalhos da URL usando o contexto de transmissão
        $headers = @get_headers($url, 1, $context);

        // Se não conseguiu obter os cabeçalhos, retorna erro
        if (!$headers) {
            return false;
        }

        // Verificar status HTTP para erros comuns
        if (isset($headers[0])) {
            if (strpos($headers[0], "401 Unauthorized") !== false || 
                strpos($headers[0], "404 Not Found") !== false) {
                return false;
            }
        }

        // Verifica se há um cabeçalho de redirecionamento (Location)
        if (isset($headers["Location"])) {
            $location = is_array($headers["Location"]) ? end($headers["Location"]) : $headers["Location"];

            // Evitar redirecionamento infinito
            if (in_array($location, $visitedUrls)) {
                return false;
            }

            // Salvar URL visitada
            $visitedUrls[] = $location;

            // Se a URL contém "token", retornamos essa URL
            if (strpos($location, "token") !== false) {
                return trim($location);
            }

            // Atualiza a URL para seguir o redirecionamento
            $url = $location;
            $redirectCount++;
        } else {
            // Se não houver mais redirecionamentos e ainda não encontramos um token, retorna falso
            return false;
        }
    }

    // Se atingir o limite de redirecionamentos sem encontrar "token", retorna falso
    return false;
}
// Obter o agente do usuário
$userAgent = $_SERVER['HTTP_USER_AGENT'];

$type_url = $_GET['type_url'] ?? 'series';
$username = $_GET['usuario'] ?? null;
$password = $_GET['senha'] ?? null;
$arquivo = $_GET['arquivo'] ?? null;


$arquivo_sem_extensao = pathinfo($arquivo, PATHINFO_FILENAME);
$extensao = pathinfo($arquivo, PATHINFO_EXTENSION);

if (!$username || !$password) {
    http_response_code(401); 
   $errorResponse['user_info'] = array();
   $errorResponse['user_info']['auth'] = 0;
   $errorResponse['user_info']['msg'] = "username e password necessario!";
    echo json_encode($errorResponse);
    exit();
}

$conexao = conectar_bd();
$query = "SELECT *
                    FROM clientes
                    WHERE usuario = :username AND senha = :password";
$statement = $conexao->prepare($query);
$statement->bindValue(':username', $username);
$statement->bindValue(':password', $password);
$statement->execute();
$result = $statement->fetch(PDO::FETCH_ASSOC);
if (!$result) {
    http_response_code(401); 
    $errorResponse = json_encode(["user_info" => ["auth" => 0]]);
    echo $errorResponse;
    exit();
}

// Verifica se a data de vencimento é maior do que a data atual
$dataAtual = new DateTime();
if (new DateTime($result['Vencimento']) < $dataAtual) {
    $vencido = "http://" . $_SERVER['HTTP_HOST'] . "/video/block.mp4";
    header("Location: $vencido");
    exit();
}


$query_streams = "SELECT * FROM series_episodes WHERE id = :id";
$stmt_streams = $conexao->prepare($query_streams);
$stmt_streams->bindParam(':id', $arquivo_sem_extensao, PDO::PARAM_STR);
$stmt_streams->execute();
$resultado_streams = $stmt_streams->fetch(PDO::FETCH_ASSOC);


if (empty($resultado_streams)) {
    $vod_nao_encontrado = "http://" . $_SERVER['HTTP_HOST'] . "/video/vod_nao_encontrado.mp4";
    //echo "$vod_nao_encontrado";
    header("Location: $vod_nao_encontrado");
    exit();
}

$location = $resultado_streams['link'];
$tipo_link = $resultado_streams['tipo_link'];

if ($tipo_link == 'link_direto') {
    header("Location: ".$location);
    exit;
}

$dados = @processarUrl($location);

if ($location && $dados && $tipo_link !== 'link_direto2') {
    $url = "http://{$dados['dominio']}/{$type_url}/{$dados['usuario']}/{$dados['senha']}/{$dados['arquivo']}.$extensao";
    //echo "link 0: ".$url;
    if ($tipo_link == 'padrao2') {
        header("Location: ".$location);
        exit;
    }
    //header("Location: ".$url);
    //exit;
    //$location2 = @getLocationFromURL($url);
    $location2 = json_decode(getHeadersAsJson($url), true);
    if ($location2) {
        if (isset($location2['location'])) {
            //echo json_encode($location2['location'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            header("url: 1 location");
            if (is_array($location2['location'])) {
                echo "link location: ".$location2['location'][0];
                header("Location: ".$location2['location'][0]);
            }else{
                echo "link location: ".$location2['location'];
                header("Location: ".$location2['location']);
            }
            //header("Location: ".$location2['location']);
            exit;
        }
        if (isset($location2['URLsComToken'])) {
            //echo json_encode($location2['URLsComToken'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            header("url: 2 URLsComToken");
            //header("Location: ".$location2['URLsComToken'][0]);
            if (is_array($location2['URLsComToken'])) {
                echo "link location: ".$location2['URLsComToken'][0];
                header("Location: ".$location2['URLsComToken'][0]);
                exit;
            }else{
                echo "link location 2: ".$location2['URLsComToken'];
                header("Location: ".$location2['URLsComToken']);
            }
        }
        //echo "link 1: ".$location;
        //echo "link 1: ".$location2;
        //header("Location: ".$location2);
        //exit;
    }
    //echo json_encode($location2, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    header("url: 3 link original");
    echo "link 2: ".$url;
    header("Location: $url");
    exit;
}



$location3 = json_decode(getHeadersAsJson($location), true);
    
if ($location3) {
    if (isset($location3['location'])) {
        //echo json_encode($location3['location'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        header("url: 1 link direto location");
        if (is_array($location3['location'])) {
            echo "link location: ".$location3['location'][0];
            header("Location: ".$location3['location'][0]);
        }else{
            echo "link location: ".$location3['location'];
            header("Location: ".$location3['location']);
        }
        //header("Location: ".$location3['location']);
        exit;
    }
    if (isset($location3['URLsComToken'])) {
        //echo json_encode($location3['URLsComToken'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        header("url: 2 link direto URLsComToken");
        //header("Location: ".$location3['URLsComToken'][0]);
        if (is_array($location3['URLsComToken'])) {
            echo "link location: ".$location3['URLsComToken'][0];
            header("Location: ".$location3['URLsComToken'][0]);
            exit;
        }else{
            echo "link location 2: ".$location3['URLsComToken'];
            header("Location: ".$location3['URLsComToken']);
        }
    }
    //echo "link 1: ".$location;
    echo "link 1: ".$location3;
    //header("Location: ".$location3);
    //exit;
}
header("url: 3 link direto original");
echo "link 4: ".$location;
header("Location: $location");
exit;