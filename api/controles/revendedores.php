<?php

function deletaradmins() {

    try {
        $conexao = conectar_bd();

        $sql = "SELECT id, criado_por FROM admin WHERE admin = 0";
        $stmt = $conexao->query($sql);

        $resposta = []; 

        if ($stmt->rowCount() > 0) {

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $id = $row['id'];
                $criado_por = $row['criado_por'];

                $count_sql = "SELECT COUNT(*) AS total FROM admin WHERE id = :criado_por";
                $count_stmt = $conexao->prepare($count_sql);
                $count_stmt->bindParam(':criado_por', $criado_por, PDO::PARAM_INT);
                $count_stmt->execute();

                $total = $count_stmt->fetchColumn();

                if ($total == 0) {
                    $delete_sql_admin = "DELETE FROM admin WHERE id = :id";
                    $delete_stmt_admin = $conexao->prepare($delete_sql_admin);
                    $delete_stmt_admin->bindParam(':id', $id, PDO::PARAM_INT);
                    $delete_stmt_admin->execute();

                    $delete_sql_clientes = "DELETE FROM clientes WHERE admin_id = :id";
                    $delete_stmt_clientes = $conexao->prepare($delete_sql_clientes);
                    $delete_stmt_clientes->bindParam(':id', $id, PDO::PARAM_INT);
                    $delete_stmt_clientes->execute();

                    $delete_sql_planos = "DELETE FROM planos WHERE admin_id = :id";
                    $delete_stmt_planos = $conexao->prepare($delete_sql_planos);
                    $delete_stmt_planos->bindParam(':id', $id, PDO::PARAM_INT);
                    $delete_stmt_planos->execute();
                }
            }
        } else {

        }
    } catch (PDOException $e) {

    }
}

function edite_revendedor($id)
{
    $conexao = conectar_bd();

    $token = isset($_SESSION['token']) ? $_SESSION['token'] : "0";
    $admin_id = isset($_SESSION['admin_id']) ? $_SESSION['admin_id'] : null;

    $sql = "SELECT a.*, 
                   a.user AS user, 
                   a.plano AS plano,
                   aa.plano AS admin_plano,
                   aa.admin AS admin 
            FROM admin a 
            LEFT JOIN admin aa ON a.criado_por = :admin_id  
            WHERE a.id = :id AND aa.token = :token";
        $stmt = $conexao->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':token', $token);
        $stmt->bindParam(':admin_id', $admin_id);
        $stmt->execute();

    $option = "";
    $option_servidores = "";
    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

        extract($row);

        $planos_admin = $conexao->query("SELECT * FROM planos_admin WHERE id < $admin_plano");

        while ($p_admin = $planos_admin->fetch()) {

            if ($p_admin['id'] == $plano) {
                $option .= '<option value="'.$p_admin['id'].'" selected>'.$p_admin['nome'].' [Atual]</option>';
            } else {
                $option .= '<option value="'.$p_admin['id'].'">'.$p_admin['nome'].'</option>';
            }
        }

        $modal_body = '';
        $modal_body .= '<input type="hidden" name="confirme_edite_revendedor" value="'.$id.'">';
        $modal_body .= '<div class="form-row row">';
            $modal_body .= '<div class="form-group col-md col">
                                <label for="usuario">Usuario:</label>
                                <input type="text" class="form-control" name="usuario" value="'.$user.'">
                            </div>';
            $modal_body .= ' <div class="form-group col-md col">
                            <label for="senha">Senha:</label>
                            <input type="text" class="form-control" name="senha" value="'.$pass.'">
                        </div>';
        $modal_body .= '</div>';
        $modal_body .= '<div class="form-group pb-2"> Selecione Um plano </div>';
        $modal_body .= "<select class='form-select form-control' name='plano'>$option</select>";

        $modal_footer = "<button type='button' onclick='enviardados(\"modal_master_form\", \"revendedores.php\")' class='btn btn-info waves-effect waves-light' >Salvar</button><button type='button' class='btn btn-danger' data-bs-dismiss='modal' aria-label='Close'>Cancelar</button>";

        $resposta = [
            'modal_header_class'=> "d-block modal-header bg-info text-white m-2",
            'modal_titulo'=> "Info do Revendedor",
            'modal_body'=> $modal_body,
            'modal_footer'=> $modal_footer
        ];

        return $resposta;
    } else {
        return 0;
    }
}

