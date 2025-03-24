<?php

require_once('./controles/db.php');

$conexao = conectar_bd();

if ($conexao) {

    echo "conectado";
}

$conexao = null;

?>