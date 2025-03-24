<?php
function gerarCodigoUnico() {
    // Pega o timestamp atual (número de segundos desde 1970)
    $timestamp = time();

    // Extrai os últimos 4 dígitos do timestamp (pode ser ajustado dependendo da precisão)
    $codigo = substr($timestamp, -4);

    // Caso o número extraído tenha menos de 4 dígitos, preenche com zeros à esquerda
    return str_pad($codigo, 4, '0', STR_PAD_LEFT);
}

function buscarSerieTMDB($query) {
    $apiKey = "30888e8f271f1698dcfb0228f589bec1";

    // Remover "[L]" do nome antes da requisição
    $queryLimpa = str_replace("[L]", "", $query);

    $url = "http://api.themoviedb.org/3/search/tv?query=" . urlencode($query) . "&api_key=" . $apiKey . "&language=pt-BR";

    // Inicializa cURL
    $ch = curl_init();

    // Configurações do cURL
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Desativa verificação SSL (caso necessário)

    // Executa a requisição
    $response = curl_exec($ch);

    // Verifica erro na requisição
    if (curl_errno($ch)) {
        return json_encode(['erro' => 'Erro na requisição: ' . curl_error($ch)]);
    }

    // Fecha conexão cURL
    curl_close($ch);

    // Decodifica o JSON para um array associativo
    $dados = json_decode($response, true);

    return json_encode($dados, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}

function buscarDetalhesSerieTMDB($tmdb_id) {
    $apiKey = "30888e8f271f1698dcfb0228f589bec1";
    $url = "https://api.themoviedb.org/3/tv/$tmdb_id?api_key=$apiKey&language=pt-BR&append_to_response=credits,videos";

    // Inicializa cURL
    $ch = curl_init();
    
    // Configurações do cURL
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    // Executa a requisição
    $response = curl_exec($ch);

    // Verifica erro na requisição
    if (curl_errno($ch)) {
        return json_encode(['erro' => 'Erro na requisição: ' . curl_error($ch)]);
    }

    // Fecha conexão cURL
    curl_close($ch);

    // Decodifica o JSON para um array associativo
    $dados = json_decode($response, true);

    return json_encode($dados, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}


// Conecta-se ao banco de dados de usuários
require_once($_SERVER['DOCUMENT_ROOT'] . '/api/controles/db.php');
header('Content-Type: application/json; charset=utf-8');
header('Connection: close');
header('Access-Control-Allow-Credentials: true');
date_default_timezone_set('America/Sao_Paulo');

$userAgent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';

$url = $_SERVER['HTTP_HOST'];
$username = isset($_GET['username']) ? $_GET['username'] : null;
$password = isset($_GET['password']) ? $_GET['password'] : null;
$action = isset($_GET['action']) ? $_GET['action'] : null;
$series_id = isset($_GET['series_id']) ? $_GET['series_id'] : null;
$vod_id = isset($_GET['vod_id']) ? $_GET['vod_id'] : null;
$category_id = isset($_GET['category_id']) ? $_GET['category_id'] : null;
$type = isset($_GET['type']) ? $_GET['type'] : null;

if($category_id === "*"){
    $category_id = null;
}

if(isset($_POST['username']) && isset($_POST['password']) && isset($_POST['action']) && isset($_POST['series_id'])) {
  $username = $_POST['username'];
  $password = $_POST['password'];
  $action = $_POST['action'];
  $series_id = $_POST['series_id'];
  header('Location: player_api.php?username=' . urlencode($username) . '&password=' . urlencode($password) . '&action=' . urlencode($action) . '&series_id=' . urlencode($series_id));
  exit;
}

if(isset($_POST['username']) && isset($_POST['password']) && isset($_POST['action'])) {
  $username = $_POST['username'];
  $password = $_POST['password'];
  $action = $_POST['action'];
  header('Location: player_api.php?username=' . urlencode($username) . '&password=' . urlencode($password) . '&action=' . urlencode($action));
  exit;
}
if(isset($_POST['username']) && isset($_POST['password'])) {
  $username = $_POST['username'];
  $password = $_POST['password'];
  exit;
}

//Autenticação /player_api.php?username=X&password=X
if (!$username || !$password) {
    http_response_code(401); // 401 Unauthorized
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
    http_response_code(401); // 401 Unauthorized
    $errorResponse = json_encode(["user_info" => ["auth" => 0]]);
    echo $errorResponse;
    exit();
}

//Autenticação /player_api.php?username=X&password=X
if (isset($_GET['username']) && isset($_GET['password']) && !isset($_GET['action'])) {

    $exp_date = strtotime($result['Vencimento']);
    $created_at = strtotime($result['Criado_em']);
    $status = "Active";
    $auth = "1";

    if ($exp_date < strtotime(date("Y-m-d H:i:s"))) {
        $status = "Inactive";
        $auth = "0";
    }

    $response = array(
        'user_info' => array(
            'username' => $result['usuario'],
            'password' => $result['senha'],
            'message' => 'BEM-VINDOS AO FURIA XTREAM!',
            'auth' => $auth,
            'status' => $status,
            'exp_date' => "$exp_date",
            'is_trial' => "".$result['is_trial'],
            'active_cons' => 0,
            'created_at' => "$created_at",
            'max_connections' => "".$result['conexoes'],
            'allowed_output_formats' => array('m3u8', 'ts', 'rtmp')
        ),
        'server_info' => array(
            'painel' => 'FURIA XTREAM',
            'version' => '0.0.1',
            'revision' => 1,
            'url' => $_SERVER['HTTP_HOST'],
            'port' => $_SERVER['SERVER_PORT'],
            'https_port' => "443",
            'server_protocol' => $_SERVER['REQUEST_SCHEME'],
            'rtmp_port' => '8880',
            'timestamp_now' => time(),
            'time_now' => date('Y-m-d H:i:s'),
            'timezone' => date_default_timezone_get()
        )
    );
    // retorna a resposta em JSON
    echo json_encode($response);
    exit();
}

//-----GET Categorias de transmissão ao vivo
//player_api.php?username=X&password=X&action=get_live_categories
if (isset($_GET['action']) && in_array($_GET['action'], ['get_live_categories', 'get_vod_categories', 'get_series_categories'])) {
    $action = $_GET['action'];
    $adulto = $result['adulto'];
    $type = '';

    // Define o tipo de categoria baseado na ação
    switch ($action) {
        case 'get_live_categories':
            $type = 'live';
            break;
        case 'get_vod_categories':
            $type = 'movie';
            break;
        case 'get_series_categories':
            $type = 'series';
            break;
    }

    // Prepara a query com ou sem filtro de adulto
    $query_str = "SELECT * FROM categoria WHERE type = :type";
    if ($adulto == 0) {
        $query_str .= " AND is_adult = '0'";
    }

    $query = $conexao->prepare($query_str);
    $query->bindValue(":type", $type);
    $query->execute();

    // Processa os resultados
    $results = [];
    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
        $results[] = [
            "category_id" => (string)$row["id"], // Cast para string
            "category_name" => $row["nome"],
            "parent_id" => $row["parent_id"],
        ];
    }

    // Caso não haja resultados, adiciona uma categoria padrão
    if (empty($results)) {
        $results[] = [
            "category_id" => "1",
            "category_name" => "Sem categorias",
            "parent_id" => 0,
        ];
    }

    // Retorna o JSON
    header('Content-Type: application/json');
    echo json_encode($results);
    exit;
}


//-----GET streams de transmissão ao vivo
//player_api.php?username=X&password=X&action=get_live_streams
if (isset($_GET['action']) && in_array($_GET['action'], ['get_live_streams', 'get_vod_streams', 'get_series'])) {
    $action = $_GET['action'];
    $stream_type = '';
    $table = 'streams';
    $adulto = isset($result['adulto']) ? $result['adulto'] : 0;

    // Define o tipo de stream e a tabela correspondente
    switch ($action) {
        case 'get_live_streams':
            $stream_type = 'live';
            break;
        case 'get_vod_streams':
            $stream_type = 'movie';
            break;
        case 'get_series':
            $stream_type = 'series';
            $table = 'series'; // Usamos a tabela de séries.
            break;
    }
    $codigo_unico = gerarCodigoUnico();

    // Prepara a query com ou sem filtro de adulto
    $query_str = "SELECT * FROM $table WHERE stream_type = :stream_type";
    if ($adulto == 0) {
        $query_str .= " AND is_adult = '0'";
    }

    $query = $conexao->prepare($query_str);
    $query->bindValue(":stream_type", $stream_type);
    $query->execute();

    // Processa os resultados
    $num = 0;
    $results = [];
    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
        $num++;
        // Monta os resultados com base no tipo de ação
        if ($action == 'get_live_streams') {
            $results[] = [
                "num" => $num,
                "name" => nl2br($row["name"]),
                "stream_type" => $row["stream_type"],
                "stream_id" => $row["id"],//.$codigo_unico,
                "stream_icon" => $row["stream_icon"],
                "epg_channel_id" => $row["epg_channel_id"],
                "added" => $row["added"],
                "is_adult" => "0",
                "custom_sid" => "",
                "tv_archive" => 0,
                "direct_source" => "",
                "tv_archive_duration" => 0,
                "category_id" => $row["category_id"],
                "category_ids" => [$row["category_id"]],
                "thumbnail" => "",
                "codigo_unico" => (int) $codigo_unico,
            ];
        } elseif ($action == 'get_vod_streams') {
            $results[] = [
                "num" => $num,
                "name" => nl2br($row["name"]),
                "title" => nl2br($row["name"]),
                "year" => $row["year"],
                "stream_type" => $row["stream_type"],
                "stream_id" => $row["id"],
                "stream_icon" => $row["stream_icon"],
                "rating" => $row["rating"],
                "rating_5based" => $row["rating_5based"],
                "added" => $row["added"],
                "is_adult" => $row["is_adult"],
                "category_id" => $row["category_id"],
                "container_extension" => "mp4",
                "custom_sid" => "",
                "direct_source" => "",
            ];
        }  // Ajusta os campos para corresponderem ao formato do JSON desejado
        if ($action == 'get_series') {
            $tmdb_id = "";
                $results[] = [
                    "num" => $num,
                    "name" => $row["name"] ?? "",
                    "title" => $row["name"] ?? "",
                    "year" => $row["year"] ?? "",
                    "stream_type" => $row["stream_type"],
                    "series_id" => $row["id"],
                    "cover" => $row["cover"] ?? "",
                    "plot" => $row["plot"] ?? "",
                    "cast" => !empty($row["cast"]) ? $row["cast"] : null,
                    "director" => !empty($row["director"]) ? $row["director"] : null,
                    "genre" => $row["genre"] ?? "",
                    "release_date" => $row["release_date"] ?? "",
                    "releaseDate" => $row["release_date"] ?? "",
                    "last_modified" => $row["last_modified"] ?? "",
                    "rating" => $row["rating"] ?? "0",
                    "rating_5based" => floatval($row["rating_5based"] ?? 0),
                    "backdrop_path" => !empty($row["backdrop_path"]) ? explode(",", $row["backdrop_path"]) : [],
                    "youtube_trailer" => !empty($row["youtube_trailer"]) ? $row["youtube_trailer"] : null,
                    "episode_run_time" => $row["episode_run_time"] ?? "0",
                    "category_id" => $row["category_id"],
                    "category_ids" => [$row["category_id"]],
                ];            
        }
    }

    // Filtro por category_id, se aplicável
    if (isset($category_id)) {
        $category_id_to_filter = $category_id;
        $results = array_filter($results, function ($item) use ($category_id_to_filter) {
            return $item['category_id'] == $category_id_to_filter;
        });

        // Reorganiza os índices do array após a filtragem
        $results = array_values($results);
    }

    // Retorna o JSON
    header('Content-Type: application/json');
    echo json_encode($results, JSON_PRETTY_PRINT);
    exit;
}