function confirme_editar_revendedor($id, $usuario, $senha, $plano)
{
    $usuario = preg_replace('/[^a-zA-Z0-9#@!%&*]/', '', $usuario);
    $senha = preg_replace('/[^a-zA-Z0-9#@!%&*]/', '', $senha);

    if (empty($usuario) || empty($senha)) {
        $resposta = [
            'title' => 'Usuário ou senha inválidos.',
            'icon' => 'error'
        ];
        return $resposta;
    }
    $conexao = conectar_bd();

    $token = isset($_SESSION['token']) ? $_SESSION['token'] : "0";
    $admin_id = isset($_SESSION['admin_id']) ? $_SESSION['admin_id'] : null;

    $sql = "SELECT a.*
            FROM admin a 
            LEFT JOIN admin aa ON aa.id = :id  
            WHERE a.id = :admin_id AND a.token = :token";
        $stmt = $conexao->prepare($sql);
        $stmt->bindParam(':admin_id', $admin_id, PDO::PARAM_INT);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':token', $token);
        $stmt->execute();
    $resposta = [];    
    if ($row = $stmt->fetch()) {

            $sql_update = "UPDATE admin SET ";
            $updates = [];

            if (!empty($usuario)) {
                $updates[] = "user = :user";
            }
            if (!empty($senha)) {
                $updates[] = "pass = :pass";
            }
            if (!empty($plano)) {
                $updates[] = "plano = :plano";
            }

            $sql_update .= implode(", ", $updates);
            $sql_update .= " WHERE id = :id";

            $stmt_update = $conexao->prepare($sql_update);

            if (!empty($usuario)) {
                $stmt_update->bindParam(':user', $usuario);
            }
            if (!empty($senha)) {
                $stmt_update->bindParam(':pass', $senha);
            }
            if (!empty($plano)) {
                $stmt_update->bindParam(':plano', $plano);
            }

            $stmt_update->bindParam(':id', $id, PDO::PARAM_INT);

            if ($stmt_update->execute()) {
                $resposta['title'] = "Concluído!";
                $resposta['msg'] = "Revendedor editado com sucesso";
                $resposta['icon'] = "success";
            } else {
                $resposta['title'] = "Erro!";
                $resposta['msg'] = "Erro ao editar Revendedor";
                $resposta['icon'] = "error";
            }

            return $resposta;
    } else {
        return 0;
    }
}

function edite_admin()
{
    $conexao = conectar_bd();

    $token = isset($_SESSION['token']) ? $_SESSION['token'] : "0";
    $admin_id = isset($_SESSION['admin_id']) ? $_SESSION['admin_id'] : null;

    $sql = "SELECT *
            FROM admin 
            WHERE id = :admin_id AND token = :token";
        $stmt = $conexao->prepare($sql);
        $stmt->bindParam(':token', $token, PDO::PARAM_STR);
        $stmt->bindParam(':admin_id', $admin_id, PDO::PARAM_INT);
        $stmt->execute();

    $option = "";
    $option_servidores = "";
    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

        extract($row);

        $modal_body = '';
        $modal_body .= '<input type="hidden" name="confirme_edite_admin">';
        $modal_body .= '<div class="form-row row">';
            $modal_body .= '<div class="form-group col-md col">
                                <label for="usuario">Usuario:</label>
                                <input type="text" class="form-control" name="usuario" value="'.$user.'">
                            </div>';
            $modal_body .= ' <div class="form-group col-md col">
                            <label for="senha">Senha:</label>
                            <input type="text" class="form-control" name="senha" value="'.$pass.'">
                        </div>';
        $modal_body .= '</div>';
        $modal_body .= '<label class="pt-3" for="tipo_link">Configurações:</label>';
        $modal_body .= '<div class="form-group col-md col">
                                <label for="tipo_link">Tipo de link:</label>
                                <select class="form-control form-select" name="tipo_link">
                                    <option value="padrao" ' . (($tipo_link === 'padrao' || is_null($tipo_link)) ? 'selected' : '') . '>Padrão (com proteção)</option>
                                    <option value="padrao2" ' . (($tipo_link === 'padrao2') ? 'selected' : '') . '>Padrão (sem proteção)</option>
                                    <option value="link_direto" ' . (($tipo_link === 'link_direto') ? 'selected' : '') . '>Link direto</option>
                                    <option value="link_direto2" ' . (($tipo_link === 'link_direto') ? 'selected' : '') . '>Link direto (Com proteção)</option>
                                </select>
                            </div>';

        $modal_footer = "<button type='button' onclick='enviardados(\"modal_master_form\", \"revendedores.php\")' class='btn btn-info waves-effect waves-light' >Editar</button><button type='button' class='btn btn-danger' data-bs-dismiss='modal' aria-label='Close'>Cancelar</button>";

        $resposta = [
            'modal_header_class'=> "d-block modal-header bg-info text-white m-2",
            'modal_titulo'=> "Alterar a sua senha",
            'modal_body'=> $modal_body,
            'modal_footer'=> $modal_footer
        ];

        return $resposta;
    } else {
        return 0;
    }
}

