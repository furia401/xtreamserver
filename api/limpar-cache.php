<?php
   session_start();

    if (isset($_SESSION['cache'])) {
        unset($_SESSION['cache']); 
        unset($_SESSION['seriesCache']); 
        unset($_SESSION['info']); 
        echo json_encode([
            "status" => "success",
            "message" => "O cache foi limpo com sucesso."
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Não há cache para limpar."
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
    exit;