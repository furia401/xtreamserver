<?php
session_start();
require_once('./controles/db.php');
require_once('./controles/planos.php');
require_once('./controles/checkLogout.php');
header('Content-Type: application/json; charset=utf-8');

checkLogoutapi();

if (isset($_POST['edite_planos'])) {
    $id = $_POST['edite_planos']; 

    if (function_exists('edite_planos')) {

        $edite_planos = edite_planos($id);
        if (!$edite_planos){
            $resposta = [
                'title' => 'Erro!',
                'msg' => 'Erro ao editar do revendedor.',
                'icon' => 'error'
            ];
            echo json_encode($resposta);
            exit();
        }else{
            echo json_encode($edite_planos);
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

if (isset($_POST['confirme_edite_planos'])) {
    $id                  = isset($_POST["confirme_edite_planos"]) ? (int) $_POST["confirme_edite_planos"] : null;

    $nome                = isset($_POST["nome"]) ? htmlspecialchars($_POST["nome"]) : null;
    $valor = isset($_POST["valor"]) ? htmlspecialchars($_POST["valor"]) : null;

    $custo_por_credito   = isset($_POST["custo_por_credito"]) ? htmlspecialchars($_POST["custo_por_credito"]) : null;
    

    $id = preg_replace("/[^a-zA-Z0-9]/", "", $id);
    $nome = preg_replace("/[^a-zA-Z0-9 ]/", "", $nome);
    $valor = str_replace(',', '.', $valor);
    $valor = preg_replace("/[^a-zA-Z0-9.] /", "", $valor);
    $custo_por_credito = str_replace(',', '.', $custo_por_credito);
    $custo_por_credito = preg_replace("/[^a-zA-Z0-9.]/", "", $custo_por_credito);

    if (function_exists('confirme_edite_planos')) {

        $confirme_edite_planos = confirme_edite_planos($id, $nome, $valor, $custo_por_credito);
        if (!$confirme_edite_planos){
            $resposta = [
                'title' => 'Erro!',
                'msg' => 'Erro ao editar revendedor.',
                'icon' => 'error'
            ];
            echo json_encode($resposta);
            exit();
        }else{
            echo json_encode($confirme_edite_planos);
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

if (isset($_POST['delete_plano'])) {
    $id = $_POST['delete_plano'];
    $nome = isset($_POST['nome']) ? $_POST['nome'] : null;

    if (function_exists('delete_plano')) {
        $delete_plano = delete_plano($id, $nome);
        if (!$delete_plano){
            $resposta = [
                'title' => 'Erro!',
                'msg' => 'Erro ao deletar plano.',
                'icon' => 'error'
            ];
            echo json_encode($resposta);
            exit();
        }else{
            echo json_encode($delete_plano);
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

if (isset($_POST['confirme_delete_plano'])) {
    $id = $_POST['confirme_delete_plano'];
    $nome = isset($_POST['nome']) ? $_POST['nome'] : null;

    if (function_exists('confirme_delete_plano')) {
        $confirme_delete_plano = confirme_delete_plano($id, $nome);
        if (!$confirme_delete_plano){
            $resposta = [
                'title' => 'Erro!',
                'msg' => 'Erro ao deletar Plano.',
                'icon' => 'error'
            ];
            echo json_encode($resposta);
            exit();
        }else{
            echo json_encode($confirme_delete_plano);
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
if (isset($_POST['adicionar_planos'])) {

    if (function_exists('adicionar_planos')) {
        $adicionar_planos = adicionar_planos();
        if (!$adicionar_planos){
            $resposta = [
                'title' => 'Erro!',
                'msg' => 'Erro ao adiconar plano.',
                'icon' => 'error'
            ];
            echo json_encode($resposta);
            exit();
        }else{
            echo json_encode($adicionar_planos);
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

if (isset($_POST['confirme_adicionar_plano'])) {

    $nome                = isset($_POST["nome"]) ? htmlspecialchars($_POST["nome"]) : null;
    $valor = isset($_POST["valor"]) ? htmlspecialchars($_POST["valor"]) : null;
    if (isset($_POST["valor_primeiro_mes"]) && $_POST["valor_primeiro_mes"] !== "") {
        $valor_primeiro_mes = htmlspecialchars($_POST["valor_primeiro_mes"]);
    } else {
        $valor_primeiro_mes = $valor; 
    }

    $custo_por_credito   = isset($_POST["custo_por_credito"]) ? htmlspecialchars($_POST["custo_por_credito"]) : null;

    $nome = preg_replace("/[^a-zA-Z0-9 ]/", "", $nome);
    $valor = preg_replace("/[^a-zA-Z0-9.] /", "", $valor);
    $custo_por_credito = preg_replace("/[^a-zA-Z0-9.]/", "", $custo_por_credito);

    if (function_exists('confirme_adicionar_plano')) {

        $confirme_adicionar_plano = confirme_adicionar_plano($nome, $valor, $custo_por_credito);
        if (!$confirme_adicionar_plano){
            $resposta = [
                'title' => 'Erro!',
                'msg' => 'Erro ao editar revendedor.',
                'icon' => 'error'
            ];
            echo json_encode($resposta);
            exit();
        }else{
            echo json_encode($confirme_adicionar_plano);
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

if (isset($_POST['mover_clientes_plano'])) {
    $id = $_POST['mover_clientes_plano']; 

    if (function_exists('mover_clientes_plano')) {

        $mover_clientes_plano = mover_clientes_plano($id);
        if (!$mover_clientes_plano){
            $resposta = [
                'title' => 'Erro!',
                'msg' => 'Erro ao mover clientes do plano.',
                'icon' => 'error'
            ];
            echo json_encode($resposta);
            exit();
        }else{
            echo json_encode($mover_clientes_plano);
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

if (isset($_POST['confirme_mover_clientes_plano'])) {
    $plano_atual = $_POST['confirme_mover_clientes_plano'];
    $plano = isset($_POST['plano']) ? $_POST['plano'] : null;

    if (function_exists('confirme_mover_clientes_plano')) {
        $confirme_mover_clientes_plano = confirme_mover_clientes_plano($plano_atual, $plano);
        if (!$confirme_mover_clientes_plano){
            $resposta = [
                'title' => 'Erro!',
                'msg' => 'Erro na funçao mudar cliente de plano.',
                'icon' => 'error'
            ];
            echo json_encode($resposta);
            exit();
        }else{
            echo json_encode($confirme_mover_clientes_plano);
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