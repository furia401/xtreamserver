<?php
session_start();
require_once('./controles/db.php');
require_once('./controles/filmes.php');
require_once('./controles/checkLogout.php');
header('Content-Type: application/json; charset=utf-8');

checkLogoutapi();

if (isset($_POST['editar_filmes'])) {
    $id = $_POST['editar_filmes'];

    if (function_exists('editar_filmes')) {

        $editar_filmes = editar_filmes($id);
        if (!$editar_filmes){
            $resposta = [
                'title' => 'Erro!',
                'msg' => 'Erro ao buscar info do cliente.',
                'icon' => 'error'
            ];
            echo json_encode($resposta);
            exit();
        }else{
            echo json_encode($editar_filmes);
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

if (isset($_POST['confirme_editar_filmes'])) {

    $nome               = isset($_POST["nome"]) ? htmlspecialchars($_POST["nome"]) : '';
    $link            = isset($_POST["link"]) ? htmlspecialchars($_POST["link"]) : null;
    $logo              = isset($_POST["logo"]) ? htmlspecialchars($_POST["logo"]) : null;
    $Categoria             = isset($_POST["Categoria"]) ? (int) $_POST["Categoria"] : null;
    $adulto              = isset($_POST["adulto"]) ? (int) $_POST["adulto"] : '0';
    $id              = isset($_POST["confirme_editar_filmes"]) ? (int) $_POST["confirme_editar_filmes"] : null;
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



    if (function_exists('confirme_editar_filmes')) {
        $editar_filmes = confirme_editar_filmes($id, $nome, $link, $logo, $Categoria, $adulto, $director, $genre, $actors, $plot, $backdrop_path, $releasedate, $duration, $rating, $rating_5based, $year);
        if (!$editar_filmes){
            $resposta = [
                'title' => 'Erro!',
                'msg' => 'Erro ao renovar cliente.',
                'icon' => 'error'
            ];
            echo json_encode($resposta);
            exit();
        }else{
            echo json_encode($editar_filmes);
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

if (isset($_POST['delete_filmes'])) {
    $name = isset($_POST["name"]) ? htmlspecialchars($_POST["name"]) : null;
    $id   = isset($_POST["delete_filmes"]) ? (int) $_POST["delete_filmes"] : null;
    if (function_exists('delete_filmes')) {
        $delete_filmes = delete_filmes($id, $name);
        if (!$delete_filmes){
            $resposta = [
                'title' => 'Erro!',
                'msg' => 'Erro ao deletar cliente.',
                'icon' => 'error'
            ];
            echo json_encode($resposta);
            exit();
        }else{
            echo json_encode($delete_filmes);
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

if (isset($_POST['confirme_delete_filmes'])) {
    $id = $_POST['confirme_delete_filmes'];
    $nome = isset($_POST['nome']) ? $_POST['nome'] : null;

    if (function_exists('confirme_delete_filmes')) {
        $delete_filmes = confirme_delete_filmes($id, $nome);
        if (!$delete_filmes){
            $resposta = [
                'title' => 'Erro!',
                'msg' => 'Erro ao deletar canal.',
                'icon' => 'error'
            ];
            echo json_encode($resposta);
            exit();
        }else{
            echo json_encode($delete_filmes);
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

if (isset($_POST['adicionar_filmes'])) {

    if (function_exists('adicionar_filmes')) {

        $adicionar_filmes = adicionar_filmes();
        if (!$adicionar_filmes){
            $resposta = [
                'title' => 'Erro!',
                'msg' => 'Erro ao buscar info do cliente.',
                'icon' => 'error'
            ];
            echo json_encode($resposta);
            exit();
        }else{
            echo json_encode($adicionar_filmes);
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

if (isset($_POST['confirme_adicionar_filmes'])) {

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

    if (function_exists('confirme_adicionar_filmes')) {
        $confirme_adicionar_filmes = confirme_adicionar_filmes($nome, $link, $logo, $Categoria, $adulto, $director, $genre, $actors, $plot, $backdrop_path, $releasedate, $duration, $rating, $rating_5based, $year);
        if (!$confirme_adicionar_filmes){
            $resposta = [
                'title' => 'Erro!',
                'msg' => 'Erro na funçao mudar servidor.',
                'icon' => 'error'
            ];
            echo json_encode($resposta);
            exit();
        }else{
            echo json_encode($confirme_adicionar_filmes);
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