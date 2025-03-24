<?php

require_once('./controles/db.php');
require_once('./controles/login.php');

if ($conexao = conectar_bd()) {
    if (isset($_GET['login'])) {
        if (empty($_GET['username'])) {
            $resposta = [
                'title' => 'É necessário preencher o campo usuário.',
                'icon' => 'error'
            ];
            echo json_encode($resposta);
        } elseif (empty($_GET['password'])) {
            $resposta = [
                'title' => 'É necessário preencher o campo senha.',
                'icon' => 'error'
            ];
            echo json_encode($resposta);
        } else {
            $info = login($_GET['username'], $_GET['password'], $conexao);
            echo json_encode($info);
        }
    }
}else {
    $resposta = [
        'title' => 'Não foi possível conectar ao banco de dados.',
        'icon' => 'error'
    ];
    echo json_encode($resposta);
}

$conexao = null;

