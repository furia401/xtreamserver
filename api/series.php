<?php
session_start();
require_once('./controles/db.php');
require_once('./controles/series.php');
require_once('./controles/checkLogout.php');
header('Content-Type: application/json; charset=utf-8');

checkLogoutapi();

if (isset($_POST['editar_series'])) {
    $id = $_POST['editar_series'];

    if (function_exists('editar_series')) {

        $editar_series = editar_series($id);
        if (!$editar_series){
            $resposta = [
                'title' => 'Erro!',
                'msg' => 'Erro ao buscar info do cliente.',
                'icon' => 'error'
            ];
            echo json_encode($resposta);
            exit();
        }else{
            echo json_encode($editar_series);
            exit();
        }
    }else {
        $resposta = [
            'title' => 'Erro!',
            'msg' => 'Funçao nao encontrada!',
            'icon' => 'error'
        ];
        echo json_encode($resposta);
        exit();
    }
}

if (isset($_POST['confirme_editar_series'])) {

    $nome          = isset($_POST["nome"]) ? htmlspecialchars($_POST["nome"]) : '';
    $link          = isset($_POST["link"]) ? htmlspecialchars($_POST["link"]) : null;
    $logo          = isset($_POST["logo"]) ? htmlspecialchars($_POST["logo"]) : null;
    $Categoria     = isset($_POST["Categoria"]) ? (int) $_POST["Categoria"] : null;
    $adulto        = isset($_POST["adulto"]) ? (int) $_POST["adulto"] : '0';
    $id            = isset($_POST["confirme_editar_series"]) ? (int) $_POST["confirme_editar_series"] : null;
    $director      = isset($_POST["director"]) ? htmlspecialchars($_POST["director"]) : '';
    $genre         = isset($_POST["genre"]) ? htmlspecialchars($_POST["genre"]) : '';
    $actors        = isset($_POST["actors"]) ? htmlspecialchars($_POST["actors"]) : '';
    $plot          = isset($_POST["plot"]) ? htmlspecialchars($_POST["plot"]) : '';
    $backdrop_path = isset($_POST["backdrop_path"]) ? htmlspecialchars($_POST["backdrop_path"]) : '';
    $releasedate   = isset($_POST["releasedate"]) ? htmlspecialchars($_POST["releasedate"]) : '';
    $duration      = isset($_POST["duration"]) ? htmlspecialchars($_POST["duration"]) : '';
    $rating        = isset($_POST["rating"]) ? htmlspecialchars($_POST["rating"]) : '';
    $rating_5based = isset($_POST["rating_5based"]) ? htmlspecialchars($_POST["rating_5based"]) : '';
    $year          = isset($_POST["year"]) ? (int) $_POST["year"] : '';



    if (function_exists('confirme_editar_series')) {
        $editar_series = confirme_editar_series($id, $nome, $link, $logo, $Categoria, $adulto, $director, $genre, $actors, $plot, $backdrop_path, $releasedate, $duration, $rating, $rating_5based, $year);
        if (!$editar_series){
            $resposta = [
                'title' => 'Erro!',
                'msg' => 'Erro ao renovar cliente.',
                'icon' => 'error'
            ];
            echo json_encode($resposta);
            exit();
        }else{
            echo json_encode($editar_series);
            exit();
        }
    }else {

        $resposta = [
            'title' => 'Erro!',
            'msg' => 'Funçao nao encontrada!',
            'icon' => 'error'
        ];
        echo json_encode($resposta);
        exit();
    }
}

if (isset($_POST['delete_series'])) {
    $name = isset($_POST["name"]) ? htmlspecialchars($_POST["name"]) : null;
    $id   = isset($_POST["delete_series"]) ? (int) $_POST["delete_series"] : null;
    if (function_exists('delete_series')) {
        $delete_series = delete_series($id, $name);
        if (!$delete_series){
            $resposta = [
                'title' => 'Erro!',
                'msg' => 'Erro ao deletar cliente.',
                'icon' => 'error'
            ];
            echo json_encode($resposta);
            exit();
        }else{
            echo json_encode($delete_series);
            exit();
        }
    }else {
        $resposta = [
            'title' => 'Erro!',
            'msg' => 'Funçao nao encontrada!',
            'icon' => 'error'
        ];
        echo json_encode($resposta);
        exit();
    }
}

if (isset($_POST['confirme_delete_series'])) {
    $id = $_POST['confirme_delete_series'];
    $nome = isset($_POST['nome']) ? $_POST['nome'] : null;

    if (function_exists('confirme_delete_series')) {
        $delete_series = confirme_delete_series($id, $nome);
        if (!$delete_series){
            $resposta = [
                'title' => 'Erro!',
                'msg' => 'Erro ao deletar canal.',
                'icon' => 'error'
            ];
            echo json_encode($resposta);
            exit();
        }else{
            echo json_encode($delete_series);
            exit();
        }
    }else {

        $resposta = [
            'title' => 'Erro!',
            'msg' => 'Funçao nao encontrada!',
            'icon' => 'error'
        ];
        echo json_encode($resposta);
        exit();
    }
}

