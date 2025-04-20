<?php
function editar_canal($id)
{
    $conexao = conectar_bd();

    $token = isset($_SESSION['token']) ? $_SESSION['token'] : "0";
    $admin_id = isset($_SESSION['admin_id']) ? $_SESSION['admin_id'] : null;
    
    $sql = "SELECT s.*
            FROM streams s 
            LEFT JOIN admin a ON a.id = :admin_id 
            WHERE s.id = :id AND a.token = :token";
        $stmt = $conexao->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':admin_id', $admin_id, PDO::PARAM_INT);
        $stmt->bindParam(':token', $token, PDO::PARAM_STR);
        $stmt->execute();


    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        
        extract($row);
        
        $categoria = $conexao->query("SELECT * FROM categoria WHERE admin_id = '$admin_id' and type = 'live' ORDER BY nome ASC");
        $categoriaOptions = "";
        $categoriaOptions2 = "";
        while ($lista_categorias = $categoria->fetch()) {
            

            if ($lista_categorias['id'] == $category_id) {
                $categoriaOptions .= '<option value="'.$lista_categorias['id'].'"> Categoria Atual => '.$lista_categorias['nome'].' </option>';
            }else{
                $categoriaOptions2 .= '<option value="'.$lista_categorias['id'].'">'.$lista_categorias['nome'].'</option>';
            }
        }

        $modal_body = '';
        $modal_body .= '<input type="hidden" name="confirme_editar_canal" value="'.$id.'">';
        $modal_body .= '<div class="form-group">
                        <label for="nome">Nome do canal:</label>
                        <input type="text" class="form-control" id="nome" name="nome" value="'.$name.'" autocomplete="off">
                        </div>';
        $modal_body .= '<div class="form-group">
                        <label for="link">Link do canal:</label>
                        <input type="text" class="form-control" name="link" value="'.$link.'" autocomplete="off" placeholder="http://servidor.com/live/1234/1234/234.ts" style="filter: blur(0px);">
                        </div>';
        $modal_body .= '<div class="form-group">
                        <label for="logo">Link do Logo (capa do canal):</label>
                        <input type="text" class="form-control" name="logo" value="'.$stream_icon.'" autocomplete="off" placeholder="http://servidor.com/canal.png">
                        </div>';
        $modal_body .= '<div class="form-row row">';
            $modal_body .= '<div class="form-group col-12 text-center">
                            <label for="Categoria">Categoria</label>
                            <select class="form-control form-select"name="Categoria">
                        '.$categoriaOptions.' '.$categoriaOptions2.'
                        </select>
                            </div>';
            $modal_body .= ' <div class="form-group col-md col text-center">
                            <label for="adulto">É um canal adulto?</label>
                            <select class="form-control form-select" name="adulto">
                            <option value="'.$is_adult.'"">'. ($is_adult == 0 ? "Não" : "Sim") .'</option>';
                            switch ($is_adult) {
                                case "0":
                                    $modal_body .= "<option value='1'>Mudar PARA ( SIM )</option>";
                                    break;
                                case "1":
                                    $modal_body .= "<option value='0'>Mudar PARA ( NAO )</option>";
                                    break;
                            }
            $modal_body .= '</select>
                            </div>';
            $modal_body .= '<div class="form-group col-md col text-center">
                                <label for="tipo_link">Tipo de link:</label>
                                <select class="form-control form-select" name="tipo_link">
                                    <option value="padrao" ' . (($tipo_link === 'padrao' || is_null($tipo_link)) ? 'selected' : '') . '>Padrão (com proteção)</option>
                                    <option value="padrao2" ' . (($tipo_link === 'padrao2') ? 'selected' : '') . '>Padrão (sem proteção)</option>
                                    <option value="link_direto" ' . (($tipo_link === 'link_direto') ? 'selected' : '') . '>Link direto</option>
                                    <option value="link_direto2" ' . (($tipo_link === 'link_direto') ? 'selected' : '') . '>Link direto (Com proteçao)</option>
                                </select>
                            </div>';

        $modal_body .= '</div>';



        $modal_footer = "<button type='button' onclick='enviardados(\"modal_master_form\", \"canais.php\")' class='btn btn-info waves-effect waves-light' >Salvar</button><button type='button' class='btn btn-danger' data-bs-dismiss='modal' aria-label='Close'>Cancelar</button>";




        $resposta = [
            'modal_header_class'=> "d-block modal-header bg-info text-white",
            'modal_titulo'=> "Editar canal ( $name )",
            'modal_body'=> $modal_body,
            'modal_footer'=> $modal_footer
        ];

    return $resposta;
    } else {
        return 0;
    }
}

