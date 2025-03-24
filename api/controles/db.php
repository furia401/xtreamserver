<?php
header("Access-Control-Allow-Origin: *"); 
date_default_timezone_set('America/Sao_Paulo');
header("Server: nginx");

function conectar_bd() {
    $endereco = "localhost"; 
    $banco = "xtserveropensource"; 
    $dbusuario = "root"; 
    $dbsenha = ""; 

    try {
        $conexao = new PDO("mysql:host=$endereco;dbname=$banco", $dbusuario, $dbsenha);
        $conexao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conexao;
    } catch(PDOException $e) {

        return null;
    }
}