if (isset($_POST['adicionar_series'])) {

    if (function_exists('adicionar_series')) {

        $adicionar_series = adicionar_series();
        if (!$adicionar_series){
            $resposta = [
                'title' => 'Erro!',
                'msg' => 'Erro ao adicionar series.',
                'icon' => 'error'
            ];
            echo json_encode($resposta);
            exit();
        }else{
            echo json_encode($adicionar_series);
            exit();
        }
    }else {
        $resposta = [
            'title' => 'Erro!',
            'msg' => 'Funçao nao encontrada!',
            'icon' => 'error'
        ];
        echo json_encode($resposta);
        exit();
    }
}

if (isset($_POST['confirme_adicionar_series'])) {

    $nome               = isset($_POST["nome"]) ? htmlspecialchars($_POST["nome"]) : '';
    $link            = isset($_POST["link"]) ? htmlspecialchars($_POST["link"]) : null;
    $logo              = isset($_POST["logo"]) ? htmlspecialchars($_POST["logo"]) : null;
    $Categoria             = isset($_POST["Categoria"]) ? (int) $_POST["Categoria"] : null;
    $adulto              = isset($_POST["adulto"]) ? (int) $_POST["adulto"] : '0';

    $director               = isset($_POST["director"]) ? htmlspecialchars($_POST["director"]) : '';
    $genre               = isset($_POST["genre"]) ? htmlspecialchars($_POST["genre"]) : '';
    $actors               = isset($_POST["actors"]) ? htmlspecialchars($_POST["actors"]) : '';
    $plot               = isset($_POST["plot"]) ? htmlspecialchars($_POST["plot"]) : '';
    $backdrop_path               = isset($_POST["backdrop_path"]) ? htmlspecialchars($_POST["backdrop_path"]) : '';
    $releasedate              = isset($_POST["releasedate"]) ? htmlspecialchars($_POST["releasedate"]) : '';
    $duration              = isset($_POST["duration"]) ? htmlspecialchars($_POST["duration"]) : '';
    $rating              = isset($_POST["rating"]) ? htmlspecialchars($_POST["rating"]) : '';
    $rating_5based              = isset($_POST["rating_5based"]) ? htmlspecialchars($_POST["rating_5based"]) : '';
    $year              = isset($_POST["year"]) ? (int) $_POST["year"] : '';

    if (function_exists('confirme_adicionar_series')) {
        $confirme_adicionar_series = confirme_adicionar_series($nome, $link, $logo, $Categoria, $adulto, $director, $genre, $actors, $plot, $backdrop_path, $releasedate, $duration, $rating, $rating_5based, $year);
        if (!$confirme_adicionar_series){
            $resposta = [
                'title' => 'Erro!',
                'msg' => 'Erro na funçao mudar servidor.',
                'icon' => 'error'
            ];
            echo json_encode($resposta);
            exit();
        }else{
            echo json_encode($confirme_adicionar_series);
            exit();
        }
    }else {

        $resposta = [
            'title' => 'Erro!',
            'msg' => 'Funçao nao encontrada!',
            'icon' => 'error'
        ];
        echo json_encode($resposta);
        exit();
    }
}

if (isset($_POST['confirme_adicionar_temporadas'])) {

    $series_id            = isset($_POST["confirme_adicionar_temporadas"]) ? htmlspecialchars($_POST["confirme_adicionar_temporadas"]) : null;

    if (function_exists('confirme_adicionar_temporadas')) {
        $confirme_adicionar_temporadas = confirme_adicionar_temporadas($series_id);
        if (!$confirme_adicionar_temporadas){
            $resposta = [
                'title' => 'Erro!',
                'msg' => 'Erro na funçao.',
                'icon' => 'error'
            ];
            echo json_encode($resposta);
            exit();
        }else{
            echo json_encode($confirme_adicionar_temporadas);
            exit();
        }
    }else {

        $resposta = [
            'title' => 'Erro!',
            'msg' => 'Funçao nao encontrada!',
            'icon' => 'error'
        ];
        echo json_encode($resposta);
        exit();
    }
}

if (isset($_POST['ver_episodios'])) {
    $series_id = $_POST['ver_episodios'];
    $temporada = $_POST['temporada'];
    $episodio = $_POST['ep'];

    if (function_exists('ver_episodios')) {

        $ver_episodios = ver_episodios($series_id, $temporada, $episodio);
        if (!$ver_episodios){
            $resposta = [
                'title' => 'Erro!',
                'msg' => 'Erro ao buscar info dos episodios.',
                'icon' => 'error'
            ];
            echo json_encode($resposta);
            exit();
        }else{
            echo json_encode($ver_episodios);
            exit();
        }
    }else {
        $resposta = [
            'title' => 'Erro!',
            'msg' => 'Funçao nao encontrada!',
            'icon' => 'error'
        ];
        echo json_encode($resposta);
        exit();
    }
}

