<?php

function Dashboard()
{
    $conexao = conectar_bd();
    $token = isset($_SESSION['token']) ? $_SESSION['token'] : "0";
    $admin_id = isset($_SESSION['admin_id']) ? $_SESSION['admin_id'] : null;
     
    $sql = "SELECT c.*,c.id AS id_usuario, c.name AS c_nome, c.usuario AS c_usuario,
                    p.* 
            FROM clientes c
            LEFT JOIN planos p ON c.plano = p.id 
            WHERE c.admin_id = :admin_id AND is_trial = 0";
    $stmt = $conexao->prepare($sql);
    $stmt->bindParam(':admin_id', $admin_id, PDO::PARAM_INT);
    $stmt->execute();
    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);


    $resultadoFinal = [];
    $resultadoFinal['Totaldeclientes'] = count($resultados);
    $resultadoFinal['Totaldeclientes_valor'] = 0;
    $resultadoFinal['Totaldeclientes_valor_unidade'] = 0;
    $resultadoFinal['clientesAtivos'] = 0;
    $resultadoFinal['clientesAtivos_valor'] = 0;
    $resultadoFinal['clientesAtivos_valor_unidade'] = 0;
    $resultadoFinal['clientesvencidostotal'] = 0;
    $resultadoFinal['clientesvencidostotal_valor'] = 0;
    $resultadoFinal['clientesvencidostotal_valor_unidade'] = 0;
    $resultadoFinal['clientesrenovados_lista'] = array();
    $resultadoFinal['clientesrenovados_lista_valor'] = 0;
    $resultadoFinal['clientesrenovados_lista_valor_total'] = 0;
    $resultadoFinal['clientesrenovados'] = 0;
    $resultadoFinal['clientesrenovados_valor'] = 0;
    $resultadoFinal['clientesrenovados_valor_total'] = 0;
    $resultadoFinal['clientesrenovados_valor_unidade'] = 0;
    $resultadoFinal['clientesarenovar'] = 0;
    $resultadoFinal['clientesarenovar_valor'] = 0;
    $resultadoFinal['clientesarenovar_valor_unidade'] = 0;
    $resultadoFinal['clientesnovos'] = 0;
    $resultadoFinal['clientesnovos_valor'] = 0;
    $resultadoFinal['clientesnovos_valor_unidade'] = 0;
    $resultadoFinal['clientesvencidos_este_mes_lista'] = array();
    $resultadoFinal['clientesvencidos_este_mes'] = 0;
    $resultadoFinal['clientesvencidos_este_mes_valor'] = 0;
    $resultadoFinal['clientesvencidos_este_mes_valor_unidade'] = 0;
    $resultadoFinal['clientesvencidos_hoje_lista'] = array();
    $resultadoFinal['clientesvencidos_hoje_lucro'] = 0;
    $resultadoFinal['clientesvencidos_valor_total'] = 0;
    $resultadoFinal['clientesvencidos_amanha_lista'] = array();
    $resultadoFinal['clientesvencidos_amanha_lucro'] = 0;
    $resultadoFinal['clientesvencidos_amanha_valor_total'] = 0;
    $resultadoFinal['clientesvencidos_proximos'] = array();
    $resultadoFinal['clientesvencidos_proximos_lucro'] = 0;
    $resultadoFinal['clientesvencidos_proximos_valor_total'] = 0;
    $lucro = 0;
    foreach ($resultados as $dados) {
        $custo_por_credito = isset($dados["custo_por_credito"]) ? (float)$dados["custo_por_credito"] : 0;
        $lucro += $dados['V_total'] - $custo_por_credito;
        $resultadoFinal['Totaldeclientes_valor'] += $dados['V_total'] - $custo_por_credito;
        $resultadoFinal['Totaldeclientes_valor_unidade'] = number_format($lucro / $resultadoFinal['Totaldeclientes'], 2);

        if ($dados['Vencimento'] >= date('Y-m-d')) {
            $resultadoFinal['clientesAtivos']++;
            $resultadoFinal['clientesAtivos_valor'] += ($dados['V_total'] - $custo_por_credito);
            $resultadoFinal['clientesAtivos_valor_unidade'] = number_format($resultadoFinal['clientesAtivos_valor'] / $resultadoFinal['clientesAtivos'], 2);
        }
        if (date('Y-m', strtotime($dados['Vencimento'])) < date('Y-m', strtotime(date('Y-m-d')))) {
            $resultadoFinal['clientesvencidostotal']++;
            $resultadoFinal['clientesvencidostotal_valor'] += ($dados['V_total'] - $custo_por_credito);
            $resultadoFinal['clientesvencidostotal_valor_unidade'] = number_format($resultadoFinal['clientesvencidostotal_valor'] / $resultadoFinal['clientesvencidostotal'], 2);
        }
        $proximoMes = date('Y-m', strtotime('+1 month', strtotime(date('Y-m'))));
        if (date('Y-m', strtotime($dados['Vencimento'])) == date('Y-m', strtotime($proximoMes)) && date('Y-m', strtotime($dados['Criado_em'])) < date('Y-m', strtotime(date('Y-m')))) {
            $resultadoFinal['clientesrenovados']++;
            $resultadoFinal['clientesrenovados_valor'] += ($dados['V_total'] - $custo_por_credito);
            $resultadoFinal['clientesrenovados_valor_total'] += $dados['V_total'];
            $resultadoFinal['clientesrenovados_valor_unidade'] = number_format($resultadoFinal['clientesrenovados_valor'] / $resultadoFinal['clientesrenovados'], 2);
        }
        if (date('Y-m', strtotime($dados['Vencimento'])) == date('Y-m', strtotime($proximoMes)) && date('Y-m', strtotime($dados['Criado_em'])) <= date('Y-m', strtotime(date('Y-m')))) {
            $resultadoFinal['clientesrenovados_lista_valor'] += ($dados['V_total'] - $custo_por_credito);
            $resultadoFinal['clientesrenovados_lista_valor_total'] += $dados['V_total'];
            $resultadoFinal['clientesrenovados_lista'][] = array(
                "id" => $dados['id_usuario'],
                "usuario" => $dados['c_usuario'],
                "lucro" => ($dados['V_total'] - $custo_por_credito),
                "total" => $dados['V_total'],
                "data" => date('d-m-Y', strtotime($dados['Vencimento'])),
            );
        }
        if (date('Y-m-d', strtotime($dados['Vencimento'])) >= date('Y-m-d', strtotime(date('Y-m-d'))) && date('Y-m', strtotime($dados['Vencimento'])) < date('Y-m', strtotime($proximoMes))) {
            $resultadoFinal['clientesarenovar']++;
            $resultadoFinal['clientesarenovar_valor'] += ($dados['V_total'] - $custo_por_credito);
            $resultadoFinal['clientesarenovar_valor_unidade'] = number_format($resultadoFinal['clientesarenovar_valor'] / $resultadoFinal['clientesarenovar'], 2);
        }
        if (date('Y-m', strtotime($dados['Criado_em'])) == date('Y-m', strtotime(date('Y-m-d')))) {
            $resultadoFinal['clientesnovos']++;
            $resultadoFinal['clientesnovos_valor'] += ($dados['V_total'] - $custo_por_credito);
            $resultadoFinal['clientesnovos_valor_unidade'] = number_format($resultadoFinal['clientesnovos_valor'] / $resultadoFinal['clientesnovos'], 2);
        }
        if (date('Y-m-d', strtotime($dados['Vencimento'])) < date('Y-m-d', strtotime(date('Y-m-d'))) && date('Y-m', strtotime($dados['Vencimento'])) == date('Y-m', strtotime(date('Y-m-d')))) {
            $resultadoFinal['clientesvencidos_este_mes']++;
            $resultadoFinal['clientesvencidos_este_mes_valor'] += ($dados['V_total'] - $custo_por_credito);
            $resultadoFinal['clientesvencidos_este_mes_valor_unidade'] = number_format($resultadoFinal['clientesvencidos_este_mes_valor'] / $resultadoFinal['clientesvencidos_este_mes'], 2);
            $resultadoFinal['clientesvencidos_este_mes_lista'][] = array(
                "id" => $dados['id_usuario'],
                "usuario" => $dados['c_usuario'],
                "lucro" => ($dados['V_total'] - $custo_por_credito),
                "total" => $dados['V_total'],
                "data" => date('d-m-Y', strtotime($dados['Vencimento'])),
            );
        }
        if (date('Y-m-d', strtotime($dados['Vencimento'])) == date('Y-m-d', strtotime(date('Y-m-d')))) {
            $resultadoFinal['clientesvencidos_hoje_lista'][] = array(
                "id" => $dados['id_usuario'],
                "usuario" => $dados['c_usuario'],
                "lucro" => ($dados['V_total'] - $custo_por_credito),
                "total" => $dados['V_total'],
                "data" => date('d-m-Y', strtotime($dados['Vencimento'])),
            );
            $resultadoFinal['clientesvencidos_hoje_lucro'] += ($dados['V_total'] - $custo_por_credito);
            $resultadoFinal['clientesvencidos_valor_total'] += $dados['V_total'];
        }

        $hojeMaisUmDia = date('Y-m-d', strtotime('+1 day', strtotime(date('Y-m-d'))));
        if (date('Y-m-d', strtotime($dados['Vencimento'])) == $hojeMaisUmDia) {
            $resultadoFinal['clientesvencidos_amanha_lista'][] = array(
                "id" => $dados['id_usuario'],
                "usuario" => $dados['c_usuario'],
                "lucro" => ($dados['V_total'] - $custo_por_credito),
                "total" => $dados['V_total'],
                "data" => date('d-m-Y', strtotime($dados['Vencimento'])),
            );
            $resultadoFinal['clientesvencidos_amanha_lucro'] += ($dados['V_total'] - $custo_por_credito);
            $resultadoFinal['clientesvencidos_amanha_valor_total'] += $dados['V_total'];
        }

        $hojeMaisdoisDias = date('Y-m-d', strtotime('+2 day', strtotime(date('Y-m-d'))));
        $seteDiasDepois = date('Y-m-d', strtotime('+7 days', strtotime(date('Y-m-d'))));
        if (date('Y-m-d', strtotime($dados['Vencimento'])) >= $hojeMaisdoisDias && date('Y-m-d', strtotime($dados['Vencimento'])) <= $seteDiasDepois) {
            $resultadoFinal['clientesvencidos_proximos'][] = array(
                "id" => $dados['id_usuario'],
                "usuario" => $dados['c_usuario'],
                "lucro" => ($dados['V_total'] - $custo_por_credito),
                "total" => $dados['V_total'],
                "data" => date('d-m-Y', strtotime($dados['Vencimento'])),
            );
            $resultadoFinal['clientesvencidos_proximos_lucro'] += ($dados['V_total'] - $custo_por_credito);
            $resultadoFinal['clientesvencidos_proximos_valor_total'] += $dados['V_total'];
        }

         // Ordena os resultados pela data de vencimento em ordem crescente
        usort($resultadoFinal['clientesrenovados_lista'], function ($a, $b) {
            $dataA = strtotime($a['data']);
            $dataB = strtotime($b['data']);
            return $dataA - $dataB;
        });

        // Ordena os resultados pela data de vencimento em ordem crescente
        usort($resultadoFinal['clientesvencidos_este_mes_lista'], function ($a, $b) {
            $dataA = strtotime($a['data']);
            $dataB = strtotime($b['data']);
            return $dataA - $dataB;
        });

         // Ordena os resultados pela data de vencimento em ordem crescente
        usort($resultadoFinal['clientesvencidos_proximos'], function ($a, $b) {
            $dataA = strtotime($a['data']);
            $dataB = strtotime($b['data']);
            return $dataA - $dataB;
        });
    }

    return $resultadoFinal;

}