//-----GET de informações de filmes
//player_api.php?username=X&password=X&action=get_vod_info&vod_id=X
if (isset($_GET['action']) && isset($_GET['vod_id']) && $_GET['action'] === 'get_vod_info') {
    
    $query = $conexao->prepare("SELECT * FROM streams WHERE id = :vod_id");
    $query->execute(array(":vod_id" => $vod_id));
    $info = array();
    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
        $info = array(
            "kinopoisk_url" => $row["kinopoisk_url"] ?? "",
            "tmdb_id" => $row["tmdb_id"] ?? "",
            "name" => $row["name"] ?? "",
            "o_name" => $row["name"] ?? "",
            "cover_big" => $row["stream_icon"] ?? "",
            "movie_image" => $row["stream_icon"] ?? "",
            "release_date" => $row["release_date"] ?? "",
            "episode_run_time" => $row["episode_run_time"] ?? "",
            "youtube_trailer" => $row["youtube_trailer"] ?? "",
            "director" => $row["director"] ?? "",
            "actors" => $row["actors"] ?? "",
            "cast" => $row["cast"] ?? "",
            "description" => $row["description"] ?? "",
            "plot" => $row["description"] ?? "",
            "age" => $row["age"] ?? "",
            "mpaa_rating" => "",
            "rating_count_kinopoisk" => $row["rating_count_kinopoisk"] ?? "",
            "country" => $row["country"] ?? "",
            "genre" => $row["genre"] ?? "",
            "backdrop_path" => explode(",", $row["backdrop_path"] ?? ""),
            "duration_secs" => $row["duration_secs"] ?? "",
            "duration" => $row["duration"] ?? "",
            "bitrate" => $row["bitrate"] ?? "",
            "rating" => $row["rating"] ?? "",
            "releasedate" => $row["releasedate"] ?? "",
            "subtitles" => explode(",", $row["subtitles"] ?? "")
        );
    }
    $query = $conexao->prepare("SELECT * FROM streams WHERE id = :vod_id");
    $query->execute(array(":vod_id" => $vod_id));
    $movie_data = array();
    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
        $movie_data = array(
            "name" => $row["name"],
            "title" => $row["name"],
            "year" => $row["year"],
            "added" => $row["added"],
            "stream_id" => $row["id"],
            "category_id" => $row["category_id"],
            "category_ids" => [$row["category_id"]],
            "container_extension" => $row["container_extension"],
            "custom_sid" => "",
            "direct_source" => "",
        );
    }
    $result = array(
        "info" => $info,
        "movie_data" => $movie_data
    );


    echo json_encode($result);
    exit;
}
//-----GET de informações de series
//player_api.php?username=X&password=X&action=get_series_info&series_id=X
if (isset($_GET['action'], $_GET['series_id']) && $_GET['action'] === 'get_series_info') {
    // Obtém e valida o ID da série
    $series_id = filter_input(INPUT_GET, 'series_id', FILTER_VALIDATE_INT);

    if (!$series_id) {
        http_response_code(400);
        echo json_encode(["error" => "ID da série inválido."]);
        exit;
    }

    // Busca informações da série
    $query = $conexao->prepare("SELECT * FROM series WHERE id = :series_id");
    $query->execute([":series_id" => $series_id]);
    $row = $query->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        http_response_code(404);
        echo json_encode(["error" => "Série não encontrada."]);
        exit;
    }

    $series = [
        "name" => $row["name"] ?? "",
        "title" => $row["name"] ?? "",
        "cover" => $row["cover"] ?? "",
        "year" => $row["year"] ?? "",
        "plot" => $row["plot"] ?? "",
        "cast" => $row["cast"] ?? "",
        "director" => $row["director"] ?? "",
        "genre" => $row["genre"] ?? "",
        "release_date" => $row["release_date"] ?? "",
        "last_modified" => !empty($row["last_modified"]) ? explode(",", $row["last_modified"]) : [],
        "rating" => $row["rating"] ?? "",
        "rating_5based" => $row["rating_5based"] ?? "",
        "backdrop_path" => !empty($row["backdrop_path"]) ? explode(",", $row["backdrop_path"]) : [],
        "youtube_trailer" => $row["youtube_trailer"] ?? "",
        "episode_run_time" => $row["episode_run_time"] ?? "",
        "category_id" => $row["category_id"] ?? "",
        "category_ids" => [$row["category_id"] ?? ""]
    ];

    // Busca temporadas da série
    $query = $conexao->prepare("SELECT * FROM series_seasons WHERE series_id = :series_id");
    $query->execute([":series_id" => $series_id]);
    $seasons = [];

    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
        $seasons[] = [
            "air_date" => $row["air_date"] ?? "",
            "episode_count" => $row["episode_count"] ?? "",
            "id" => $row["id"] ?? "",
            "name" => $row["name"] ?? "",
            "overview" => $row["overview"] ?? "",
            "season_number" => $row["season_number"] ?? "",
            "cover" => $row["cover"] ?? "",
            "cover_big" => $row["cover_big"] ?? ""
        ];
    }

    // Busca episódios organizados por temporada
    $query = $conexao->prepare("SELECT * FROM series_episodes WHERE series_id = :series_id ORDER BY season, episode_num");
    $query->execute([":series_id" => $series_id]);
    $episodes = [];

    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
        $season = $row['season'] ?? 0;
        if (!isset($episodes[$season])) {
            $episodes[$season] = [];
        }

        $episodes[$season][] = [
            "id" => $row["id"] ?? "",
            "episode_num" => $row["episode_num"] ?? "",
            "title" => $row["title"] ?? "",
            "container_extension" => "mp4",
            "info" => [
                "tmdb_id" => $row["tmdb_id"] ?? "",
                "duration_secs" => $row["duration_secs"] ?? "",
                "duration" => $row["duration"] ?? "",
                "bitrate" => $row["bitrate"] ?? "",
                "cover_big" => $row["cover_big"] ?? "",
                "movie_image" => $row["movie_image"] ?? "",
                "plot" => $row["plot"] ?? ""
            ],
            "subtitles" => !empty($row["subtitles"]) ? explode(",", $row["subtitles"]) : [],
            "custom_sid" => $row["custom_sid"] ?? "",
            "added" => $row["added"] ?? "",
            "season" => $season,
            "direct_source" => ""
        ];
    }

    // Retorno final
    header('Content-Type: application/json');
    echo json_encode([
        "seasons" => $seasons,
        "info" => $series,
        "episodes" => $episodes
    ]);
    exit;
}




http_response_code(401); // 401 Unauthorized
$errorResponse = json_encode(["user_info" => ["auth" => 0]]);
echo $errorResponse;
exit();