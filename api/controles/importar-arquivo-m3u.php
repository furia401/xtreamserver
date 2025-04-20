<?php
set_time_limit(0);
require_once('db.php');
session_start();

function executarConsulta($conexao, $query, $parametros) {
    $stmt = $conexao->prepare($query);
    $stmt->execute($parametros);
    return $stmt;
}

function adicionarCategoria($nome, $type) {
    $conexao = conectar_bd();
    $query = "SELECT id FROM categoria WHERE nome = :nome AND type = :type";
    $categoria = executarConsulta($conexao, $query, ['nome' => $nome, 'type' => $type])->fetch(PDO::FETCH_ASSOC);
    if ($categoria) return $categoria['id'];

    $query = "INSERT INTO categoria (nome, type, admin_id) VALUES (:nome, :type, :admin_id)";
    executarConsulta($conexao, $query, ['nome' => $nome, 'type' => $type, 'admin_id' => $_SESSION['admin_id']]);
    return $conexao->lastInsertId();
}

function adicionarSeries($nomeSerie, $category_id, $tvgLogo) {
    $conexao = conectar_bd();
    $query = "SELECT id FROM series WHERE name = :name AND category_id = :category_id";
    $series = executarConsulta($conexao, $query, ['name' => $nomeSerie, 'category_id' => $category_id])->fetch(PDO::FETCH_ASSOC);
    if ($series) return $series['id'];

    $query = "INSERT INTO series (name, category_id, cover) VALUES (:name, :category_id, :cover)";
    executarConsulta($conexao, $query, ['name' => $nomeSerie, 'category_id' => $category_id, 'cover' => $tvgLogo]);
    return $conexao->lastInsertId();
}

function adicionarSeasonsSeries($temporada, $episode_count, $series_id) {
    $conexao = conectar_bd();
    $query = "SELECT * FROM series_seasons WHERE series_id = :series_id AND season_number = :season_number";
    $temporadaExistente = executarConsulta($conexao, $query, ['series_id' => $series_id, 'season_number' => $temporada])->fetch(PDO::FETCH_ASSOC);

    if ($temporadaExistente) {
        if ($temporadaExistente['episode_count'] >= $episode_count) return $temporadaExistente['episode_count'];
        $query = "UPDATE series_seasons SET episode_count = :episode_count WHERE series_id = :series_id AND season_number = :season_number";
        executarConsulta($conexao, $query, ['episode_count' => $episode_count, 'series_id' => $series_id, 'season_number' => $temporada]);
        return $episode_count;
    }

    $query = "INSERT INTO series_seasons (series_id, season_number, episode_count, name) VALUES (:series_id, :season_number, :episode_count, :name)";
    executarConsulta($conexao, $query, [
        'series_id' => $series_id,
        'season_number' => $temporada,
        'episode_count' => $episode_count,
        'name' => 'Temporada ' . $temporada
    ]);
    return $episode_count;
}
function verificarLinksExistentes($conexao, $urls) {
    $tabelas = ['series_episodes', 'streams'];
    if (empty($urls) || empty($tabelas)) return [];

    $resultss = [];
    $placeholders = implode(',', array_fill(0, count($urls), '?'));

    foreach ($tabelas as $tabela) {
        $query = "SELECT link FROM $tabela WHERE link IN ($placeholders)";
        $stmt = $conexao->prepare($query);
        $stmt->execute($urls);

        $resultss = array_merge($resultss, $stmt->fetchAll(PDO::FETCH_COLUMN, 0));
    }

    return array_unique($resultss);
}

function processarUrl($url) {

    $parsedUrl = parse_url($url);

    $path = $parsedUrl['path'];

    $pathParts = explode('/', trim($path, '/'));

    if (count($pathParts) >= 4) {
        $domain = $parsedUrl['host'];

        $usuario = $pathParts[1];
        $senha = $pathParts[2];
        $arquivo = pathinfo($pathParts[3], PATHINFO_FILENAME);
    }elseif (count($pathParts) <= 3) {
        $domain = $parsedUrl['host'];
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
        'dominio' => $domain,
        'usuario' => $usuario,
        'senha' => $senha,
        'arquivo' => $arquivo,
    ];
}

