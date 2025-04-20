<?php
header('Content-Type: text/plain; charset=UTF-8');  // Resposta em texto, mas com conteúdo JSON
header('Access-Control-Allow-Origin: *'); // Permite requisições de qualquer origem


// Conecta-se ao banco de dados de usuários
require_once($_SERVER['DOCUMENT_ROOT'] . '/api/controles/db.php');

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

// Processa informações do usuário
$exp_date = strtotime($result['Vencimento']);
$created_at = strtotime($result['Criado_em']);
$status = ($exp_date < time()) ? "Inactive" : "Active";
$auth = ($status === "Active") ? "1" : "0";

// Informações do usuário
$user_info = [
    "username" => $result['usuario'],
    "password" => $result['senha'],
    "auth" => $auth,
    "status" => $status,
    "exp_date" => "$exp_date",
    "is_trial" => "{$result['is_trial']}",
    "active_cons" => "1",
    "created_at" => "$created_at",
    "max_connections" => "{$result['conexoes']}",
    "allowed_output_formats" => ["m3u8", "ts", "rtmp"]
];

// Informações do servidor
$server_info = [
    "url" => $_SERVER['HTTP_HOST'],
    "port" => $_SERVER['SERVER_PORT'],
    "https_port" => "443",
    "server_protocol" => $_SERVER['REQUEST_SCHEME']
];

// Obtenção das categorias
$categorias = ["movie" => [], "live" => [], "series" => []];
$tipos = ["movie" => "get_vod_categories", "live" => "get_live_categories", "series" => "get_series_categories"];
$mapa_categorias = []; // Mapeia category_id -> category_name

foreach ($tipos as $tipo => $acao) {
    $query = $conexao->prepare("SELECT * FROM categoria WHERE type = :type");
    $query->bindValue(":type", $tipo);
    $query->execute();

    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
        $categorias[$tipo][] = [
            "category_id" => (string)$row["id"],
            "category_name" => $row["nome"],
            "parent_id" => $row["parent_id"]
        ];
        // Mapeia o ID da categoria para o nome
        $mapa_categorias[$row["id"]] = $row["nome"];
    }
}

// Obtenção dos streams
$streams = [];
$num = 0;
$query = $conexao->query("SELECT * FROM streams");
while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
     // Correção de caracteres especiais
    $stream_name = htmlspecialchars_decode($row["name"], ENT_QUOTES);
    $stream_name = mb_convert_encoding($stream_name, 'UTF-8', 'ISO-8859-1');

    $category_name = isset($mapa_categorias[$row["category_id"]]) ? $mapa_categorias[$row["category_id"]] : "Sem Categoria";
    $num++;
    $streams[$row["id"]] = [
        "num" => $num,
        "name" => $row["name"],
        "stream_type" => $row["stream_type"],
        "type_name" => ucfirst($row["stream_type"]) . " Streams",
        "stream_id" => $row["id"],
        "stream_icon" => $row["stream_icon"],
        "epg_channel_id" => null,
        "added" => $row["added"],
        "category_name" => $category_name, // Corrigido para pegar do array de categorias
        "category_id" => $row["category_id"],
        "series_no" => null,
        "live" => ($row["stream_type"] == "live") ? "1" : "0",
        "container_extension" => ($row["stream_type"] == "live") ? null : "mp4",
        "custom_sid" => "",
        "tv_archive" => 0,
        "direct_source" => "",
        "tv_archive_duration" => 0
    ];
}

// Monta o JSON final
$response = [
    "user_info" => $user_info,
    "server_info" => $server_info,
    "categories" => $categorias,
    "available_channels" => $streams
];

// Retorna o JSON formatado
echo json_encode($response, JSON_PRETTY_PRINT);
exit();
?>