function confirme_edite_admin($usuario, $senha, $tipo_link_novo)
{
    $usuario = preg_replace('/[^a-zA-Z0-9#@!%&*]/', '', $usuario);
    $senha = preg_replace('/[^a-zA-Z0-9#@!%&*]/', '', $senha);

    $conexao = conectar_bd();
    $token = $_SESSION['token'] ?? "0";
    $admin_id = $_SESSION['admin_id'] ?? null;

    $sql = "SELECT * FROM admin WHERE id = :admin_id AND token = :token";
    $stmt = $conexao->prepare($sql);
    $stmt->bindParam(':admin_id', $admin_id, PDO::PARAM_INT);
    $stmt->bindParam(':token', $token, PDO::PARAM_STR);
    $stmt->execute();

    if ($row = $stmt->fetch()) {
        extract($row);
        $updates = [];
        $params = [];

        if ($user !== $usuario) {
            $updates[] = "user = :user";
            $params[':user'] = $usuario;
        }
        if ($pass !== $senha) {
            $updates[] = "pass = :pass";
            $params[':pass'] = $senha;
        }
        if ($tipo_link !== $tipo_link_novo) {
            $updates[] = "tipo_link = :tipo_link";
            $params[':tipo_link'] = $tipo_link_novo;
        }

        if (!empty($updates)) {
            $sql_update = "UPDATE admin SET " . implode(", ", $updates) . " WHERE id = :id";
            $stmt_update = $conexao->prepare($sql_update);
            $stmt_update->bindParam(':id', $admin_id, PDO::PARAM_INT);

            foreach ($params as $key => $value) {
                $stmt_update->bindValue($key, $value);
            }

            if ($stmt_update->execute()) {
                if (isset($params[':user']) || isset($params[':pass'])) {
                    $token = bin2hex(random_bytes(32));
                    $_SESSION['token'] = $token;
                    $_SESSION['username'] = $usuario;
                    $_SESSION['password'] = $senha;
                    $_SESSION['last_activity'] = time();

                    $sql_update2 = "UPDATE admin SET token = :token WHERE id = :id";
                    $stmt_update2 = $conexao->prepare($sql_update2);
                    $stmt_update2->bindParam(':token', $token);
                    $stmt_update2->bindParam(':id', $admin_id, PDO::PARAM_INT);
                    $stmt_update2->execute();
                }

                $resposta = [
                    'title' => "Concluído!",
                    'msg' => "Admin editado com sucesso!",
                    'icon' => "success"
                ];

                if ($tipo_link !== $tipo_link_novo) {
                    $sql_update = "
                        UPDATE streams SET tipo_link = :tipo_link;
                        UPDATE series_episodes SET tipo_link = :tipo_link;
                    ";

                    $stmt = $conexao->prepare($sql_update);
                    $stmt->bindParam(':tipo_link', $tipo_link_novo);
                    $stmt->execute();

                    $resposta['msg'] .= " Tipo de link modificado para todos os links!";
                }

                return $resposta;
            } else {
                return [
                    'title' => "Erro!",
                    'msg' => "Erro ao editar Admin",
                    'icon' => "error"
                ];
            }
        } else {
            return [
                'title' => "Nenhuma alteração feita.",
                'msg' => "Os dados informados são idênticos aos já existentes.",
                'icon' => "info"
            ];
        }
    } else {
        return 0;
    }
}

