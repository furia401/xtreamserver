<?php

function checkLogout() {

    if (session_status() !== PHP_SESSION_ACTIVE) {
        header('Location: ./index.php');
        exit();
    }

    if (empty($_SESSION['logged_in_fxtream'])) {
        header('Location: ./index.php');
        exit();
    }

    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 3600)) {

        $_SESSION = array();
        session_unset();
        session_destroy();

        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }

        header('Location: ./index.php');
        exit();
    }

    session_regenerate_id(true);

    $_SESSION['last_activity'] = time();
}

function checkLogoutapi() {
    $resposta = []; 

    if (session_status() !== PHP_SESSION_ACTIVE) {
        $resposta['title'] = "Erro!";
        $resposta['msg'] = "sessao expirada faça o login novamente";
        $resposta['icon'] = "error";
        $resposta['url'] = "index.php";
        echo json_encode($resposta);
        exit();
    }

    if (empty($_SESSION['logged_in_fxtream'])) {
        $resposta['title'] = "Erro!";
        $resposta['msg'] = "sessao expirada faça o login novamente";
        $resposta['icon'] = "error";
        $resposta['url'] = "index.php";
        echo json_encode($resposta);
        exit();
    }

    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 3600)) {

        $_SESSION = array();
        session_unset();
        session_destroy();

        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }

        $resposta['title'] = "Erro!";
        $resposta['msg'] = "sessao expirada faça o login novamente";
        $resposta['icon'] = "error";
        $resposta['url'] = "index.php";
        echo json_encode($resposta);
        exit();
    }
}