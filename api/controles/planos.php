<?php

function edite_planos($id)
{
    $conexao = conectar_bd();

    $token = isset($_SESSION['token']) ? $_SESSION['token'] : "0";
    $admin_id = isset($_SESSION['admin_id']) ? $_SESSION['admin_id'] : null;

    $sql = "SELECT p.*
            FROM admin a 
            LEFT JOIN planos p ON p.id = :id  
            WHERE a.id = :admin_id AND a.token = :token AND p.admin_id = :admin_id";
        $stmt = $conexao->prepare($sql);
        $stmt->bindParam(':admin_id', $admin_id, PDO::PARAM_INT);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':token', $token);
        $stmt->execute();

    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

        extract($row);

        $modal_body = '';
        $modal_body .= '<input type="hidden" name="confirme_edite_planos" value="'.$id.'">';
        $modal_body .= '<div class="form-group col-md col">
                        <label for="nome">Nome:</label>
                        <input type="text" class="form-control" name="nome" value="'.$nome.'">
                        </div>';
        $modal_body .= ' <div class="form-group col-md col">
                       <label for="valor">Valor Padrao:</label>
                       <input type="text" class="form-control" name="valor" value="'.$valor.'">
                       </div>';
        $modal_body .= ' <div class="form-group col-md col">
                       <label for="custo_por_credito">Custo por Clientes:</label><small class="text-danger">* caso tenha algum custo por clientes coloque abaixo ou deixe zerado.</small>
                       <input type="text" class="form-control" name="custo_por_credito" value="'.$custo_por_credito.'">
                       </div>';

        $modal_footer = "<button type='button' onclick='enviardados(\"modal_master_form\", \"planos.php\")' class='btn btn-info waves-effect waves-light' >Salvar</button><button type='button' class='btn btn-danger' data-bs-dismiss='modal' aria-label='Close'>Cancelar</button>";

        $resposta = [
            'modal_header_class'=> "d-block modal-header bg-info text-white m-2",
            'modal_titulo'=> "Editar Plano",
            'modal_body'=> $modal_body,
            'modal_footer'=> $modal_footer
        ];

        return $resposta;
    } else {
        return 0;
    }
}

function confirme_edite_planos($id, $nome, $valor, $custo_por_credito)
{

    if ($valor === null || $valor === '' || !is_numeric($valor)) {
        $valor = 0; 
        $valor_primeiro_mes = 0;
    }

    if ($custo_por_credito === null || $custo_por_credito === '' || !is_numeric($custo_por_credito)) {
        $custo_por_credito = 0; 
    }


    $conexao = conectar_bd();

    $token = isset($_SESSION['token']) ? $_SESSION['token'] : "0";
    $admin_id = isset($_SESSION['admin_id']) ? $_SESSION['admin_id'] : null;

    $sql = "SELECT p.*
            FROM admin a 
            LEFT JOIN planos p ON p.id = :id  
            WHERE a.id = :admin_id AND a.token = :token AND p.admin_id = :admin_id";
        $stmt = $conexao->prepare($sql);
        $stmt->bindParam(':admin_id', $admin_id, PDO::PARAM_INT);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':token', $token);
        $stmt->execute();
    $resposta = [];    
    if ($row = $stmt->fetch()) {

            $sql_update = "UPDATE planos SET ";
            $updates = [];

            if (!is_null($nome) && $nome !== '') {
                $updates[] = "nome = :nome";
            }
            if (is_numeric($valor)) {
                $updates[] = "valor = :valor";
            }
            if (is_numeric($custo_por_credito)) {
                $updates[] = "custo_por_credito = :custo_por_credito";
            }
            
            $sql_update .= implode(", ", $updates);
            $sql_update .= " WHERE id = :id AND admin_id = :admin_id";

            $stmt_update = $conexao->prepare($sql_update);

            if (!is_null($nome) && $nome !== '') {
                $stmt_update->bindParam(':nome', $nome);
            }
            if (is_numeric($valor)) {
                $stmt_update->bindParam(':valor', $valor);
            }
            if (is_numeric($custo_por_credito)) {
                $stmt_update->bindParam(':custo_por_credito', $custo_por_credito);
            }

            $stmt_update->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt_update->bindParam(':admin_id', $admin_id, PDO::PARAM_INT);

            if ($stmt_update->execute()) {
                $sql_update1 = "UPDATE clientes SET V_total = '$valor' WHERE plano = '$id' AND admin_id = '$admin_id'";
                if ($conexao->exec($sql_update1)) {

                    $dataatual = date("Y-m");
                    $sql_update2 = "UPDATE clientes SET V_total = '$valor' WHERE plano = '$id' AND admin_id = '$admin_id' AND DATE_FORMAT(Criado_em, '%Y-%m') = '$dataatual' ";
                    if ($conexao->exec($sql_update2)) {

                        $resposta['title'] = "Plano editado com sucesso";
                        $resposta['msg'] = "Alterado para todos os clientes novos e antigos com sucesso! ";
                        $resposta['icon'] = "success";
                        $resposta['url'] = "planos.php";
                        return $resposta;
                    }

                    $resposta['title'] = "Plano editado com sucesso";
                    $resposta['msg'] = "Alterado para todos os clientes com sucesso! ";
                    $resposta['icon'] = "success";
                    $resposta['url'] = "planos.php";
                    return $resposta;
                }
                $resposta['title'] = "Concluído!";
                $resposta['msg'] = "Plano editado com sucesso";
                $resposta['icon'] = "success";
                $resposta['url'] = "planos.php";
            } else {
                $resposta['title'] = "Erro!";
                $resposta['msg'] = "Erro ao editar Plano";
                $resposta['icon'] = "error";
            }

            return $resposta;
    } else {
        return 0;
    }
}