function confirme_editar_canal($id, $name, $link, $logo, $category_id, $is_adult, $tipo_link)
{
    $conexao = conectar_bd();

    $token = isset($_SESSION['token']) ? $_SESSION['token'] : "0";
    $admin_id = isset($_SESSION['admin_id']) ? $_SESSION['admin_id'] : null;

    $is_adult = preg_replace("/[^0-9]/", "", $is_adult);
    $category_id = preg_replace("/[^0-9]/", "", $category_id);
    
    $sql = "SELECT *
            FROM admin
            WHERE id = :admin_id AND token = :token";
        $stmt = $conexao->prepare($sql);
        $stmt->bindParam(':admin_id', $admin_id, PDO::PARAM_INT);
        $stmt->bindParam(':token', $token);
        $stmt->execute();

    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

            // Preparar a atualização
            $sql_update = "UPDATE streams SET ";
            $updates = [];

            if (!empty($is_adult)) {
                $updates[] = "is_adult = :is_adult";
            }else{
                $updates[] = "is_adult = 0";
            }
            if (!empty($name)) {
                $updates[] = "name = :name";
            }
            if (!empty($link)) {
                $updates[] = "link = :link";
            }
            if (!empty($logo)) {
                $updates[] = "stream_icon = :stream_icon";
            }
            if (!empty($category_id)) {
                $updates[] = "category_id = :category_id";
            }
            if (!empty($tipo_link)) {
                $updates[] = "tipo_link = :tipo_link";
            }
            // Montar a query de atualização
            $sql_update .= implode(", ", $updates);
            $sql_update .= " WHERE id = :id";

            // Preparar o statement de atualização
            $stmt_update = $conexao->prepare($sql_update);

            // Bind dos parâmetros
            if (!empty($is_adult)) {
                $stmt_update->bindParam(':is_adult', $is_adult, PDO::PARAM_INT);
            }
            if (!empty($name)) {
                $stmt_update->bindParam(':name', $name, PDO::PARAM_STR);
            }
            if (!empty($link)) {
                $stmt_update->bindParam(':link', $link, PDO::PARAM_STR);
            }
            if (!empty($logo)) {
                $stmt_update->bindParam(':stream_icon', $logo, PDO::PARAM_STR);
            }
            if (!empty($category_id)) {
                $stmt_update->bindParam(':category_id', $category_id, PDO::PARAM_INT);
            }
            if (!empty($tipo_link)) {
                $stmt_update->bindParam(':tipo_link', $tipo_link, PDO::PARAM_STR);
            }

            // Bind dos parâmetros de ID e admin_id
            $stmt_update->bindParam(':id', $id, PDO::PARAM_INT);

            // Executar o statement de atualização
            if ($stmt_update->execute()) {
                $resposta['title'] = "Concluído!";
                $resposta['msg'] = "Canal editado com sucesso";
                $resposta['icon'] = "success";
            } else {
                $resposta['title'] = "Erro!";
                $resposta['msg'] = "Erro ao editar Canal";
                $resposta['icon'] = "error";
            }

            return $resposta;
        } else {
            // Caso não encontre o cliente com o ID e token especificados
            $resposta['title'] = "Erro!";
            $resposta['msg'] = "Canal não encontrado";
            $resposta['icon'] = "error";
            return $resposta;
        }

}