function edite_admin_revenda()
{
    $conexao = conectar_bd();

    $token = isset($_SESSION['token']) ? $_SESSION['token'] : "0";
    $admin_id = isset($_SESSION['admin_id']) ? $_SESSION['admin_id'] : null;

    $sql = "SELECT *
            FROM admin 
            WHERE id = :admin_id AND token = :token";
        $stmt = $conexao->prepare($sql);
        $stmt->bindParam(':token', $token, PDO::PARAM_STR);
        $stmt->bindParam(':admin_id', $admin_id, PDO::PARAM_INT);
        $stmt->execute();

    $option = "";
    $option_servidores = "";
    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

        extract($row);

        $modal_body = '';
        $modal_body .= '<input type="hidden" name="confirme_edite_admin_revenda">';
        $modal_body .= '<div class="form-row">';
            $modal_body .= ' <div class="form-group col-md col">
                            <label for="senha_atual">Senha Atual:</label>
                            <input type="text" class="form-control" name="senha_atual" value="">
                        </div>';
            $modal_body .= ' <div class="form-group col-md col">
                            <label for="nova_senha">Nova Senha:</label>
                            <input type="text" class="form-control" name="nova_senha" value="">
                        </div>';
            $modal_body .= ' <div class="form-group col-md col">
                            <label for="confime_senha">Confirme a nova Senha:</label>
                            <input type="text" class="form-control" name="confime_senha" value="">
                        </div>';
        $modal_body .= '</div>';

        $modal_footer = "<button type='button' onclick='enviardados(\"modal_master_form\", \"revendedores.php\")' class='btn btn-info waves-effect waves-light' >Editar</button><button type='button' class='btn btn-danger' data-bs-dismiss='modal' aria-label='Close'>Cancelar</button>";

        $resposta = [
            'modal_header_class'=> "d-block modal-header bg-info text-white m-2",
            'modal_titulo'=> "Alterar a sua senha",
            'modal_body'=> $modal_body,
            'modal_footer'=> $modal_footer
        ];

        return $resposta;
    } else {
        return 0;
    }
}