if (isset($_POST['confirme_editar_episodio'])) {
    $id        = isset($_POST["confirme_editar_episodio"]) ? htmlspecialchars($_POST["confirme_editar_episodio"]) : null;
    $temporada = isset($_POST["temporada"]) ? (int) $_POST["temporada"] : null;
    $series_id = isset($_POST["series_id"]) ? (int) $_POST["series_id"] : null;
    $episodio  = isset($_POST["episodio"]) ? (int) $_POST["episodio"] : null;
    $title     = isset($_POST["nome"]) ? htmlspecialchars($_POST["nome"]) : null;
    $link      = isset($_POST["link"]) ? htmlspecialchars($_POST["link"]) : null;
    $capa      = isset($_POST["capa"]) ? htmlspecialchars($_POST["capa"]) : null;
    $duration  = isset($_POST["duration"]) ? htmlspecialchars($_POST["duration"]) : null;
    if (function_exists('confirme_editar_episodio')) {
        $confirme_editar_episodio = confirme_editar_episodio($id, $series_id, $temporada, $title, $link, $capa, $episodio, $duration);
        if (!$confirme_editar_episodio){
            $resposta = [
                'title' => 'Erro!',
                'msg' => 'Erro ao editar episodio.',
                'icon' => 'error'
            ];
            echo json_encode($resposta);
            exit();
        }else{
            echo json_encode($confirme_editar_episodio);
            exit();
        }
    }else {
        $resposta = [
            'title' => 'Erro!',
            'msg' => 'Funçao nao encontrada!',
            'icon' => 'error'
        ];
        echo json_encode($resposta);
        exit();
    }
}

if (isset($_POST['detetar_temporada'])) {
    $Temporada = isset($_POST["detetar_temporada"]) ? htmlspecialchars($_POST["detetar_temporada"]) : null;
    $series_id   = isset($_POST["series_id"]) ? (int) $_POST["series_id"] : null;
    if (function_exists('detetar_temporada')) {
        $detetar_temporada = detetar_temporada($series_id, $Temporada);
        if (!$detetar_temporada){
            $resposta = [
                'title' => 'Erro!',
                'msg' => 'Erro ao deletar temporada.',
                'icon' => 'error'
            ];
            echo json_encode($resposta);
            exit();
        }else{
            echo json_encode($detetar_temporada);
            exit();
        }
    }else {
        $resposta = [
            'title' => 'Erro!',
            'msg' => 'Funçao nao encontrada!',
            'icon' => 'error'
        ];
        echo json_encode($resposta);
        exit();
    }
}

if (isset($_POST['confirme_adicionar_episodios'])) {

    $series_id   = isset($_POST["confirme_adicionar_episodios"]) ? (int) $_POST["confirme_adicionar_episodios"] : null;
    $temporada   = isset($_POST["temporada"]) ? (int) $_POST["temporada"] : null;

    if (function_exists('confirme_adicionar_episodios')) {
        $confirme_adicionar_episodios = confirme_adicionar_episodios($series_id, $temporada);
        if (!$confirme_adicionar_episodios){
            $resposta = [
                'title' => 'Erro!',
                'msg' => 'Erro ao adiconar episodio.',
                'icon' => 'error'
            ];
            echo json_encode($resposta);
            exit();
        }else{
            echo json_encode($confirme_adicionar_episodios);
            exit();
        }
    }else {

        $resposta = [
            'title' => 'Erro!',
            'msg' => 'Funçao nao encontrada!',
            'icon' => 'error'
        ];
        echo json_encode($resposta);
        exit();
    }
}

if (isset($_POST['detetar_episodio'])) {

    $series_id   = isset($_POST["series_id"]) ? (int) $_POST["series_id"] : null;
    $episodio   = isset($_POST["detetar_episodio"]) ? (int) $_POST["detetar_episodio"] : null;
    $temporada   = isset($_POST["temporada"]) ? (int) $_POST["temporada"] : null;

    if (function_exists('detetar_episodio')) {
        $detetar_episodio = detetar_episodio($series_id, $temporada, $episodio);
        if (!$detetar_episodio){
            $resposta = [
                'title' => 'Erro!',
                'msg' => 'Erro ao adiconar episodio.',
                'icon' => 'error'
            ];
            echo json_encode($resposta);
            exit();
        }else{
            echo json_encode($detetar_episodio);
            exit();
        }
    }else {

        $resposta = [
            'title' => 'Erro!',
            'msg' => 'Funçao nao encontrada!',
            'icon' => 'error'
        ];
        echo json_encode($resposta);
        exit();
    }
}