function delete_plano($id, $nome)
{
    $conexao = conectar_bd();

    $token = isset($_SESSION['token']) ? $_SESSION['token'] : "0";
    $admin_id = isset($_SESSION['admin_id']) ? $_SESSION['admin_id'] : null;

    $sql = "SELECT p.*
            FROM admin a 
            LEFT JOIN planos p ON p.id = :id  
            WHERE a.id = :admin_id AND a.token = :token AND p.admin_id = :admin_id";
        $stmt = $conexao->prepare($sql);
        $stmt->bindParam(':admin_id', $admin_id, PDO::PARAM_INT);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':token', $token);
        $stmt->execute();

    $resposta = []; 

    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

        $dataAtual = date("Y-m-d H:i:s");

        $sql_count = "SELECT COUNT(*) AS count_planos
                      FROM clientes
                      WHERE plano = :id AND Vencimento > :dataAtual AND is_trial = 0";
        $stmt_count = $conexao->prepare($sql_count);
        $stmt_count->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt_count->bindParam(':dataAtual', $dataAtual);
        $stmt_count->execute();
        $count_result = $stmt_count->fetch(PDO::FETCH_ASSOC);

        $count_servidores = $count_result['count_planos'];

        if ($count_servidores > 0) {
            $modal_body = "";
            $modal_body .= '<div class="form-group pb-3"> Este plano ainda tem clientes mova para outro plano em <i class="fa-exchange-alt fas p-2"></i> para poder excluir </div>';

            $modal_footer = "<button type='button' class='btn btn-primary btn-sm' data-bs-dismiss='modal' aria-label='Close'>Sair</button><button type='button' class='btn btn-success btn-sm' onclick='modal_master(\"api/planos.php\", \"mover_clientes_plano\", \"$id\")'><i class='fa-exchange-alt fas p-2'></i> Mudar plano</button>";

            $resposta = [
                'modal_header_class'=> "d-block modal-header bg-danger text-white",
                'modal_titulo'=> "NAO É POSSIVEL EXCLUIR O PLANO",
                'modal_body'=> $modal_body,
                'modal_footer'=> $modal_footer
            ];
        } else {
            $modal_body = "<input type=\"hidden\" name=\"confirme_delete_plano\" value='$id'></div>";
            $modal_body .= "<input type=\"hidden\" name=\"nome\" value='$nome'></div>";
            $modal_body .= "Tem certeza de que deseja excluir o plano ($nome) ?";

            $modal_footer = "<button type='button' class='btn btn-primary btn-sm' data-bs-dismiss='modal' aria-label='Close'>Cancelar</button><button type='button' class='btn btn-danger btn-sm' onclick='enviardados(\"modal_master_form\", \"planos.php\")'>EXCLUIR</button>";

            $resposta = [
                'modal_header_class'=> "d-block modal-header bg-danger text-white",
                'modal_titulo'=> "EXCLUIR PLANO",
                'modal_body'=> $modal_body,
                'modal_footer'=> $modal_footer
            ];
        }

        return $resposta;
    } else {
        return 0;
    }
}

