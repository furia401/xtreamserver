<?php
session_start();
require_once('./controles/db.php');
require_once('./controles/testes.php');
require_once('./controles/checkLogout.php');
header('Content-Type: application/json; charset=utf-8');

checkLogoutapi();

if (isset($_POST['info_cliente'])) {
    $id = $_POST['info_cliente'];

    if (function_exists('info_cliente')) {

        $info_cliente = info_cliente($id);
        if (!$info_cliente){
            $resposta = [
                'title' => 'Erro!',
                'msg' => 'Erro ao buscar info do cliente.',
                'icon' => 'error'
            ];
            echo json_encode($resposta);
            exit();
        }else{
            echo json_encode($info_cliente);
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

if (isset($_POST['add_tempo'])) {
    $id = $_POST['add_tempo'];
    $usuario = isset($_POST['usuario']) ? $_POST['usuario'] : null;

    if (function_exists('add_tempo')) {
        $add_tempo = add_tempo($id, $usuario);
        if (!$add_tempo){
            $resposta = [
                'title' => 'Erro!',
                'msg' => 'Erro ao renovar cliente.',
                'icon' => 'error'
            ];
            echo json_encode($resposta);
            exit();
        }else{
            echo json_encode($add_tempo);
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

if (isset($_POST['confirme_add_tempo_testes'])) {
    $id = $_POST['confirme_add_tempo_testes'];
    $tempo = min(intval($_POST["tempo"] ?? '1'), 72); 
    $tempo = min(max(intval($_POST["tempo"] ?? 1), -72), 72); 

    if (function_exists('confirme_add_tempo_testes')) {
        $confirme_add_tempo_testes = confirme_add_tempo_testes($id, $tempo);
        if (!$confirme_add_tempo_testes){
            $resposta = [
                'title' => 'Erro!',
                'msg' => 'Erro ao adicionar Tempo.',
                'icon' => 'error'
            ];
            echo json_encode($resposta);
            exit();
        }else{
            echo json_encode($confirme_add_tempo_testes);
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

if (isset($_POST['ativar_teste'])) {
    $id = $_POST['ativar_teste'];
    $usuario = isset($_POST['usuario']) ? $_POST['usuario'] : null;

    if (function_exists('ativar_teste')) {
        $ativar_teste = ativar_teste($id, $usuario);
        if (!$ativar_teste){
            $resposta = [
                'title' => 'Erro!',
                'msg' => 'Erro ao renovar cliente.',
                'icon' => 'error'
            ];
            echo json_encode($resposta);
            exit();
        }else{
            echo json_encode($ativar_teste);
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

if (isset($_POST['confirme_ativar_teste'])) {
    $id = $_POST['confirme_ativar_teste'];
    $ativar_meses = isset($_POST['ativar_meses']) ? $_POST['ativar_meses'] : null;

    if (function_exists('confirme_ativar_teste')) {
        $confirme_ativar_teste = confirme_ativar_teste($id, $ativar_meses);
        if (!$confirme_ativar_teste){
            $resposta = [
                'title' => 'Erro!',
                'msg' => 'Erro ao renovar cliente.',
                'icon' => 'error'
            ];
            echo json_encode($resposta);
            exit();
        }else{
            echo json_encode($confirme_ativar_teste);
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

if (isset($_POST['delete_cliente'])) {
    $id = $_POST['delete_cliente'];
    $usuario = isset($_POST['usuario']) ? $_POST['usuario'] : null;

    if (function_exists('delete_cliente')) {
        $delete_cliente = delete_cliente($id, $usuario);
        if (!$delete_cliente){
            $resposta = [
                'title' => 'Erro!',
                'msg' => 'Erro ao deletar cliente.',
                'icon' => 'error'
            ];
            echo json_encode($resposta);
            exit();
        }else{
            echo json_encode($delete_cliente);
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

if (isset($_POST['confirme_delete_cliente'])) {
    $id = $_POST['confirme_delete_cliente'];
    $usuario = isset($_POST['usuario']) ? $_POST['usuario'] : null;

    if (function_exists('confirme_delete_cliente')) {
        $delete_cliente = confirme_delete_cliente($id, $usuario);
        if (!$delete_cliente){
            $resposta = [
                'title' => 'Erro!',
                'msg' => 'Erro ao deletar cliente.',
                'icon' => 'error'
            ];
            echo json_encode($resposta);
            exit();
        }else{
            echo json_encode($delete_cliente);
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

if (isset($_POST['adicionar_testes'])) {

    if (function_exists('adicionar_testes')) {

        $adicionar_testes = adicionar_testes();
        if (!$adicionar_testes){
            $resposta = [
                'title' => 'Erro!',
                'msg' => 'Erro ao buscar info do cliente.',
                'icon' => 'error'
            ];
            echo json_encode($resposta);
            exit();
        }else{
            echo json_encode($adicionar_testes);
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

if (isset($_POST['confirme_adicionar_testes'])) {

    $name               = isset($_POST["name"]) ? htmlspecialchars($_POST["name"]) : '';
    $usuario            = isset($_POST["usuario"]) ? htmlspecialchars($_POST["usuario"]) : null;
    $senha              = isset($_POST["senha"]) ? htmlspecialchars($_POST["senha"]) : null;
    $adulto             = isset($_POST["adulto"]) ? (int) $_POST["adulto"] : null;
    $plano              = isset($_POST["plano"]) ? (int) $_POST["plano"] : null;
    $Dispositivo        = isset($_POST["dispositivo"]) ? htmlspecialchars($_POST["dispositivo"]) : '';
    $App                = isset($_POST["app"]) ? htmlspecialchars($_POST["app"]) : '';
    $Forma_de_pagamento = isset($_POST["forma-de-pagamento"]) ? htmlspecialchars($_POST["forma-de-pagamento"]) : '';
    $nome_do_pagador    = isset($_POST["nome-do-pagador"]) ? htmlspecialchars($_POST["nome-do-pagador"]) : '';
    $Whatsapp           = isset($_POST["whatsapp"]) ? htmlspecialchars($_POST["whatsapp"]) : '';
    $indicacao          = isset($_POST["indicacao"]) ? (int) $_POST["indicacao"] : null;
    $tempo        = isset($_POST["tempo"]) ? (int) $_POST["tempo"] : "1";

    if ($usuario && $senha) {
    }else{
        $resposta = [
            'title' => 'Erro!',
            'msg' => 'O usuario ou senha nao pode esta vazio',
            'icon' => 'error'
        ];
        echo json_encode($resposta);
        exit();
    }

    if (isset($_POST["device_mac"])) {

        if ($_POST["device_mac"] !== '') {
            $mac = htmlspecialchars($_POST["device_mac"]);
        } else {

            $mac = 'vazio';
        }
    } else {

        $mac = 'não está presente';
    }
    if (isset($_POST["device_key"])) {

        if ($_POST["device_key"] !== '') {
            $key = htmlspecialchars($_POST["device_key"]);
        } else {

            $key = 'vazio';
        }
    } else {

        $key = 'não está presente';
    }

    if (function_exists('confirme_adicionar_testes')) {
        $confirme_adicionar_testes = confirme_adicionar_testes($name, $usuario, $senha, $adulto, $plano, $Dispositivo, $App, $Forma_de_pagamento, $nome_do_pagador, $Whatsapp, $indicacao, $mac, $key, $tempo);
        if (!$confirme_adicionar_testes){
            $resposta = [
                'title' => 'Erro!',
                'msg' => 'Erro na funçao mudar servidor.',
                'icon' => 'error'
            ];
            echo json_encode($resposta);
            exit();
        }else{
            echo json_encode($confirme_adicionar_testes);
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