function adicionarLinks($links) {
    $conexao = conectar_bd();
    $results = [
        'success' => [],
        'exists' => 0,
        'error' => [],
        'urls' => 0,
        'movie' => 0,
        'live' => 0,
        'series' => 0,
        'episodios' => 0,
        'temporadas' => 0,
        'epg' => 0,
    ];

    $conexao->beginTransaction();

    if (!isset($_SESSION['cache'])) {
        $_SESSION['cache'] = [];
    }

    try {
        $urls = array_column($links, 'url');
        $linksExistentes_episodios = verificarLinksExistentes($conexao, $urls);
        foreach ($links as $link) {
            $url = trim($link['url'] ?? '');
            if (in_array($url, $linksExistentes_episodios)) {
                $results['exists'] ++;

                continue;
            } else{
                $results['urls'] ++;
                $groupTitle = trim($link['groupTitle'] ?? '');
                $channelName = trim($link['channelName'] ?? '');
                $tvgLogo = trim($link['tvgLogo'] ?? '');
                $epg_channel_id = trim($link['tvgId'] ?? '');
                $epg_channel_name = trim($link['tvgName'] ?? '');
                $container = (preg_match('/\.(mp4|mkv|ts|m3u8)$/', $url, $ext)) ? $ext[1] : 'ts';

                $type = (preg_match('/\/live\b/i', $url)) ? 'live' :
            ((preg_match('/\/series\b/i', $url)) ? 'series' :
            ((preg_match('/\/movie\b/i', $url)) ? 'movie' : null));

                if (is_null($type)) {
                    $verificarurl = @processarUrl($url);
                    if ($verificarurl) {
                        $type = "live";
                    }else{
                        $type = (preg_match('/\b(canais)\b/i', $groupTitle)) ? 'live' :
                        ((preg_match('/\b(series)\b/i', $groupTitle)) ? 'series' :
                        ((preg_match('/\b(filmes)\b/i', $groupTitle)) ? 'movie' : null));
                    }

                }

                if (!isset($_SESSION['cache'][$groupTitle]) && $groupTitle != null) {
                    $queryCategoria = "SELECT id FROM categoria WHERE nome = :nome AND type = :type";
                    $categoria = executarConsulta($conexao, $queryCategoria, ['nome' => $groupTitle, 'type' => $type])->fetch(PDO::FETCH_ASSOC);

                    if ($categoria) {
                        $category_id = $categoria['id'];
                    } else {
                        $queryInsertCategoria = "INSERT INTO categoria (nome, type, admin_id) VALUES (:nome, :type, :admin_id)";
                        executarConsulta($conexao, $queryInsertCategoria, [
                            'nome' => $groupTitle,
                            'admin_id' => $_SESSION['admin_id'],
                            'type' => $type
                        ]);
                        $category_id = $conexao->lastInsertId();
                    }

                    $_SESSION['cache'][$groupTitle] = [
                        'category_id' => $category_id,
                        'series' => [],
                    ];
                } else {
                    $category_id = $_SESSION['cache'][$groupTitle]['category_id'];
                }

                if ($type === 'series' && $url != null) {

                    if (preg_match('/^(.*?)\s?S(\d{1,3})E(\d{1,4})$/', $channelName, $matches)) {
                        $nomeSerie = trim($matches[1]);
                        $temporada = (int) str_replace("S", "", $matches[2]);
                        $episodioNum = (int) str_replace("E", "", $matches[3]);

                        $titulosemepisodio = "{$nomeSerie} S{$temporada}";
                    } elseif (preg_match('/^(.*?)\s(S\d{1,3})\s(E\d{1,4})$/', $channelName, $matches)) {
                        $nomeSerie = trim($matches[1]);
                        $temporada = (int) str_replace("S", "", $matches[2]);
                        $episodioNum = (int) str_replace("E", "", $matches[3]);
                        $titulosemepisodio = "{$nomeSerie} S{$temporada}";
                    }

                    if (!isset($_SESSION['cache'][$groupTitle]['series'][$nomeSerie])) {
                        $querySeries = "SELECT id FROM series WHERE name = :name AND category_id = :category_id";
                        $serieExistente = executarConsulta($conexao, $querySeries, ['name' => $nomeSerie, 'category_id' => $category_id])->fetch(PDO::FETCH_ASSOC);

                        if ($serieExistente) {
                            $series_id = $serieExistente['id'];

                        } else {

                            $queryInsertSeries = "INSERT INTO series (name, category_id, cover) VALUES (:name, :category_id, :cover)";
                            executarConsulta($conexao, $queryInsertSeries, [
                                'name' => $nomeSerie,
                                'category_id' => $category_id,
                                'cover' => $tvgLogo
                            ]);
                            $series_id = $conexao->lastInsertId();

                            $results['series'] ++;
                        }

                        $query = "INSERT INTO series_seasons (series_id, season_number, episode_count, name, category_id) VALUES (:series_id, :season_number, :episode_count, :name, :category_id)";
                        executarConsulta($conexao, $query, [
                            'series_id' => $series_id,
                            'season_number' => $temporada,
                            'category_id' => $category_id,
                            'episode_count' => $episodioNum,
                            'name' => 'Temporada ' . $temporada
                        ]);
                        $_SESSION['cache'][$groupTitle]['series'][$nomeSerie] = [
                            'series_id' => $series_id,
                            'seasons' => [$temporada],
                            'temporadas_atualizadas' => [],
                        ];
                        $results['temporadas'] ++;
                    } else {
                        $series_id = $_SESSION['cache'][$groupTitle]['series'][$nomeSerie]['series_id'];
                    }

                    if (!isset($_SESSION['cache'][$groupTitle]['series'][$nomeSerie]['seasons'])) {
                        $_SESSION['cache'][$groupTitle]['series'][$nomeSerie]['seasons'] = [];
                    }

                    if (!in_array($temporada, $_SESSION['cache'][$groupTitle]['series'][$nomeSerie]['seasons'])) {

                        $query = "SELECT * FROM series_seasons WHERE series_id = :series_id AND season_number = :season_number";
                        $temporadaExistente = executarConsulta($conexao, $query, ['series_id' => $series_id, 'season_number' => $temporada])->fetch(PDO::FETCH_ASSOC);

                        if ($temporadaExistente) {
                            if ($temporadaExistente['episode_count'] >= $episodioNum) return $temporadaExistente['episode_count'];
                            $query = "UPDATE series_seasons SET episode_count = :episode_count WHERE series_id = :series_id AND season_number = :season_number";
                            executarConsulta($conexao, $query, ['episode_count' => $episodioNum, 'series_id' => $series_id, 'season_number' => $temporada]);
                            $_SESSION['cache'][$groupTitle]['series'][$nomeSerie]['temporadas_atualizadas'][] = $temporada;
                        }else{
                            $query = "INSERT INTO series_seasons (series_id, season_number, episode_count, name) VALUES (:series_id, :season_number, :episode_count, :name)";
                            executarConsulta($conexao, $query, [
                                'series_id' => $series_id,
                                'season_number' => $temporada,
                                'episode_count' => $episodioNum,
                                'name' => 'Temporada ' . $temporada
                            ]);
                            $results['temporadas'] ++;
                            $_SESSION['cache'][$groupTitle]['series'][$nomeSerie]['seasons'][] = $temporada;
                        }

                    }

                    $query = "INSERT INTO series_episodes (link, series_id, season, episode_num, title, container_extension, movie_image, category_id) 
                              VALUES (:link, :series_id, :season, :episode_num, :title, :container_extension, :movie_image, :category_id)";
                    executarConsulta($conexao, $query, [
                        'link' => $url,
                        'series_id' => $series_id,
                        'season' => $temporada,
                        'episode_num' => $episodioNum,
                        'title' => $channelName,
                        'container_extension' => $container,
                        'movie_image' => $tvgLogo,
                        'category_id' => $category_id,
                    ]);
                    $results['episodios'] ++;

                }
                if (($type === 'live' || $type === 'movie') && $url != null) {

                    $epg_channel = $epg_channel_name;

                    if ($epg_channel_id) {
                        $epg_channel = $epg_channel_id; 
                    }
                    $query = "INSERT INTO streams (link, category_id, name, stream_type, container_extension, stream_icon) 
                              VALUES (:link, :category_id, :name, :stream_type, :container_extension, :stream_icon)";
                    $execute = executarConsulta($conexao, $query, [
                        'link' => $url, 
                        'category_id' => $category_id, 
                        'name' => $channelName,
                        'stream_type' => $type, 
                        'container_extension' => $container, 
                        'stream_icon' => $tvgLogo,
                    ]);
                    $results[$type] ++;

                }
            }
        }

        $conexao->commit();
    } catch (Exception $e) {
        $conexao->rollBack();
        $results['error'][] = "error";
    }

    return $results;
}