function testes()
{
    $conexao = conectar_bd();
    $token = isset($_SESSION['token']) ? $_SESSION['token'] : "0";
    $admin_id = isset($_SESSION['admin_id']) ? $_SESSION['admin_id'] : null;
     
    $sql = "SELECT c.*,
                    p.* 
            FROM clientes c
            LEFT JOIN planos p ON c.plano = p.id 
            WHERE c.admin_id = :admin_id AND is_trial = 1";
    $stmt = $conexao->prepare($sql);
    $stmt->bindParam(':admin_id', $admin_id, PDO::PARAM_INT);
    $stmt->execute();
    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);


    $resultadoFinal = [];
    $resultadoFinal['Totaldetestes'] = count($resultados);
    $resultadoFinal['Totaldetestes_valor'] = 0;
    $resultadoFinal['Totaldetestes_valor_unidade'] = 0;
    $resultadoFinal['TestesAtivos'] = 0;
    $resultadoFinal['TestesAtivos_valor'] = 0;
    $resultadoFinal['TestesAtivos_valor_unidade'] = 0;
    $resultadoFinal['Testesvencidostotal'] = 0;
    $resultadoFinal['Testesvencidostotal_valor'] = 0;
    $resultadoFinal['Testesvencidostotal_valor_unidade'] = 0;
    $lucro = 0;
    foreach ($resultados as $dados) {
        $custo_por_credito = isset($dados["custo_por_credito"]) ? (float)$dados["custo_por_credito"] : 0;
        $lucro += $dados['V_total'] - $custo_por_credito;
        $resultadoFinal['Totaldetestes_valor'] += $dados['V_total'] - $dados['custo_por_credito'];
        $resultadoFinal['Totaldetestes_valor_unidade'] = number_format($lucro / $resultadoFinal['Totaldetestes'], 2);

        if ($dados['Vencimento'] >= date('Y-m-d')) {
            $resultadoFinal['TestesAtivos']++;
            $resultadoFinal['TestesAtivos_valor'] += ($dados['V_total'] - $dados['custo_por_credito']);
            $resultadoFinal['TestesAtivos_valor_unidade'] = number_format($resultadoFinal['TestesAtivos_valor'] / $resultadoFinal['TestesAtivos'], 2);
        }
        if (date('Y-m', strtotime($dados['Vencimento'])) < date('Y-m', strtotime(date('Y-m-d')))) {
            $resultadoFinal['Testesvencidostotal']++;
            $resultadoFinal['Testesvencidostotal_valor'] += ($dados['V_total'] - $dados['custo_por_credito']);
            $resultadoFinal['Testesvencidostotal_valor_unidade'] = number_format($resultadoFinal['Testesvencidostotal_valor'] / $resultadoFinal['Testesvencidostotal'], 2);
        }
    }

    return $resultadoFinal;

}

function conteudos()
{
    $conexao = conectar_bd();

    $sql = "
        SELECT 
            (SELECT COUNT(*) FROM streams WHERE stream_type = 'live') AS TotalLiveStreams,
            (SELECT COUNT(*) FROM streams WHERE stream_type = 'movie') AS TotalMovieStreams,
            (SELECT COUNT(*) FROM series) AS TotalSeries,
            (SELECT COUNT(*) FROM series_episodes) AS TotalEpisodes
    ";

    $stmt = $conexao->prepare($sql);
    $stmt->execute();
    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

    return $resultado;
}
