<?php

$username = isset($_GET['username']) ? $_GET['username'] : null;
$password = isset($_GET['password']) ? $_GET['password'] : null;
$output = isset($_GET['output']) ? $_GET['output'] : "m3u8";

if (empty($username) || empty($password)) {
    die("Username e password são obrigatórios.");
}

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
    http_response_code(401); 
    die("Username e password incorretos.");
}

if (isset($_GET['username']) && isset($_GET['password'])) {

    $hostUrl = $_SERVER['HTTP_HOST'];

    $tempFile = tempnam(sys_get_temp_dir(), 'playlist_') . '.m3u';

    $fileHandle = fopen($tempFile, 'w');

    fwrite($fileHandle, "#EXTM3U-1\n");

    $query = "
        SELECT s.id, s.name, s.stream_icon, s.category_id, s.stream_type, s.container_extension, c.nome AS category_name
        FROM streams s
        LEFT JOIN categoria c ON s.category_id = c.id
        WHERE s.stream_type IN ('live', 'movie')
    ";

    $stmt = $conexao->prepare($query);
    $stmt->execute();

    while ($stream = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $line = "#EXTINF:-1 tvg-id=\"\" tvg-name=\"" . htmlspecialchars($stream['name']) . "\" ";
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

    fclose($fileHandle);

    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="'.$username.'.m3u"');
    header('Content-Length: ' . filesize($tempFile));

    readfile($tempFile);

    unlink($tempFile);

    exit;
}