function delete_canal($id, $nome)
{

        $modal_body = "<input type=\"hidden\"  name=\"confirme_delete_canal\" value='$id'></div>";
        $modal_body .= "<input type=\"hidden\"  name=\"nome\" value='$nome'></div>";
        $modal_body .= "Tem certeza de que deseja excluir o canal ($nome) ?";


        $modal_footer = "<button type='button' class='btn btn-primary btn-sm' data-bs-dismiss='modal' aria-label='Close'>Cancelar</button><button type='button' class='btn btn-danger btn-sm' onclick='enviardados(\"modal_master_form\", \"canais.php\")'>EXCLUIR</button>";




        $resposta = [
            'modal_header_class'=> "d-block modal-header bg-danger text-white",
            'modal_titulo'=> "EXCLUIR Canal",
            'modal_body'=> $modal_body,
            'modal_footer'=> $modal_footer
        ];

    return $resposta;
}

function confirme_delete_canal($id, $name)
{
    $conexao = conectar_bd();
    $token = isset($_SESSION['token']) ? $_SESSION['token'] : "0";
    $admin_id = isset($_SESSION['admin_id']) ? $_SESSION['admin_id'] : null;

    $id = preg_replace("/[^0-9]/", "", $id);
    
    $sql = "SELECT *
            FROM admin
            WHERE id = :admin_id AND token = :token";
        $stmt = $conexao->prepare($sql);
        $stmt->bindParam(':admin_id', $admin_id, PDO::PARAM_INT);
        $stmt->bindParam(':token', $token);
        $stmt->execute();

    $resposta = []; 

    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

        $sql_delete = "DELETE FROM streams WHERE id = :id";

        $stmt_delete = $conexao->prepare($sql_delete);

        // Bind dos parâmetros
        $stmt_delete->bindParam(':id', $id, PDO::PARAM_INT);

        // Executa a query
        if ($stmt_delete->execute()) {
            $resposta['title'] = "Sucesso!";
            $resposta['msg'] = "Canal deletado com sucesso!";
            $resposta['icon'] = "success";
        } else {
            $resposta['title'] = "Erro!";
            $resposta['msg'] = "Erro ao deletar canal.";
            $resposta['icon'] = "error";
        }

        return $resposta;
    } else {
        return 0;
    }
}

function adicionar_canal()
{
    $conexao = conectar_bd();

    $token = isset($_SESSION['token']) ? $_SESSION['token'] : "0";
    $admin_id = isset($_SESSION['admin_id']) ? $_SESSION['admin_id'] : null;
        
    $sql = "SELECT a.id as admin_id, a.admin as administrador
            FROM admin a
            WHERE a.id = :admin_id AND a.token = :token";
        $stmt = $conexao->prepare($sql);
        $stmt->bindParam(':admin_id', $admin_id, PDO::PARAM_INT);
        $stmt->bindParam(':token', $token);
        $stmt->execute();

    $option = "";
    if ($row = $stmt->fetch()) {

        // Abre conexão com o banco de dados "planos"
        $categoria = $conexao->query("SELECT * FROM categoria WHERE admin_id = '$admin_id' and type = 'live' ORDER BY nome ASC");
        $categoriasOptions = "";
        while ($categorias = $categoria->fetch()) {

            $categoriasOptions .= '<option value="'.$categorias['id'].'">'.$categorias['nome'].'</option>';

        }
        $modal_body = "";
        $modal_body .= '<input type="hidden"  name="confirme_adicionar_canal" value=""></div>';
        $modal_body .= '<div class="form-group">
                        <label for="nome">Nome do canal:</label>
                        <input type="text" class="form-control" id="nome" name="nome" value="" autocomplete="off">
                        </div>';
        $modal_body .= '<div class="form-group">
                        <label for="link">Link do canal:</label>
                        <input type="text" class="form-control" name="link" value="" autocomplete="off" placeholder="http://servidor.com/live/1234/1234/234.ts">
                        </div>';
        $modal_body .= '<div class="form-group">
                        <label for="logo">Link do Logo (capa do canal):</label>
                        <input type="text" class="form-control" name="logo" value="" autocomplete="off" placeholder="http://servidor.com/canal.png">
                        </div>';
        $modal_body .= '<div class="form-row row">';
            $modal_body .= '<div class="form-group col-md col text-center">
                            <label for="Categoria">Categoria</label>
                            <select class="form-control form-select"name="Categoria">
                        '.$categoriasOptions.'
                        </select>
                            </div>';
            $modal_body .= ' <div class="form-group col-md col text-center">
                            <label for="adulto">Conteudo adulto?</label>
                            <select class="form-control form-select" name="adulto">
                            <option value="0"> NAO </option>
                            <option value="1"> SIM </option>
                            </select>
                            </div>';
        $modal_body .= '</div>';

        $modal_footer = "<button type='button' class='btn btn-outline-danger btn-sm' data-bs-dismiss='modal' aria-label='Close'>Cancelar</button><button type='button' class='btn btn-primary btn-sm' onclick='enviardados(\"modal_master_form\", \"canais.php\")'>Adicionar</button>";




        $resposta = [
            'modal_header_class'=> "d-block modal-header bg-primary text-white ",
            'modal_titulo'=> "Adiconar Canal",
            'modal_body'=> $modal_body,
            'modal_footer'=> $modal_footer
        ];

        return $resposta;
    } else {
        return 0;
    }
}

