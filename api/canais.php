<?php
session_start();
require_once('./controles/db.php');
require_once('./controles/canais.php');
require_once('./controles/checkLogout.php');
header('Content-Type: application/json; charset=utf-8');

checkLogoutapi();

if (isset($_POST['editar_canal'])) {
    $id = $_POST['editar_canal'];

    if (function_exists('editar_canal')) {

        $editar_canal = editar_canal($id);
        if (!$editar_canal){
            $resposta = [
                'title' => 'Erro!',
                'msg' => 'Erro ao buscar info do cliente.',
                'icon' => 'error'
            ];
            echo json_encode($resposta);
            exit();
        }else{
            echo json_encode($editar_canal);
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

if (isset($_POST['confirme_editar_canal'])) {

    $nome               = isset($_POST["nome"]) ? htmlspecialchars($_POST["nome"]) : '';
    $link            = isset($_POST["link"]) ? htmlspecialchars($_POST["link"]) : null;
    $logo              = isset($_POST["logo"]) ? htmlspecialchars($_POST["logo"]) : null;
    $Categoria             = isset($_POST["Categoria"]) ? (int) $_POST["Categoria"] : null;
    $adulto              = isset($_POST["adulto"]) ? (int) $_POST["adulto"] : '0';
    $id              = isset($_POST["confirme_editar_canal"]) ? (int) $_POST["confirme_editar_canal"] : null;
    $tipo_link              = isset($_POST["tipo_link"]) ? htmlspecialchars($_POST["tipo_link"]) : null;

    if (function_exists('confirme_editar_canal')) {
        $editar_canal = confirme_editar_canal($id, $nome, $link, $logo, $Categoria, $adulto, $tipo_link);
        if (!$editar_canal){
            $resposta = [
                'title' => 'Erro!',
                'msg' => 'Erro ao renovar cliente.',
                'icon' => 'error'
            ];
            echo json_encode($resposta);
            exit();
        }else{
            echo json_encode($editar_canal);
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

if (isset($_POST['delete_canal'])) {
    $name = isset($_POST["name"]) ? htmlspecialchars($_POST["name"]) : null;
    $id   = isset($_POST["delete_canal"]) ? (int) $_POST["delete_canal"] : null;
    if (function_exists('delete_canal')) {
        $delete_canal = delete_canal($id, $name);
        if (!$delete_canal){
            $resposta = [
                'title' => 'Erro!',
                'msg' => 'Erro ao deletar cliente.',
                'icon' => 'error'
            ];
            echo json_encode($resposta);
            exit();
        }else{
            echo json_encode($delete_canal);
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

if (isset($_POST['confirme_delete_canal'])) {
    $id = $_POST['confirme_delete_canal'];
    $nome = isset($_POST['nome']) ? $_POST['nome'] : null;

    if (function_exists('confirme_delete_canal')) {
        $delete_canal = confirme_delete_canal($id, $nome);
        if (!$delete_canal){
            $resposta = [
                'title' => 'Erro!',
                'msg' => 'Erro ao deletar canal.',
                'icon' => 'error'
            ];
            echo json_encode($resposta);
            exit();
        }else{
            echo json_encode($delete_canal);
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

if (isset($_POST['adicionar_canal'])) {

    if (function_exists('adicionar_canal')) {

        $adicionar_canal = adicionar_canal();
        if (!$adicionar_canal){
            $resposta = [
                'title' => 'Erro!',
                'msg' => 'Erro ao buscar info do cliente.',
                'icon' => 'error'
            ];
            echo json_encode($resposta);
            exit();
        }else{
            echo json_encode($adicionar_canal);
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

if (isset($_POST['confirme_adicionar_canal'])) {

    $nome               = isset($_POST["nome"]) ? htmlspecialchars($_POST["nome"]) : '';
    $link            = isset($_POST["link"]) ? htmlspecialchars($_POST["link"]) : null;
    $logo              = isset($_POST["logo"]) ? htmlspecialchars($_POST["logo"]) : null;
    $Categoria             = isset($_POST["Categoria"]) ? (int) $_POST["Categoria"] : null;
    $adulto              = isset($_POST["adulto"]) ? (int) $_POST["adulto"] : '0';

    if (function_exists('confirme_adicionar_canal')) {
        $confirme_adicionar_canal = confirme_adicionar_canal($nome, $link, $logo, $Categoria, $adulto);
        if (!$confirme_adicionar_canal){
            $resposta = [
                'title' => 'Erro!',
                'msg' => 'Erro na funçao mudar servidor.',
                'icon' => 'error'
            ];
            echo json_encode($resposta);
            exit();
        }else{
            echo json_encode($confirme_adicionar_canal);
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