function confirme_delete_plano($id, $nome)
{
    $conexao = conectar_bd();
    $token = isset($_SESSION['token']) ? $_SESSION['token'] : "0";
    $admin_id = isset($_SESSION['admin_id']) ? $_SESSION['admin_id'] : null;

    $sql = "SELECT p.*
            FROM admin a 
            LEFT JOIN planos p ON p.id = :id  
            WHERE p.id = :id AND p.admin_id = :admin_id AND a.token = :token AND a.id = :admin_id";
        $stmt = $conexao->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':token', $token);
        $stmt->bindParam(':admin_id', $admin_id);
        $stmt->execute();

    $resposta = []; 

    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

        $sql_count = "SELECT COUNT(*) AS count_planos
                      FROM clientes
                      WHERE plano = :id";
        $stmt_count = $conexao->prepare($sql_count);
        $stmt_count->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt_count->execute();
        $count_result = $stmt_count->fetch(PDO::FETCH_ASSOC);
        $count_planos = $count_result['count_planos'];

        if ($count_planos > 0) {
            $resposta['title'] = "Erro!";
            $resposta['msg'] = "Este plano ainda possue clientes. voce precisa deletar ou mover os clientes para outro plano";
            $resposta['icon'] = "error";
            return $resposta;
        }

        $sql_delete = "DELETE FROM planos WHERE id = :id AND admin_id = :admin_id AND nome = :nome";
        $stmt_delete = $conexao->prepare($sql_delete);
        $stmt_delete->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt_delete->bindParam(':admin_id', $row['admin_id'], PDO::PARAM_INT);
        $stmt_delete->bindParam(':nome', $nome);

        if ($stmt_delete->execute()) {

            if ($stmt_delete->rowCount() > 0) {
                $resposta['title'] = "Sucesso!";
                $resposta['msg'] = "Plano deletado com sucesso!";
                $resposta['icon'] = "success";
                $resposta['url'] = "planos.php";
                return $resposta;
            } else {
                $resposta['title'] = "Erro!";
                $resposta['msg'] = "Nao possivel deletar o Plano.";
                $resposta['icon'] = "error";
                return $resposta;
            }
        } else {
            $resposta['title'] = "Erro!";
            $resposta['msg'] = "Erro ao deletar Plano.";
            $resposta['icon'] = "error";
            return $resposta;
        }

    } else {
        return 0;
    }
}

function adicionar_planos()
{
    $conexao = conectar_bd();

    $token = isset($_SESSION['token']) ? $_SESSION['token'] : "0";
    $admin_id = isset($_SESSION['admin_id']) ? $_SESSION['admin_id'] : null;

    $sql = "SELECT *
            FROM admin a 
            WHERE a.id = :admin_id AND a.token = :token";
        $stmt = $conexao->prepare($sql);
        $stmt->bindParam(':admin_id', $admin_id, PDO::PARAM_INT);
        $stmt->bindParam(':token', $token);
        $stmt->execute();

    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

        $modal_body = '';
        $modal_body .= '<input type="hidden" name="confirme_adicionar_plano" value="">';
        $modal_body .= '<div class="form-group col-md col">
                        <label for="nome">Nome:</label>
                        <input type="text" class="form-control" name="nome" value="">
                        </div>';
        $modal_body .= ' <div class="form-group col-md col">
                       <label for="valor">Valor Padrao:</label>
                       <input type="text" class="form-control" name="valor" value="30">
                       </div>';
        $modal_body .= ' <div class="form-group col-md col">
                       <label for="custo_por_credito">Custo por Cliente:</label><small class="text-danger">* caso tenha algum custo por clientes coloque abaixo ou deixe zerado.</small>
                       <input type="text" class="form-control" name="custo_por_credito" value="">
                       </div>';

        $modal_footer = "<button type='button' onclick='enviardados(\"modal_master_form\", \"planos.php\")' class='btn btn-info waves-effect waves-light' >Adicionar</button><button type='button' class='btn btn-danger' data-bs-dismiss='modal' aria-label='Close'>Cancelar</button>";

        $resposta = [
            'modal_header_class'=> "d-block modal-header bg-info text-white m-2",
            'modal_titulo'=> "Adicionar Plano",
            'modal_body'=> $modal_body,
            'modal_footer'=> $modal_footer
        ];

        return $resposta;
    } else {
        return 0;
    }
}

function confirme_adicionar_plano($nome, $valor, $custo_por_credito)
{

    $conexao = conectar_bd();

    $token = isset($_SESSION['token']) ? $_SESSION['token'] : "0";
    $admin_id = isset($_SESSION['admin_id']) ? $_SESSION['admin_id'] : null;

    $sql = "SELECT *
            FROM admin a 
            WHERE a.id = :admin_id AND a.token = :token";
        $stmt = $conexao->prepare($sql);
        $stmt->bindParam(':admin_id', $admin_id, PDO::PARAM_INT);
        $stmt->bindParam(':token', $token);
        $stmt->execute();
    $resposta = [];    
    if ($row = $stmt->fetch()) {

        $sql = "INSERT INTO planos (admin_id, nome, valor, custo_por_credito) 
                VALUES (:admin_id, :nome, :valor, :custo_por_credito)";

        $stmt = $conexao->prepare($sql);

        $stmt->bindParam(':admin_id', $admin_id, PDO::PARAM_INT);
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':valor', $valor);
        $stmt->bindParam(':custo_por_credito', $custo_por_credito);

        if ($stmt->execute()) {
            $resposta['title'] = "Concluído!";
            $resposta['msg'] = "Plano inserido com sucesso";
            $resposta['icon'] = "success";
            $resposta['url'] = "planos.php";
            return $resposta;
        } else {
            $resposta['title'] = "Erro!";
            $resposta['msg'] = "Erro ao inserir Plano";
            $resposta['icon'] = "error";
            return $resposta;
        }

    } else {
        return 0;
    }
}