function confirme_edite_admin_revenda($senha_atual, $nova_senha, $confime_senha)
{
    $senha_atual = preg_replace('/[^a-zA-Z0-9#@!%&*]/', '', $senha_atual);
    $nova_senha = preg_replace('/[^a-zA-Z0-9#@!%&*]/', '', $nova_senha);
    $confime_senha = preg_replace('/[^a-zA-Z0-9#@!%&*]/', '', $confime_senha);

    $conexao = conectar_bd();

    $token = isset($_SESSION['token']) ? $_SESSION['token'] : "0";
    $admin_id = isset($_SESSION['admin_id']) ? $_SESSION['admin_id'] : null;

    $sql = "SELECT *
            FROM admin 
            WHERE id = :admin_id AND token = :token AND pass = :senha_atual";
        $stmt = $conexao->prepare($sql);
        $stmt->bindParam(':admin_id', $admin_id, PDO::PARAM_INT);
        $stmt->bindParam(':token', $token, PDO::PARAM_STR);
        $stmt->bindParam(':senha_atual', $senha_atual, PDO::PARAM_STR);
        $stmt->execute();
    $resposta = [];    
    if ($row = $stmt->fetch()) {

            $sql_update = "UPDATE admin SET ";
            $updates = [];

            if (!empty($confime_senha)) {
                $updates[] = "pass = :pass";
            }

            $sql_update .= implode(", ", $updates);
            $sql_update .= " WHERE id = :id";

            $stmt_update = $conexao->prepare($sql_update);

            if (!empty($confime_senha)) {
                $stmt_update->bindParam(':pass', $confime_senha);
            }

            $stmt_update->bindParam(':id', $admin_id, PDO::PARAM_INT);

            if ($stmt_update->execute()) {

                $token = bin2hex(random_bytes(32));

                $_SESSION['token'] = $token;
                $_SESSION['password'] = $confime_senha;
                $_SESSION['last_activity'] = time();
                $sql_update2 = "UPDATE admin SET token = '$token' WHERE id = '$admin_id' and pass = '$confime_senha'";
                $conexao->exec($sql_update2);
                $resposta['title'] = "Concluído!";
                $resposta['msg'] = "Senha modificada com sucesso";
                $resposta['icon'] = "success";
            } else {
                $resposta['title'] = "Erro!";
                $resposta['msg'] = "Erro ao modificar Senha";
                $resposta['icon'] = "error";
            }

            return $resposta;
    } else {
        $resposta['title'] = "Erro!";
        $resposta['msg'] = "A senha atual esta incorreta!";
        $resposta['icon'] = "error";
        return $resposta;
    }
}
function add_creditos($id, $usuario)
{

        $modal_body = "<input type=\"hidden\" name=\"confirme_add_creditos\" value='$id'></div>";
        $modal_body .= "<label>Adicionar Creditos:</label>";
        $modal_body .= "<input type='number' name='creditos' class='form-control' placeholder='Creditos' min='-10' value='10'>";
        $modal_body .= "<small class='form-text text-muted'>-Ao colocar um valor negativo ira remover os creditos do revendedor e sera adicionado ao seu saldo</small><br>";

        $modal_footer = "<button type='button' class='btn btn-success waves-effect waves-light' onclick='enviardados(\"modal_master_form\", \"revendedores.php\")'>Confirmar</button>";

        $resposta = [
            'modal_header_class'=> "d-block modal-header bg-success text-white",
            'modal_titulo'=> "Adcionar Creditos para ($usuario)",
            'modal_body'=> $modal_body,
            'modal_footer'=> $modal_footer
        ];

    return $resposta;
}

