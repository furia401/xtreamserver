<?php
//ini_set('memory_limit', '512M'); // Aumenta para 512 MB

// Obtém os parâmetros da URL
$username = isset($_GET['username']) ? $_GET['username'] : null;
$password = isset($_GET['password']) ? $_GET['password'] : null;
$output = isset($_GET['output']) ? $_GET['output'] : "m3u8";

// Valida os parâmetros
if (empty($username) || empty($password)) {
    die("Username e password são obrigatórios.");
}

// Conecta-se ao banco de dados de usuários
require_once($_SERVER['DOCUMENT_ROOT'] . '/api/controles/db.php');
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
    die("Username e password incorretos.");
}

if (isset($_GET['username']) && isset($_GET['password'])) {
    // Obtém o host atual
    $hostUrl = $_SERVER['HTTP_HOST'];

    // Define o arquivo temporário para o conteúdo M3U
    $tempFile = tempnam(sys_get_temp_dir(), 'playlist_') . '.m3u';

    // Abre o arquivo para escrita
    $fileHandle = fopen($tempFile, 'w');

    // Cabeçalho M3U
    fwrite($fileHandle, "#EXTM3U-1\n");

    // Geração de canais e filmes (streams)
    $query = "
        SELECT s.id, s.name, s.stream_icon, s.category_id, s.stream_type, s.container_extension, c.nome AS category_name
        FROM streams s
        LEFT JOIN categoria c ON s.category_id = c.id
        WHERE s.stream_type IN ('live', 'movie')
    ";

    $stmt = $conexao->prepare($query);
    $stmt->execute();

    while ($stream = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $line = "#EXTINF:-1 tvg-id=\"". htmlspecialchars($stream['epg_channel_id']) ."\" tvg-name=\"" . htmlspecialchars($stream['epg_channel_id']) . "\" ";
        $line .= "tvg-logo=\"" . htmlspecialchars($stream['stream_icon']) . "\" ";
        $line .= "group-title=\"" . htmlspecialchars($stream['category_name']) . "\",";
        $line .= htmlspecialchars($stream['name']) . "\n";
        if ($stream['stream_type'] === "live") {
            $line .= "http://$hostUrl:80/" . htmlspecialchars($stream['stream_type']) . "/$username/$password/" . $stream['id'] . ".".$output."\n";
        }else{
            $line .= "http://$hostUrl:80/" . htmlspecialchars($stream['stream_type']) . "/$username/$password/" . $stream['id'] . ".".$stream['container_extension']."\n";
        }
        
        fwrite($fileHandle, $line);
    }

    // Geração de séries
    $query = "
        SELECT e.id, e.series_id, e.title, e.container_extension, e.movie_image, s.name AS series_name, s.category_id, c.nome AS category_name
        FROM series_episodes e
        LEFT JOIN series s ON e.series_id = s.id
        LEFT JOIN categoria c ON s.category_id = c.id
    ";

    $stmt = $conexao->prepare($query);
    $stmt->execute();

    while ($episode = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $name = $episode['series_name'] . " - " . $episode['title'];
        $line = "#EXTINF:-1 tvg-id=\"\" tvg-name=\"" . htmlspecialchars($name) . "\" ";
        $line .= "tvg-logo=\"" . htmlspecialchars($episode['movie_image']) . "\" ";
        $line .= "group-title=\"" . htmlspecialchars($episode['category_name']) . "\",";
        $line .= htmlspecialchars($name) . "\n";
        $line .= "http://$hostUrl:80/series/$username/$password/" . $episode['id'] .".".$episode['container_extension']." \n";
        fwrite($fileHandle, $line);
    }

    // Fecha o arquivo após escrever todo o conteúdo
    fclose($fileHandle);

    // Define os cabeçalhos para download do arquivo
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="'.$username.'.m3u"');
    header('Content-Length: ' . filesize($tempFile));

    // Lê o arquivo e envia para o cliente
    readfile($tempFile);

    // Remove o arquivo temporário
    unlink($tempFile);

    exit;
}
