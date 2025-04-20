<?php
session_start();
require_once('./controles/db.php');
require_once('./controles/revendedores.php');
require_once('./controles/checkLogout.php');
header('Content-Type: application/json; charset=utf-8');

checkLogoutapi();

if (isset($_POST['edite_admin'])) { 

    if (function_exists('edite_admin')) {

        $edite_admin = edite_admin();
        if (!$edite_admin){
            $resposta = [
                'title' => 'Erro!',
                'msg' => 'Erro ao editar ADMIN.',
                'icon' => 'error'
            ];
            echo json_encode($resposta);
            exit();
        }else{
            echo json_encode($edite_admin);
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

if (isset($_POST['confirme_edite_admin'])) {
    
    $usuario        = isset($_POST["usuario"]) ? htmlspecialchars($_POST["usuario"]) : null;
    $senha          = isset($_POST["senha"]) ? htmlspecialchars($_POST["senha"]) : null;
    $tipo_link          = isset($_POST["tipo_link"]) ? htmlspecialchars($_POST["tipo_link"]) : null;


    if (function_exists('confirme_edite_admin')) {

        $confirme_edite_admin = confirme_edite_admin($usuario, $senha, $tipo_link);
        if (!$confirme_edite_admin){
            $resposta = [
                'title' => 'Erro!',
                'msg' => 'Erro ao editar Admin.',
                'icon' => 'error'
            ];
            echo json_encode($resposta);
            exit();
        }else{
            echo json_encode($confirme_edite_admin);
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

if (isset($_POST['edite_admin_revenda'])) { 

    if (function_exists('edite_admin_revenda')) {

        $edite_admin_revenda = edite_admin_revenda();
        if (!$edite_admin_revenda){
            $resposta = [
                'title' => 'Erro!',
                'msg' => 'Erro ao editar Revenda.',
                'icon' => 'error'
            ];
            echo json_encode($resposta);
            exit();
        }else{
            echo json_encode($edite_admin_revenda);
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

if (isset($_POST['confirme_edite_admin_revenda'])) {
    
    $senha_atual        = isset($_POST["senha_atual"]) ? htmlspecialchars($_POST["senha_atual"]) : null;
    $nova_senha          = isset($_POST["nova_senha"]) ? htmlspecialchars($_POST["nova_senha"]) : null;
    $confime_senha          = isset($_POST["confime_senha"]) ? htmlspecialchars($_POST["confime_senha"]) : null;

    if ($nova_senha != $confime_senha) {
        $resposta = [
            'title' => 'Erro!',
            'msg' => 'As senha digitadas não estão iguais',
            'icon' => 'error'
        ];
        echo json_encode($resposta);
        exit();
    }


    if (function_exists('confirme_edite_admin_revenda')) {

        $confirme_edite_admin_revenda = confirme_edite_admin_revenda($senha_atual, $nova_senha, $confime_senha);
        if (!$confirme_edite_admin_revenda){
            $resposta = [
                'title' => 'Erro!',
                'msg' => 'Erro ao editar Admin.',
                'icon' => 'error'
            ];
            echo json_encode($resposta);
            exit();
        }else{
            echo json_encode($confirme_edite_admin_revenda);
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

if (isset($_POST['edite_revendedor'])) {
    $id = $_POST['edite_revendedor']; 

    if (function_exists('edite_revendedor')) {

        $edite_revendedor = edite_revendedor($id);
        if (!$edite_revendedor){
            $resposta = [
                'title' => 'Erro!',
                'msg' => 'Erro ao editar do revendedor.',
                'icon' => 'error'
            ];
            echo json_encode($resposta);
            exit();
        }else{
            echo json_encode($edite_revendedor);
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

if (isset($_POST['confirme_edite_revendedor'])) {
    
    $id             = isset($_POST["confirme_edite_revendedor"]) ? (int) $_POST["confirme_edite_revendedor"] : null;
    $usuario        = isset($_POST["usuario"]) ? htmlspecialchars($_POST["usuario"]) : null;
    $senha          = isset($_POST["senha"]) ? htmlspecialchars($_POST["senha"]) : null;
    $plano          = isset($_POST["plano"]) ? (int) $_POST["plano"] : null;


    if (function_exists('confirme_editar_revendedor')) {

        $confirme_editar_revendedor = confirme_editar_revendedor($id, $usuario, $senha, $plano);
        if (!$confirme_editar_revendedor){
            $resposta = [
                'title' => 'Erro!',
                'msg' => 'Erro ao editar revendedor.',
                'icon' => 'error'
            ];
            echo json_encode($resposta);
            exit();
        }else{
            echo json_encode($confirme_editar_revendedor);
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

if (isset($_POST['adicionar_creditos'])) {
    $id = $_POST['adicionar_creditos'];
    $usuario = isset($_POST['usuario']) ? $_POST['usuario'] : null;

    if (function_exists('add_creditos')) {
        $add_creditos = add_creditos($id, $usuario);
        if (!$add_creditos){
            $resposta = [
                'title' => 'Erro!',
                'msg' => 'Erro ao adicionar creditos.',
                'icon' => 'error'
            ];
            echo json_encode($resposta);
            exit();
        }else{
            echo json_encode($add_creditos);
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

if (isset($_POST['confirme_add_creditos'])) {
    $id = $_POST['confirme_add_creditos'];
    $creditos = min(intval($_POST["creditos"] ?? '1'), 1000); 
    $creditos = min(max(intval($_POST["creditos"] ?? 1), -50), 1000); 

    if (function_exists('confirme_add_creditos')) {
        $confirme_add_creditos = confirme_add_creditos($id, $creditos);
        if (!$confirme_add_creditos){
            $resposta = [
                'title' => 'Erro!',
                'msg' => 'Erro ao adicionar Creditos.',
                'icon' => 'error'
            ];
            echo json_encode($resposta);
            exit();
        }else{
            echo json_encode($confirme_add_creditos);
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

if (isset($_POST['add_revendedor'])) {

    if (function_exists('add_revendedor')) {
        $add_revendedor = add_revendedor();
        if (!$add_revendedor){
            $resposta = [
                'title' => 'Erro!',
                'msg' => 'Erro ao Funçao add_revendedor.',
                'icon' => 'error'
            ];
            echo json_encode($resposta);
            exit();
        }else{
            echo json_encode($add_revendedor);
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

if (isset($_POST['confirme_add_revendedor'])) {
    if (empty($_POST['usuario'])) {
            $resposta = [
                'title' => 'Erro!',
                'msg' => 'É necessário preencher o campo usuário.',
                'icon' => 'error'
            ];
            echo json_encode($resposta);
            exit();
        }elseif (empty($_POST['senha'])) {
            $resposta = [
                'title' => 'Erro!',
                'msg' => 'É necessário preencher o campo senha.',
                'icon' => 'error'
            ];
            echo json_encode($resposta);
            exit();
        }
    $usuario        = isset($_POST["usuario"]) ? htmlspecialchars($_POST["usuario"]) : null;
    $senha          = isset($_POST["senha"]) ? htmlspecialchars($_POST["senha"]) : null;
    $plano          = isset($_POST["plano"]) ? (int) $_POST["plano"] : null;

    if (function_exists('confirme_add_revendedor')) {
        $confirme_add_revendedor = confirme_add_revendedor($usuario, $senha, $plano);
        if (!$confirme_add_revendedor){
            $resposta = [
                'title' => 'Erro!',
                'msg' => 'Erro ao adicionar revendedor.',
                'icon' => 'error'
            ];
            echo json_encode($resposta);
            exit();
        }else{
            echo json_encode($confirme_add_revendedor);
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

if (isset($_POST['delete_revendedor'])) {
    $id = $_POST['delete_revendedor'];
    $usuario = isset($_POST['usuario']) ? $_POST['usuario'] : null;

    if (function_exists('delete_revendedor')) {
        $delete_revendedor = delete_revendedor($id, $usuario);
        if (!$delete_revendedor){
            $resposta = [
                'title' => 'Erro!',
                'msg' => 'Erro ao na funçao deletar revendedor.',
                'icon' => 'error'
            ];
            echo json_encode($resposta);
            exit();
        }else{
            echo json_encode($delete_revendedor);
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

if (isset($_POST['confirme_delete_revendedor'])) {
    $id = $_POST['confirme_delete_revendedor'];
    $usuario = isset($_POST['usuario']) ? $_POST['usuario'] : null;

    if (function_exists('confirme_delete_revendedor')) {
        $confirme_delete_revendedor = confirme_delete_revendedor($id, $usuario);
        if (!$confirme_delete_revendedor){
            $resposta = [
                'title' => 'Erro!',
                'msg' => 'Erro ao deletar revendedor.',
                'icon' => 'error'
            ];
            echo json_encode($resposta);
            exit();
        }else{
            echo json_encode($confirme_delete_revendedor);
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