function adicionarepg($links) {
    $conexao = conectar_bd();
    $results = [
        'success' => [],
        'exists' => [],
        'error' => [],
        'urls' => 0,
        'movie' => 0,
        'live' => 0,
        'series' => 0,
        'episodios' => 0,
        'temporadas' => 0,
        'epg' => 0,
    ];
    $conexao->beginTransaction();
    try {
        foreach ($links as $link) {
                $results['epg']++;
                $query = "UPDATE streams 
                          SET epg_channel_id = :epg_channel_id 
                          WHERE name = :name";

                $execute = executarConsulta($conexao, $query, [
                    'epg_channel_id' => $link['id'],
                    'name' => $link['nome']
                ]);
        }
        $conexao->commit();
    } catch (Exception $e) {
        $conexao->rollBack();
        $results['error'][] = "Erro crítico: " . $e->getMessage();
    }

    return $results;
}

header('Content-Type: application/json');
if (isset($_POST['block'])) {
    try {
        $links = $_POST['block'] ?? [];
        echo json_encode(['results' => (!empty($links)) ? adicionarLinks($links) : ['error' => 'Nenhum link fornecido.']]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
}
if (isset($_POST['epg'])) {
    try {
        $links = $_POST['epg'] ?? [];

        if (empty($links)) {
            throw new Exception("Nenhum dado recebido");
        }

        echo json_encode([
            'results' => adicionarepg($links)
        ]);

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'results' => [
                'error' => [$e->getMessage()]
            ]
        ]);
    }
}

?>