function mover_clientes_plano($id)
{
    $conexao = conectar_bd();

    $token = isset($_SESSION['token']) ? $_SESSION['token'] : "0";
    $admin_id = isset($_SESSION['admin_id']) ? $_SESSION['admin_id'] : null;

    $sql = "SELECT p.*
            FROM admin a 
            LEFT JOIN planos p ON p.id = :id
            WHERE a.id = :admin_id AND a.token = :token AND p.id = :id AND p.admin_id = :admin_id";
        $stmt = $conexao->prepare($sql);
        $stmt->bindParam(':admin_id', $admin_id, PDO::PARAM_INT);
        $stmt->bindParam(':token', $token);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':proximo_plano', $proximo_plano);
        $stmt->execute();

    $resposta = [];
    $option = "";
    $server_atual = $id;
    if ($row = $stmt->fetch()) {

            $plano_id = $conexao->query("SELECT * FROM planos WHERE admin_id = '$admin_id'");

            while ($row2 = $plano_id->fetch()) {
                if($server_atual != $row2['id']){
                    $option .= '<option class="form-control" value="' . $row2['id'] . '">[R$: '.$row2['valor'].'] ' . $row2['nome'] . ' </option>';
                }
            }

            $modal_body = "";
            $modal_body .= '<div class="form-group pb-3"> Escoha um plano para o qual os clientes será movidos </div>';
            $modal_body .= "<input type=\"hidden\" name=\"confirme_mover_clientes_plano\" value='$id'></div>";
            $modal_body .= "<select class='form-select form-control' name='plano'>$option</select>";

            $modal_footer = "<button type='button' class='btn btn-primary btn-sm' data-bs-dismiss='modal' aria-label='Close'>Sair</button><button type='button' class='btn btn-success btn-sm' onclick='enviardados(\"modal_master_form\", \"planos.php\")'><i class='fa-exchange-alt fas p-2'></i> Mudar Plano</button>";

            $resposta = [
                'modal_header_class'=> "d-block modal-header bg-primary text-white",
                'modal_titulo'=> "MOVER CLIENTES DO PLANO (". $row['nome']. ")",
                'modal_body'=> $modal_body,
                'modal_footer'=> $modal_footer
            ];
            return $resposta;
    } else {
        return 0; 
    }
}

function confirme_mover_clientes_plano($plano_atual, $proximo_plano)
{
    $conexao = conectar_bd();
    $token = isset($_SESSION['token']) ? $_SESSION['token'] : "0";
    $admin_id = isset($_SESSION['admin_id']) ? $_SESSION['admin_id'] : null;

    $sql = "SELECT p.*
            FROM admin a 
            LEFT JOIN planos p ON p.id = :proximo_plano
            WHERE a.id = :admin_id AND a.token = :token AND p.admin_id = :admin_id";
        $stmt = $conexao->prepare($sql);
        $stmt->bindParam(':admin_id', $admin_id, PDO::PARAM_INT);
        $stmt->bindParam(':token', $token);
        $stmt->bindParam(':proximo_plano', $proximo_plano);
        $stmt->execute();

    $resposta = []; 
    $modal_body = "";
    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

            $stmt = $conexao->prepare("UPDATE clientes SET plano = :proximo_plano WHERE plano = :plano_atual AND admin_id = :admin_id");
            $stmt->bindValue(':plano_atual', $plano_atual);
            $stmt->bindValue(':admin_id', $admin_id);
            $stmt->bindValue(':proximo_plano', $proximo_plano);

        if ($stmt->execute()) {
            $resposta['title'] = "Sucesso!";
            $resposta['msg'] = "Plano Alterado com sucesso!";
            $resposta['icon'] = "success";
            $resposta['url'] = "planos.php";
            return $resposta;
        } else {
            $resposta['title'] = "Erro!";
            $resposta['msg'] = "Erro ao Alterar o Plano.";
            $resposta['icon'] = "error";
            return $resposta;
        }

    } else {
            $resposta['title'] = "Erro!";
            $resposta['msg'] = "Ouve algum erro no banco de dados.";
            $resposta['icon'] = "error";
        return $resposta;
    }
}