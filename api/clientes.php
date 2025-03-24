<?php
session_start();
require_once('./controles/db.php');
require_once('./controles/clientes.php');
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

if (isset($_POST['edite_cliente'])) {
    $id = $_POST['edite_cliente'];

    if (function_exists('edite_cliente')) {

        $edite_cliente = edite_cliente($id);
        if (!$edite_cliente){
            $resposta = [
                'title' => 'Erro!',
                'msg' => 'Erro ao buscar info do cliente.',
                'icon' => 'error'
            ];
            echo json_encode($resposta);
            exit();
        }else{
            echo json_encode($edite_cliente);
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

if (isset($_POST['confirme_edite_cliente'])) {

    $id                 = isset($_POST["id"]) ? (int) $_POST["id"] : null; 
    $name               = isset($_POST["name"]) ? htmlspecialchars($_POST["name"]) : '';
    $c_id               = isset($_POST["id_c"]) ? (int) $_POST["id_c"] : null;
    $usuario            = isset($_POST["usuario"]) ? htmlspecialchars($_POST["usuario"]) : '';
    $senha              = isset($_POST["senha"]) ? htmlspecialchars($_POST["senha"]) : '';
    $fonte              = isset($_POST["fonte"]) ? htmlspecialchars($_POST["fonte"]) : null;
    $usuario0           = isset($_POST["usuario0"]) ? htmlspecialchars($_POST["usuario0"]) : '';
    $senha0             = isset($_POST["senha0"]) ? htmlspecialchars($_POST["senha0"]) : '';
    $adulto             = isset($_POST["adulto"]) ? (int) $_POST["adulto"] : null;
    $plano              = isset($_POST["plano"]) ? (int) $_POST["plano"] : null;
    $Dispositivo        = isset($_POST["dispositivo"]) ? htmlspecialchars($_POST["dispositivo"]) : '';
    $App                = isset($_POST["app"]) ? htmlspecialchars($_POST["app"]) : '';
    $Forma_de_pagamento = isset($_POST["forma-de-pagamento"]) ? htmlspecialchars($_POST["forma-de-pagamento"]) : '';
    $nome_do_pagador    = isset($_POST["nome-do-pagador"]) ? htmlspecialchars($_POST["nome-do-pagador"]) : '';
    $Whatsapp           = isset($_POST["whatsapp"]) ? htmlspecialchars($_POST["whatsapp"]) : '';
    $indicacao          = isset($_POST["indicacao"]) ? (int) $_POST["indicacao"] : null;

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
    if (isset($_POST["email_app"])) {

        if ($_POST["email_app"] !== '') {
            $email_app = htmlspecialchars($_POST["email_app"]);
        } else {

            $email_app = 'vazio';
        }
    } else {

        $email_app = 'não está presente';
    }
    if (isset($_POST["senha_app"])) {

        if ($_POST["senha_app"] !== '') {
            $senha_app = htmlspecialchars($_POST["senha_app"]);
        } else {

            $senha_app = 'vazio';
        }
    } else {

        $senha_app = 'não está presente';
    }
    if (isset($_POST["data_app"])) {

        if ($_POST["data_app"] !== '') {
            $data_app = htmlspecialchars($_POST["data_app"]);
        } else {

            $data_app = 'vazio';
        }
    } else {

        $data_app = 'não está presente';
    }

    if (function_exists('confirme_edite_cliente')) {
        $edite_cliente = confirme_edite_cliente($id, $name, $usuario, $senha, $fonte, $adulto, $plano, $Dispositivo, $App, $Forma_de_pagamento, $nome_do_pagador, $Whatsapp, $indicacao, $mac, $key, $email_app, $senha_app, $data_app);
        if (!$edite_cliente){
            $resposta = [
                'title' => 'Erro!',
                'msg' => 'Erro ao renovar cliente.',
                'icon' => 'error'
            ];
            echo json_encode($resposta);
            exit();
        }else{
            echo json_encode($edite_cliente);
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

if (isset($_POST['renovar_cliente'])) {
    $id = $_POST['renovar_cliente'];
    $usuario = isset($_POST['usuario']) ? $_POST['usuario'] : null;

    if (function_exists('renovar_cliente')) {
        $renovar_cliente = renovar_cliente($id, $usuario);
        if (!$renovar_cliente){
            $resposta = [
                'title' => 'Erro!',
                'msg' => 'Erro ao renovar cliente.',
                'icon' => 'error'
            ];
            echo json_encode($resposta);
            exit();
        }else{
            echo json_encode($renovar_cliente);
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

if (isset($_POST['confirme_renovar_cliente'])) {
    $id = $_POST['confirme_renovar_cliente'];
    $meses = $_POST['meses'];

    if (function_exists('confirme_renovar_cliente')) {
        $renovar_cliente = confirme_renovar_cliente($id, $meses);
        if (!$renovar_cliente){
            $resposta = [
                'title' => 'Erro!',
                'msg' => 'Erro ao renovar cliente.',
                'icon' => 'error'
            ];
            echo json_encode($resposta);
            exit();
        }else{
            echo json_encode($renovar_cliente);
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

if (isset($_POST['confirme_add_tempo_clientes'])) {
    $id = $_POST['confirme_add_tempo_clientes'];
    $tempo = min(intval($_POST["tempo"] ?? '1'), 72); 
    $tempo = min(max(intval($_POST["tempo"] ?? 1), -72), 72); 

    if (function_exists('confirme_add_tempo_clientes')) {
        $confirme_add_tempo_clientes = confirme_add_tempo_clientes($id, $tempo);
        if (!$confirme_add_tempo_clientes){
            $resposta = [
                'title' => 'Erro!',
                'msg' => 'Erro ao adicionar Tempo.',
                'icon' => 'error'
            ];
            echo json_encode($resposta);
            exit();
        }else{
            echo json_encode($confirme_add_tempo_clientes);
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

if (isset($_POST['adicionar_clientes'])) {

    if (function_exists('adicionar_clientes')) {

        $adicionar_clientes = adicionar_clientes();
        if (!$adicionar_clientes){
            $resposta = [
                'title' => 'Erro!',
                'msg' => 'Erro ao buscar info do cliente.',
                'icon' => 'error'
            ];
            echo json_encode($resposta);
            exit();
        }else{
            echo json_encode($adicionar_clientes);
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

if (isset($_POST['confirme_adicionar_clientes'])) {

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
    $id_servidor        = isset($_POST["confirme_adicionar_clientes"]) ? (int) $_POST["confirme_adicionar_clientes"] : null;

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

    if (function_exists('confirme_adicionar_clientes')) {
        $confirme_adicionar_clientes = confirme_adicionar_clientes($name, $usuario, $senha, $adulto, $plano, $Dispositivo, $App, $Forma_de_pagamento, $nome_do_pagador, $Whatsapp, $indicacao, $mac, $key);
        if (!$confirme_adicionar_clientes){
            $resposta = [
                'title' => 'Erro!',
                'msg' => 'Erro na funçao mudar servidor.',
                'icon' => 'error'
            ];
            echo json_encode($resposta);
            exit();
        }else{
            echo json_encode($confirme_adicionar_clientes);
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