function confirme_add_creditos($id, $addcreditos)
{
    $conexao = conectar_bd();

    $token = isset($_SESSION['token']) ? $_SESSION['token'] : "0";
    $admin_id = isset($_SESSION['admin_id']) ? $_SESSION['admin_id'] : null;

    $sql = "SELECT a.creditos as creditos, a.admin as admin, aa.creditos as rev_creditos
            FROM admin a 
            LEFT JOIN admin aa ON aa.id = :id 
            WHERE a.id = :admin_id AND a.token = :token";
        $stmt = $conexao->prepare($sql);
        $stmt->bindParam(':admin_id', $admin_id, PDO::PARAM_INT);
        $stmt->bindParam(':token', $token);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

    $resposta = []; 

    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

        extract($row);

        if ($addcreditos < 0) {

        }else {

        }

        if ($admin == 1) {
            $addcreditos_positivo = abs($addcreditos);
            if ($addcreditos >= 0) {
                $sql_update = "UPDATE admin SET creditos = creditos + :creditos WHERE id = :id";
                $stmt = $conexao->prepare($sql_update);
                $stmt->bindParam(':creditos', $addcreditos);
                $stmt->bindParam(':id', $id);
                if ($stmt->execute()) {
                    $resposta['title'] = "Concluido!";
                    $resposta['msg'] = "Foi Adicionado ".$addcreditos." Creditos. ";
                    $resposta['icon'] = "success";
                    return $resposta;
                }else{
                    $resposta['title'] = "Erro!";
                    $resposta['msg'] = "Nao foi possivel adicionar creditos";
                    $resposta['icon'] = "error";
                    return $resposta;
                }
            }else if($rev_creditos >= $addcreditos_positivo) {
                $sql_update = "UPDATE admin SET creditos = creditos + :creditos WHERE id = :id";
                $stmt = $conexao->prepare($sql_update);
                $stmt->bindParam(':creditos', $addcreditos);
                $stmt->bindParam(':id', $id);
                if ($stmt->execute()) {
                    $resposta['title'] = "Concluido!";
                    $resposta['msg'] = "Foi Removido ".$addcreditos_positivo." Credito(s). ";
                    $resposta['icon'] = "success";
                    return $resposta;
                }else{
                    $resposta['title'] = "Erro!";
                    $resposta['msg'] = "Nao foi possivel remover creditos";
                    $resposta['icon'] = "error";
                    return $resposta;
                }

            }else {
                $resposta['title'] = "Aviso!";
                $resposta['msg'] = "O revendedor nao tem creditos suficientes para ser removidos";
                $resposta['icon'] = "error";
                return $resposta;
            }

        } elseif ($admin != 1 && $creditos >= 1 && $creditos>= $addcreditos) {
            $addcreditos_positivo = abs($addcreditos);
            if ($addcreditos >= 0) {
                $sql_update = "UPDATE admin SET creditos = creditos + :creditos WHERE id = :id";
                $stmt = $conexao->prepare($sql_update);
                $stmt->bindParam(':creditos', $addcreditos);
                $stmt->bindParam(':id', $id);
                if ($stmt->execute()) {
                    $sql_update = "UPDATE admin SET creditos = creditos - $addcreditos WHERE id = '$admin_id'";
                    if ($conexao->exec($sql_update)) {

                    }else{
                        $resposta['title'] = "Erro!";
                        $resposta['msg'] = "ouve um erro ao remover os creditos do seu saldo";
                        $resposta['icon'] = "error";
                        return $resposta;
                    }
                    $resposta['title'] = "Concluido!";
                    $resposta['msg'] = "Foi Adicionado ".$addcreditos." Creditos. ";
                    $resposta['icon'] = "success";
                    return $resposta;
                }else{
                    $resposta['title'] = "Erro!";
                    $resposta['msg'] = "Nao foi possivel adicionar creditos";
                    $resposta['icon'] = "error";
                    return $resposta;
                }
            } else if($rev_creditos >= $addcreditos_positivo) {
                $sql_update = "UPDATE admin SET creditos = creditos + :creditos WHERE id = :id";
                $stmt = $conexao->prepare($sql_update);
                $stmt->bindParam(':creditos', $addcreditos);
                $stmt->bindParam(':id', $id);
                if ($stmt->execute()) {
                    $sql_update = "UPDATE admin SET creditos = creditos + $addcreditos_positivo WHERE id = '$admin_id'";
                    if ($conexao->exec($sql_update)) {

                    }else{
                        $resposta['title'] = "Erro!";
                        $resposta['msg'] = "ouve um erro ao remover os creditos do seu saldo";
                        $resposta['icon'] = "error";
                        return $resposta;
                    }
                    $resposta['title'] = "Concluido!";
                    $resposta['msg'] = "Foi Removido ".$addcreditos_positivo." Credito(s). ";
                    $resposta['icon'] = "success";
                    return $resposta;
                }else{
                    $resposta['title'] = "Erro!";
                    $resposta['msg'] = "Nao foi possivel remover creditos";
                    $resposta['icon'] = "error";
                    return $resposta;
                }

            }else {
                $resposta['title'] = "Aviso!";
                $resposta['msg'] = "O revendedor nao tem creditos suficientes para ser removidos";
                $resposta['icon'] = "error";
                return $resposta;
            }
        }else{
            $resposta['title'] = "Erro!";
            $resposta['msg'] = "voce nao tem creditos suficientes. voce precisa ter 1 ou mais creditos";
            $resposta['icon'] = "error";
        }

        return $resposta;
    } else {
        return 0;
    }
}