function confirme_adicionar_canal($nome, $link, $logo, $Categoria, $is_adult)
{
    $conexao = conectar_bd();
    $resposta = []; 

    $token = isset($_SESSION['token']) ? $_SESSION['token'] : "0";
    $admin_id = isset($_SESSION['admin_id']) ? $_SESSION['admin_id'] : null;

    if (!$Categoria) {
        $resposta['title'] = "Erro!";
        $resposta['msg'] = "A categoria nao foi informada!";
        $resposta['icon'] = "error";
        return $resposta;
    }
    $is_adult = preg_replace("/[^0-9]/", "", $is_adult);
    $Categoria = preg_replace("/[^0-9]/", "", $Categoria);
    
    $sql = "SELECT *
            FROM admin
            WHERE id = :admin_id AND token = :token";
        $stmt = $conexao->prepare($sql);
        $stmt->bindParam(':admin_id', $admin_id, PDO::PARAM_INT);
        $stmt->bindParam(':token', $token);
        $stmt->execute();

    

    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        
        extract($row);
        $type = (preg_match('/\b(?:youtube\.com|youtu\.be)\b/i', $link)) ? true : null;

        if (!$type) {
            $sql_check = "SELECT * FROM streams WHERE link = :link";
            $stmt_check = $conexao->prepare($sql_check);
            $stmt_check->bindParam(':link', $link, PDO::PARAM_STR);
            $stmt_check->execute();

            if ($stmt_check->fetch(PDO::FETCH_ASSOC)) {
                $resposta['title'] = "Erro!";
                $resposta['msg'] = "Canal já Exite!";
                $resposta['icon'] = "error";
                return $resposta;
            }
        }

        

        $sql_insert = "INSERT INTO streams (name, link, stream_icon, stream_type, category_id, is_adult) 
                        VALUES (:name, :link, :stream_icon, :stream_type, :category_id, :is_adult)";
        $stmt_insert = $conexao->prepare($sql_insert);

        // Bind dos parâmetros
        $stmt_insert->bindParam(':name', $nome, PDO::PARAM_STR);
        $stmt_insert->bindParam(':link', $link, PDO::PARAM_STR);
        $stmt_insert->bindParam(':stream_icon', $logo, PDO::PARAM_STR);
        $stmt_insert->bindValue(':stream_type', 'live', PDO::PARAM_STR);
        $stmt_insert->bindValue(':category_id',  $Categoria, PDO::PARAM_STR);
        $stmt_insert->bindParam(':is_adult', $is_adult, PDO::PARAM_STR);

        if ($admin == 1) {
            // Executar a inserção
            if ($stmt_insert->execute()) {
                $resposta['title'] = "Concluído!";
                $resposta['msg'] = "Canal inserido com sucesso";
                $resposta['icon'] = "success";
                return $resposta;
            } else {
                $resposta['title'] = "Erro!";
                $resposta['msg'] = "Erro ao adiconar canal";
                $resposta['icon'] = "error";
                return $resposta;
            }
        }else {
            $resposta['title'] = "Erro!";
            $resposta['msg'] = "Voce nao deveria ter acesso a isso!";
            $resposta['icon'] = "error";
            return $resposta;
        }

        return $resposta;
    } else {
        return 0;
    }
}