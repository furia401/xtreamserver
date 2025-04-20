<?php
session_start();
require_once('./controles/db.php');
require_once('./controles/categorias.php');
require_once('./controles/checkLogout.php');
header('Content-Type: application/json; charset=utf-8');

checkLogoutapi();
if (isset($_POST['delete_tudo'])) {
    $tabelas          = isset($_POST["delete_tudo"]) ? htmlspecialchars($_POST["delete_tudo"]) : "tudo";
    if (function_exists('delete_tudo')) {
        $delete_tudo = delete_tudo($tabelas);
        if (!$delete_tudo){
            $resposta = [
                'title' => 'Erro!',
                'msg' => 'Erro ao Funçao deletar tudo.',
                'icon' => 'error'
            ];
            echo json_encode($resposta);
            exit();
        }else{
            echo json_encode($delete_tudo);
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

if (isset($_POST['add_categoria'])) {

    if (function_exists('add_categoria')) {
        $add_categoria = add_categoria();
        if (!$add_categoria){
            $resposta = [
                'title' => 'Erro!',
                'msg' => 'Erro ao Funçao add categoria.',
                'icon' => 'error'
            ];
            echo json_encode($resposta);
            exit();
        }else{
            echo json_encode($add_categoria);
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

if (isset($_POST['confirme_add_categoria'])) {
    if (empty($_POST['nome'])) {
        $resposta = [
            'title' => 'Erro!',
            'msg' => 'É necessário preencher o campo nome.',
            'icon' => 'error'
        ];
        echo json_encode($resposta);
        exit();
    }

    $category_name          = isset($_POST["nome"]) ? htmlspecialchars($_POST["nome"]) : null;
    $type                   = isset($_POST["tipo"]) ? htmlspecialchars($_POST["tipo"]) : null;
    $is_adult               = isset($_POST["adulto"]) ? (int) $_POST["adulto"] : 0;
    $bg                     = isset($_POST["gb_ssiptv"]) ? htmlspecialchars($_POST["gb_ssiptv"]) : null;

    if (function_exists('confirme_add_categoria')) {
        $confirme_add_categoria = confirme_add_categoria($category_name, $type, $is_adult, $bg);
        if (!$confirme_add_categoria){
            $resposta = [
                'title' => 'Erro!',
                'msg' => 'Erro ao adicionar categoria.',
                'icon' => 'error'
            ];
            echo json_encode($resposta);
            exit();
        }else{
            echo json_encode($confirme_add_categoria);
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

if (isset($_POST['delete_categorias'])) {
    $id = $_POST['delete_categorias'];
    $name = isset($_POST['name']) ? $_POST['name'] : null;

    if (function_exists('delete_categorias')) {
        $delete_categorias = delete_categorias($id, $name);
        if (!$delete_categorias){
            $resposta = [
                'title' => 'Erro!',
                'msg' => 'Erro ao deletar categoria.',
                'icon' => 'error'
            ];
            echo json_encode($resposta);
            exit();
        }else{
            echo json_encode($delete_categorias);
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

if (isset($_POST['confirme_delete_categorias'])) {
    $id = isset($_POST['confirme_delete_categorias']) ? (int) $_POST['confirme_delete_categorias'] : null;
    $name = isset($_POST['name']) ? (int) $_POST['name'] : null;

    if (function_exists('confirme_delete_categorias')) {
        $delete_categorias = confirme_delete_categorias($id, $name);
        if (!$delete_categorias){
            $resposta = [
                'title' => 'Erro!',
                'msg' => 'Erro ao deletar categoria.',
                'icon' => 'error'
            ];
            echo json_encode($resposta);
            exit();
        }else{
            echo json_encode($delete_categorias);
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

if (isset($_POST['edite_categorias'])) {
    $id = $_POST['edite_categorias'];

    if (function_exists('edite_categorias')) {

        $edite_categorias = edite_categorias($id);
        if (!$edite_categorias){
            $resposta = [
                'title' => 'Erro!',
                'msg' => 'Erro ao buscar info do categoria.',
                'icon' => 'error'
            ];
            echo json_encode($resposta);
            exit();
        }else{
            echo json_encode($edite_categorias);
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

if (isset($_POST['confirme_editar_categoria'])) {
    if (empty($_POST['nome'])) {
        $resposta = [
            'title' => 'Erro!',
            'msg' => 'É necessário preencher o campo nome.',
            'icon' => 'error'
        ];
        echo json_encode($resposta);
        exit();
    }

    $id            = isset($_POST["confirme_editar_categoria"]) ? (int) $_POST["confirme_editar_categoria"] : 0;
    $category_name = isset($_POST["nome"]) ? htmlspecialchars($_POST["nome"]) : null;
    $type          = isset($_POST["tipo"]) ? htmlspecialchars($_POST["tipo"]) : null;
    $is_adult      = isset($_POST["adulto"]) ? (int) $_POST["adulto"] : 0;
    $bg            = isset($_POST["gb_ssiptv"]) ? htmlspecialchars($_POST["gb_ssiptv"]) : null;

    if (function_exists('confirme_editar_categoria')) {
        $confirme_editar_categoria = confirme_editar_categoria($id, $category_name, $type, $is_adult, $bg);
        if (!$confirme_editar_categoria){
            $resposta = [
                'title' => 'Erro!',
                'msg' => 'Erro ao editar categoria.',
                'icon' => 'error'
            ];
            echo json_encode($resposta);
            exit();
        }else{
            echo json_encode($confirme_editar_categoria);
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