function add_revendedor()
{
    $conexao = conectar_bd();

    $token = isset($_SESSION['token']) ? $_SESSION['token'] : "0";
    $admin_id = isset($_SESSION['admin_id']) ? $_SESSION['admin_id'] : null;

    $sql = "SELECT a.plano AS plano, 
                   a.servidores AS rev_servidores, 
                   a.plano AS admin_plano,
                   a.admin AS admin 
            FROM admin a
            WHERE a.id = :admin_id AND a.token = :token";
        $stmt = $conexao->prepare($sql);
        $stmt->bindParam(':token', $token);
        $stmt->bindParam(':admin_id', $admin_id, PDO::PARAM_INT);
        $stmt->execute();

    $option = "";
    $option_servidores = "";
    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

        extract($row);

        $planos_admin = $conexao->query("SELECT * FROM planos_admin WHERE id < $admin_plano");

        while ($p_admin = $planos_admin->fetch()) {

            if ($p_admin['id'] == $plano) {
                $option .= '<option value="'.$p_admin['id'].'" selected>'.$p_admin['nome'].' [Atual]</option>';
            } else {
                $option .= '<option value="'.$p_admin['id'].'">'.$p_admin['nome'].'</option>';
            }
        }

        $modal_body = '';
        $modal_body .= '<input type="hidden" name="confirme_add_revendedor" value="">';
        $modal_body .= '<div class="form-row row">';
            $modal_body .= '<div class="form-group col-md col">
                                <label for="usuario">Usuario:</label>
                                <input type="text" class="form-control" name="usuario" value="">
                            </div>';
            $modal_body .= ' <div class="form-group col-md col">
                            <label for="senha">Senha:</label>
                            <input type="text" class="form-control" name="senha" value="">
                        </div>';
        $modal_body .= '</div>';
        $modal_body .= '<div class="form-group pb-2"> Selecione Um plano </div>';
        $modal_body .= "<select class='form-select form-control' name='plano'>$option</select>";    

        $modal_footer = "<button type='button' onclick='enviardados(\"modal_master_form\", \"revendedores.php\")' class='btn btn-success waves-effect waves-light' >Adicionar</button><button type='button' class='btn btn-danger' data-bs-dismiss='modal' aria-label='Close'>Cancelar</button>";

        $resposta = [
            'modal_header_class'=> "d-block modal-header bg-success text-white m-2",
            'modal_titulo'=> "Adicionar Revendedor",
            'modal_body'=> $modal_body,
            'modal_footer'=> $modal_footer
        ];

        return $resposta;
    } else {
        return 0;
    }
}

function confirme_add_revendedor($usuario, $senha, $plano)
{
    $usuario = preg_replace('/[^a-zA-Z0-9#@!%&*]/', '', $usuario);
    $senha = preg_replace('/[^a-zA-Z0-9#@!%&*]/', '', $senha);

    if (empty($usuario) || empty($senha)) {
        $resposta = [
            'title' => 'Usuário ou senha inválidos.',
            'icon' => 'error'
        ];
        return $resposta;
    }
    $conexao = conectar_bd();

    $token = isset($_SESSION['token']) ? $_SESSION['token'] : "0";
    $admin_id = isset($_SESSION['admin_id']) ? $_SESSION['admin_id'] : null;

    $sql = "SELECT a.plano AS plano,
                   a.plano AS admin_plano,
                   a.admin AS admin 
            FROM admin a
            WHERE a.id = :admin_id AND a.token = :token";
        $stmt = $conexao->prepare($sql);
        $stmt->bindParam(':token', $token);
        $stmt->bindParam(':admin_id', $admin_id, PDO::PARAM_INT);
        $stmt->execute();

    $resposta = []; 

    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

        $sql_insert = "INSERT INTO admin (user, pass, admin, creditos, criado_por, plano, data_criado) 
                        VALUES (:user, :pass, :admin, :creditos, :criado_por, :plano, :data_criado)";
        $stmt_insert = $conexao->prepare($sql_insert);

            $admin = 0;
            $creditos = 0;
            $data_criado = date("Y-m-d H:i:s");

            $stmt_insert->bindParam(':user', $usuario, PDO::PARAM_STR);
            $stmt_insert->bindParam(':pass', $senha, PDO::PARAM_STR); 
            $stmt_insert->bindParam(':admin', $admin, PDO::PARAM_INT); 
            $stmt_insert->bindParam(':criado_por', $admin_id, PDO::PARAM_INT);
            $stmt_insert->bindParam(':plano', $plano, PDO::PARAM_STR); 
            $stmt_insert->bindParam(':creditos', $creditos, PDO::PARAM_INT); 
            $stmt_insert->bindParam(':data_criado', $data_criado, PDO::PARAM_STR);

           if ($stmt_insert->execute()) {
                $lastInsertedId = $conexao->lastInsertId();

                $nome = "Completo";
                $valor = 30;
                $custo_por_credito = 5;

                $sql_insert_planos = "INSERT INTO planos (nome, admin_id, valor, custo_por_credito) 
                                      VALUES (:nome, :admin_id, :valor, :custo_por_credito)";
                $stmt_insert_planos = $conexao->prepare($sql_insert_planos);
                $stmt_insert_planos->bindParam(':nome', $nome, PDO::PARAM_STR);
                $stmt_insert_planos->bindParam(':admin_id', $lastInsertedId, PDO::PARAM_INT);
                $stmt_insert_planos->bindParam(':valor', $valor, PDO::PARAM_INT);
                $stmt_insert_planos->bindParam(':custo_por_credito', $custo_por_credito, PDO::PARAM_INT);

                if ($stmt_insert_planos->execute()) {
                    $resposta['title'] = "Concluído!";
                    $resposta['msg'] = "Revendedor criado com sucesso";
                    $resposta['icon'] = "success";

                    return $resposta;
                }

            $resposta['title'] = "Concluído!";
            $resposta['msg'] = "Revendedor criado com sucesso";
            $resposta['icon'] = "success";

            return $resposta;
        } else {
            $resposta['title'] = "Erro!";
            $resposta['msg'] = "Erro ao criar revendedor";
            $resposta['icon'] = "error";

            return $resposta;
        }

    } else {
        return 0;
    }

}

