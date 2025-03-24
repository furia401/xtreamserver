<?php

require_once "./api/controles/db.php";
function processarUrl($url)
{
    $parsedUrl = parse_url($url);

    $path = $parsedUrl["path"];

    $pathParts = explode("/", trim($path, "/"));

    if (count($pathParts) >= 4) {
        $domain = $parsedUrl["host"];
        //$type = $pathParts[0];
        $usuario = $pathParts[1];
        $senha = $pathParts[2];
        $arquivo = pathinfo($pathParts[3], PATHINFO_FILENAME);
    } elseif (count($pathParts) <= 3) {
        $domain = $parsedUrl["host"];
        $usuario = $pathParts[0];
        $senha = $pathParts[1];
        $arquivo = pathinfo($pathParts[2], PATHINFO_FILENAME);
    } else {
        return false;
    }

    if (empty($arquivo) || !is_numeric($arquivo)) {
        return false;
    }

    return [
        "dominio" => $domain,
        "usuario" => $usuario,
        "senha" => $senha,
        "arquivo" => $arquivo,
    ];
}
function getHeadersAsJson($url)
{
    $options = [
        "http" => [
            "method" => "GET",
            "header" => "User-Agent: XCIPTV\r\n",
        ],
    ];

    $context = stream_context_create($options);

    $headers = @get_headers($url, 1, $context);

    if ($headers === false) {
        return json_encode(
            ["error" => "Não foi possível obter os cabeçalhos"],
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
        );
    }

    if (isset($headers["Location"])) {
        $locations = is_array($headers["Location"])
            ? $headers["Location"]
            : [$headers["Location"]];

        $urlsComToken = array_filter($locations, function ($location) {
            return strpos($location, "token=") !== false;
        });

        if (!empty($urlsComToken)) {
            return json_encode(
                ["URLsComToken" => array_values($urlsComToken)],
                JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
            );
        }

        return json_encode(
            ["Location" => $locations],
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
        );
    }

    return json_encode($headers, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
}
function getLocationFromURL($url)
{
    $visitedUrls = [];
    $maxRedirects = 10;
    $redirectCount = 0;

    while ($redirectCount < $maxRedirects) {
        $options = [
            "http" => [
                "method" => "GET",
                "header" => "User-Agent: XCIPTV\r\n",
            ],
        ];

        $context = stream_context_create($options);

        $headers = @get_headers($url, 1, $context);

        if (!$headers) {
            return false;
        }

        if (isset($headers[0])) {
            if (
                strpos($headers[0], "401 Unauthorized") !== false ||
                strpos($headers[0], "404 Not Found") !== false
            ) {
                return false;
            }
        }

        if (isset($headers["Location"])) {
            $location = is_array($headers["Location"])
                ? end($headers["Location"])
                : $headers["Location"];

            if (in_array($location, $visitedUrls)) {
                return false;
            }

            $visitedUrls[] = $location;

            if (strpos($location, "token") !== false) {
                return trim($location);
            }

            $url = $location;
            $redirectCount++;
        } else {
            return false;
        }
    }

    return false;
}

$userAgent = $_SERVER["HTTP_USER_AGENT"];

$type_url = $_GET["type_url"] ?? "movie";
$username = $_GET["usuario"] ?? null;
$password = $_GET["senha"] ?? null;
$arquivo = $_GET["arquivo"] ?? null;

if ($arquivo) {
    $arquivo_sem_extensao = pathinfo($arquivo, PATHINFO_FILENAME);
    $extensao = pathinfo($arquivo, PATHINFO_EXTENSION);
}

if (!$username || !$password) {
    http_response_code(401);
    $errorResponse["user_info"] = [];
    $errorResponse["user_info"]["auth"] = 0;
    $errorResponse["user_info"]["msg"] = "username e password necessario!";
    echo json_encode($errorResponse);
    exit();
}

$conexao = conectar_bd();
$query = "SELECT *
                    FROM clientes
                    WHERE usuario = :username AND senha = :password";
$statement = $conexao->prepare($query);
$statement->bindValue(":username", $username);
$statement->bindValue(":password", $password);
$statement->execute();
$result = $statement->fetch(PDO::FETCH_ASSOC);
if (!$result) {
    http_response_code(401);
    $errorResponse = json_encode(["user_info" => ["auth" => 0]]);
    echo $errorResponse;
    exit();
}

$dataAtual = new DateTime();
if (new DateTime($result["Vencimento"]) < $dataAtual) {
    $vencido = "http://" . $_SERVER["HTTP_HOST"] . "/video/block.mp4";
    header("Location: $vencido");
    exit();
}

$query_streams = "SELECT * FROM streams WHERE id = :id";
$stmt_streams = $conexao->prepare($query_streams);
$stmt_streams->bindParam(":id", $arquivo_sem_extensao, PDO::PARAM_STR);
$stmt_streams->execute();
$resultado_streams = $stmt_streams->fetch(PDO::FETCH_ASSOC);

if (empty($resultado_streams)) {
    $vod_nao_encontrado =
        "http://" . $_SERVER["HTTP_HOST"] . "/video/vod_nao_encontrado.mp4";
    header("Location: $vod_nao_encontrado");
    exit();
}
$location = $resultado_streams["link"];
$tipo_link = $resultado_streams["tipo_link"];

if ($tipo_link == "link_direto") {
    header("Location: " . $location);
    exit();
}

$dados = @processarUrl($location);

if ($location && $dados && $tipo_link !== "link_direto2") {
    $url = "http://{$dados["dominio"]}/{$type_url}/{$dados["usuario"]}/{$dados["senha"]}/{$dados["arquivo"]}.$extensao";
    if ($tipo_link == "padrao2") {
        header("Location: " . $location);
        exit();
    }
    $location2 = json_decode(getHeadersAsJson($url), true);
    if ($location2) {
        if (isset($location2["location"])) {
            header("url: 1 location");
            if (is_array($location2["location"])) {
                //echo "link location: " . $location2["location"][0];
                header("Location: " . $location2["location"][0]);
            } else {
                //echo "link location: " . $location2["location"];
                header("Location: " . $location2["location"]);
            }
            exit();
        }
        if (isset($location2["URLsComToken"])) {
            header("url: 2 URLsComToken");
            if (is_array($location2["URLsComToken"])) {
                //echo "link location: " . $location2["URLsComToken"][0];
                header("Location: " . $location2["URLsComToken"][0]);
                exit();
            } else {
                //echo "link location 2: " . $location2["URLsComToken"];
                header("Location: " . $location2["URLsComToken"]);
            }
        }
    }
    header("url: 3 link original");
    //echo "link 2: " . $url;
    header("Location: $url");
    exit();
}

$location3 = json_decode(getHeadersAsJson($location), true);

if ($location3) {
    if (isset($location3["location"])) {
        header("url: 1 link direto location");
        if (is_array($location3["location"])) {
            //echo "link location: " . $location3["location"][0];
            header("Location: " . $location3["location"][0]);
        } else {
            //echo "link location: " . $location3["location"];
            header("Location: " . $location3["location"]);
        }
        exit();
    }
    if (isset($location3["URLsComToken"])) {
        header("url: 2 link direto URLsComToken");
        if (is_array($location3["URLsComToken"])) {
            //echo "link location: " . $location3["URLsComToken"][0];
            header("Location: " . $location3["URLsComToken"][0]);
            exit();
        } else {
            //echo "link location 2: " . $location3["URLsComToken"];
            header("Location: " . $location3["URLsComToken"]);
        }
    }
    //echo "link 1: " . $location3;
}
header("url: 3 link direto original");
//echo "link 4: " . $location;
header("Location: $location");
exit();