function delete_revendedor($id, $usuario)
{

        $modal_body = "<input type=\"hidden\" name=\"confirme_delete_revendedor\" value='$id'></div>";
        $modal_body .= "<input type=\"hidden\"  id=\"usuario\" name=\"usuario\" value='$usuario'></div>";
        $modal_body .= "Tem certeza de que deseja excluir o revendedor ($usuario) ? Essa ação nao pode ser desfeita, ira apagar todos os clientes/revendedores/sub-revendedores e seus respectivo clientes";

        $modal_footer = "<button type='button' class='btn btn-primary btn-sm' data-bs-dismiss='modal' aria-label='Close'>Cancelar</button><button type='button' class='btn btn-danger btn-sm' onclick='enviardados(\"modal_master_form\", \"revendedores.php\")'>EXCLUIR</button>";

        $resposta = [
            'modal_header_class'=> "d-block modal-header bg-danger text-white",
            'modal_titulo'=> "EXCLUIR REVENDEDOR",
            'modal_body'=> $modal_body,
            'modal_footer'=> $modal_footer
        ];

    return $resposta;
}

function confirme_delete_revendedor($id, $usuario)
{
    $conexao = conectar_bd();

    $token = isset($_SESSION['token']) ? $_SESSION['token'] : "0";
    $admin_id = isset($_SESSION['admin_id']) ? $_SESSION['admin_id'] : null;

    $sql = "SELECT a.* 
            FROM admin a
            LEFT JOIN admin aa ON aa.id = :id  
            WHERE a.id = :admin_id AND a.token = :token AND aa.id = :id AND aa.user = :usuario";
        $stmt = $conexao->prepare($sql);
        $stmt->bindParam(':token', $token);
        $stmt->bindParam(':admin_id', $admin_id, PDO::PARAM_INT);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':usuario', $usuario);
        $stmt->execute();

    $resposta = []; 

    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

        $sql_delete = "DELETE FROM admin WHERE id = '$id' and user = '$usuario'";

        if ($conexao->exec($sql_delete)) {
            $sql_delete_plano = "DELETE FROM planos WHERE admin_id = '$id'";
            $conexao->exec($sql_delete_plano);

            $sql_delete_clientes = "DELETE FROM clientes WHERE admin_id = '$id'";
            $conexao->exec($sql_delete_clientes);
            deletaradmins();
            $resposta['title'] = "Sucesso!";
            $resposta['msg'] = "Revendedor deletado com sucesso!";
            $resposta['icon'] = "success";
            return $resposta;
        } else {
            $resposta['title'] = "Erro!";
            $resposta['msg'] = "Erro ao deletar Revendedor.";
            $resposta['icon'] = "error";
            return $resposta;
        }

        return $resposta;

    } else {
        return 0;
    }
}