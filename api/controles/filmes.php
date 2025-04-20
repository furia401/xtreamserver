<?php
function editar_filmes($id)
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

        $categoria = $conexao->query("SELECT * FROM categoria WHERE admin_id = '$admin_id' and type = 'movie' ORDER BY nome ASC");
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
        $modal_body .= '<input type="hidden" name="confirme_editar_filmes" value="'.$id.'">';
        $modal_body .= '<div class="form-row row">';
            $modal_body .= '<div class="align-content-center col col-12 col-lg-3 d-flex form-group">
                            <img class="logo-img img-thumbnail mx-auto rounded" src="'.$stream_icon.'" alt="'.$name.'" style=" width: 100%; height: 290px; background-color: black; background-repeat: no-repeat; background-position: 50% 50%; background-size: 100%; margin: 5px 0px; float: left; box-shadow: #ff0000 0px 0px 4px 0px; border-radius: 5px;">
                            </div>';
            $modal_body .= '<div class="form-group col-md col">';
                $modal_body .= '<div class="input-group-text m-1 mb-1 row">
                                    <span class="input-group-text col-lg-3" style=" min-width: 200px">Nome do Filme:</span>
                                    <input type="text" class="form-control col" id="nome" name="nome" value="'.$name.'" autocomplete="off">
                                </div>';
                $modal_body .= '<div class="input-group-text m-1 mb-1 row">
                                    <span class="input-group-text col-lg-3" style=" min-width: 200px">Dirigido Por:</span>
                                    <input type="text" class="form-control col" name="director" value="'.$director.'" autocomplete="off" placeholder="ex: Riley Flanagan, Jaume Collet-Serra, Jaume Collet-Serra">
                                </div>';
                $modal_body .= '<div class="input-group-text m-1 mb-1 row">
                                    <span class="input-group-text col-lg-3" style=" min-width: 200px">Data de lançamento:</span>
                                    <input type="date" class="form-control col" name="releasedate" value="'.$releasedate.'" autocomplete="off" placeholder="ex: 05-12-24">
                                </div>';
                $modal_body .= '<div class="input-group-text m-1 mb-1 row">
                                    <span class="input-group-text col-lg-3" style=" min-width: 200px">Duraçao:</span>
                                    <input type="time" step="2" class="form-control col" name="duration" value="'.$duration.'" autocomplete="off" placeholder="ex: 01:59:40">
                                </div>';
                $modal_body .= '<div class="input-group-text m-1 mb-1 row">
                                    <span class="input-group-text col-lg-3" style="min-width: 200px;">Ano:</span>
                                    <select class="form-control col" name="year">
                                        <option value="" disabled selected>Selecione o ano</option>';

                                        for ($i = '1' + date('Y'); $i >= 1900; $i--) {
                                            if ($year == $i) {
                                                $modal_body .= "<option value=\"$i\" selected>$i</option>";
                                            }
                                            $modal_body .= "<option value=\"$i\">$i</option>";
                                        }
                $modal_body .= '</select></div>';
                $modal_body .= '<div class="input-group-text m-1 mb-1 row">
                                    <span class="input-group-text col-lg-3" style=" min-width: 200px">Gênero:</span>
                                    <input type="text" class="form-control col" name="genre" value="'.$genre.'" autocomplete="off" placeholder="ex: Mistério, Thriller, Ação">
                                </div>';
            $modal_body .= '</div>';
        $modal_body .= '</div>';

        $modal_body .= '<div class="form-row input-group-text m-1 row">
                        <span class="input-group-text" style=" min-width: 200px">Elenco:</span>
                        <input type="text" class="form-control " name="actors" value="'.$actors.'" autocomplete="off" placeholder="ex: Taron Egerton, Jason Bateman, Sofia Carson, Danielle Deadwyler, Dean Norris">
                        </div>';
        $modal_body .= '<div class="form-row input-group-text m-1 row">
                        <span class="input-group-text" style=" min-width: 200px">Descriçao:</span>
                        <textarea type="text" class="form-control " name="plot" style="max-height: 150px;height: 100px;" placeholder="Digite ou cole a Descrição aqui">'.$plot.'</textarea>
                        </div>';
        $modal_body .= '<div class="form-row input-group-text m-1 row">
                        <span class="input-group-text" style=" min-width: 200px">Avaliação:</span>
                        <input type="text" class="form-control " name="rating" value="'.$rating.'" placeholder="6.9">
                        </div>';
        $modal_body .= '<div class="form-row input-group-text m-1 row">
                        <span class="input-group-text" style=" min-width: 200px">Classificação de 5:</span>
                        <input type="text" class="form-control " name="rating_5based" value="'.$rating_5based.'" placeholder="valor maximo 5">
                        </div>';
        $modal_body .= '<div class="form-row input-group-text m-1 row">
                        <span class="input-group-text" style=" min-width: 200px">Link da (capa do Filme):</span>
                        <input type="text" class="form-control" id="logo" name="logo" value="'.$stream_icon.'" autocomplete="off" placeholder="http://servidor.com/filme.png">
                        </div>';
        $modal_body .= '<div class="form-row input-group-text m-1 row">
                        <span class="input-group-text" style=" min-width: 200px">background:</span>
                        <input type="text" class="form-control" id="backdrop_path" name="backdrop_path" value="'.$backdrop_path.'" autocomplete="off" placeholder="http://servidor.com/filme.png">
                        </div>';
        $modal_body .= '<div class="form-row row input-group-text m-1 row">';
            $modal_body .= '<div class="form-group col-lg-6 text-center">
                            <label for="Categoria">Categoria</label>
                            <select class="form-control form-select"name="Categoria">
                        '.$categoriaOptions.' '.$categoriaOptions2.'
                        </select>
                            </div>';
            $modal_body .= ' <div class="form-group col-md col text-center">
                            <label for="adulto">É um filme adulto?</label>
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
        $modal_body .= '</div>';
        $modal_body .= '<div class="form-row input-group-text m-1 row">
                        <span class="input-group-text" style=" min-width: 200px">Link do Filme:</span>
                        <input type="text" class="form-control" name="link" value="'.$link.'" autocomplete="off" placeholder="http://servidor.com/live/1234/1234/234.ts" style="filter: blur(0px);">
                        </div>';

        $modal_body .= '<script>
    document.getElementById("logo").addEventListener("input", function() {
        var newLogoUrl = this.value;
        var capaFilme = document.querySelector("#modal_master img.logo-img");
        if (capaFilme) {
            capaFilme.src = newLogoUrl;
        }
    });

    document.getElementById("backdrop_path").addEventListener("input", function() {
        var newLogoBackground = this.value;
        var modalBody = document.getElementById("modal_master-body");
        if (modalBody) {
            var existingStyle = modalBody.style.cssText; 
            var logoBackgroundStyle = `
                background-image: url(\'${newLogoBackground}\');
                background-color: black;
                background-repeat: no-repeat;
                background-position: 50% 50%;
                background-size: cover;
            `;

            modalBody.style.cssText = existingStyle + logoBackgroundStyle;
        }
    });
</script>';

        $modal_footer = "<button type='button' onclick='enviardados(\"modal_master_form\", \"filmes.php\")' class='btn btn-info waves-effect waves-light' >Salvar</button><button type='button' class='btn btn-danger' data-bs-dismiss='modal' aria-label='Close'>Cancelar</button>";

        $modal_master_body_style = "max-height: 720px;";
        if ($backdrop_path) {
            $modal_master_body_style = "max-height: 720px; background-image: url(".$backdrop_path."); background-color: black; background-repeat: no-repeat; background-position: 50% 50%; background-size: cover;";
        }

        $resposta = [
            'modal_header_class'=> "d-block modal-header bg-info text-white m-2",
            'modal_master_body_style'=> $modal_master_body_style,
            'modal_titulo'=> "Editar Filme ( $name )",
            'modal_body'=> $modal_body,
            'modal_footer'=> $modal_footer
        ];

    return $resposta;
    } else {
        return 0;
    }
}

function confirme_editar_filmes($id, $name, $link, $logo, $category_id, $is_adult, $director, $genre, $actors, $plot, $backdrop_path, $releasedate, $duration, $rating, $rating_5based, $year)
{

    $conexao = conectar_bd();

    $token = isset($_SESSION['token']) ? $_SESSION['token'] : "0";
    $admin_id = isset($_SESSION['admin_id']) ? $_SESSION['admin_id'] : null;

    $is_adult = preg_replace("/[^0-9]/", "", $is_adult);
    $category_id = preg_replace("/[^0-9]/", "", $category_id);
    $rating = preg_replace("/[^0-9]/", "", $rating);
    if ($rating === "10") {
        $rating = "10";
    } elseif (strlen($rating) >= 2) {
        $rating = $rating[0] . '.' . $rating[1];
    } else {
        $rating = strlen($rating) === 1 ? $rating . '.0' : '0.0';
    }

    if ($rating_5based > '5') {
        $rating_5based = '5';
    }

    $sql = "SELECT *
            FROM admin
            WHERE id = :admin_id AND token = :token";
        $stmt = $conexao->prepare($sql);
        $stmt->bindParam(':admin_id', $admin_id, PDO::PARAM_INT);
        $stmt->bindParam(':token', $token);
        $stmt->execute();

    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

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
            if (!empty($director)) {
                $updates[] = "director = :director";
            }else{
                $updates[] = "director = :director";
            }
            if (!empty($genre)) {
                $updates[] = "genre = :genre";
            }else{
                $updates[] = "genre = :genre";
            }
            if (!empty($actors)) {
                $updates[] = "actors = :actors";
            }else{
                $updates[] = "actors = :actors";
            }
            if (!empty($plot)) {
                $updates[] = "plot = :plot";
            }else{
                $updates[] = "plot = :plot";
            }
            if (!empty($backdrop_path)) {
                $updates[] = "backdrop_path = :backdrop_path";
            }else{
                $updates[] = "backdrop_path = :backdrop_path";
            }
            if (!empty($releasedate)) {
                $updates[] = "releasedate = :releasedate";
            }else{
                $updates[] = "releasedate = :releasedate";
            }
            if (!empty($duration)) {
                $updates[] = "duration = :duration";
            }else{
                $updates[] = "duration = :duration";
            }
            if (!empty($rating)) {
                $updates[] = "rating = :rating";
            }else{
                $updates[] = "rating = :rating";
            }
            if (!empty($rating_5based)) {
                $updates[] = "rating_5based = :rating_5based";
            }else{
                $updates[] = "rating_5based = :rating_5based";
            }
            if (!empty($year)) {
                $updates[] = "year = :year";
            }else{
                $updates[] = "year = :year";
            }

            $sql_update .= implode(", ", $updates);
            $sql_update .= " WHERE id = :id";

            $stmt_update = $conexao->prepare($sql_update);

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

            if (!empty($director)) {
                $stmt_update->bindParam(':director', $director, PDO::PARAM_STR);
            }else{
                $stmt_update->bindValue(':director', null, PDO::PARAM_STR);
            }
            if (!empty($genre)) {
                $stmt_update->bindParam(':genre', $genre, PDO::PARAM_STR);
            }else{
                $stmt_update->bindValue(':genre', null, PDO::PARAM_STR);
            }
            if (!empty($actors)) {
                $stmt_update->bindParam(':actors', $actors, PDO::PARAM_STR);
            }else{
                $stmt_update->bindValue(':actors', null, PDO::PARAM_STR);
            }
            if (!empty($plot)) {
                $stmt_update->bindParam(':plot', $plot, PDO::PARAM_STR);
            }else{
                $stmt_update->bindValue(':plot', null, PDO::PARAM_STR);
            }
            if (!empty($backdrop_path)) {
                $stmt_update->bindParam(':backdrop_path', $backdrop_path, PDO::PARAM_STR);
            }else{
                $stmt_update->bindValue(':backdrop_path', null, PDO::PARAM_STR);
            }
            if (!empty($releasedate)) {
                $stmt_update->bindParam(':releasedate', $releasedate, PDO::PARAM_STR);
            }else{
                $stmt_update->bindValue(':releasedate', null, PDO::PARAM_STR);
            }
            if (!empty($duration)) {
                $stmt_update->bindParam(':duration', $duration, PDO::PARAM_STR);
            }else{
                $stmt_update->bindValue(':duration', null, PDO::PARAM_STR);
            }
            if (!empty($rating)) {
                $stmt_update->bindParam(':rating', $rating, PDO::PARAM_STR);
            }else{
                $stmt_update->bindValue(':rating', null, PDO::PARAM_STR);
            }
            if (!empty($rating_5based)) {
                $stmt_update->bindParam(':rating_5based', $rating_5based, PDO::PARAM_STR);
            }else{
                $stmt_update->bindValue(':rating_5based', null, PDO::PARAM_STR);
            }
            if (!empty($year)) {
                $stmt_update->bindParam(':year', $year, PDO::PARAM_STR);
            }else{
                $stmt_update->bindValue(':year', null, PDO::PARAM_STR);
            }

            $stmt_update->bindParam(':id', $id, PDO::PARAM_INT);

            if ($stmt_update->execute()) {
                $resposta['title'] = "Concluído!";
                $resposta['msg'] = "Filme editado com sucesso";
                $resposta['icon'] = "success";
            } else {
                $resposta['title'] = "Erro!";
                $resposta['msg'] = "Erro ao editar filme";
                $resposta['icon'] = "error";
            }

            return $resposta;
        } else {

            $resposta['title'] = "Erro!";
            $resposta['msg'] = "filme não encontrado";
            $resposta['icon'] = "error";
            return $resposta;
        }

}

function delete_filmes($id, $nome)
{

        $modal_body = "<input type=\"hidden\"  name=\"confirme_delete_filmes\" value='$id'></div>";
        $modal_body .= "<input type=\"hidden\"  name=\"nome\" value='$nome'></div>";
        $modal_body .= "Tem certeza de que deseja excluir o Filme ($nome) ?";

        $modal_footer = "<button type='button' class='btn btn-primary btn-sm' data-bs-dismiss='modal' aria-label='Close'>Cancelar</button><button type='button' class='btn btn-danger btn-sm' onclick='enviardados(\"modal_master_form\", \"filmes.php\")'>EXCLUIR</button>";

        $resposta = [
            'modal_header_class'=> "d-block modal-header bg-danger text-white m-2",
            'modal_master_body_style'=> "background-color: #ffffff00;",
            'modal_titulo'=> "EXCLUIR FILME",
            'modal_body'=> $modal_body,
            'modal_footer'=> $modal_footer
        ];

    return $resposta;
}

function confirme_delete_filmes($id, $name)
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

        $sql_delete = "DELETE FROM streams WHERE id = :id AND name = :name";

        $stmt_delete = $conexao->prepare($sql_delete);

        $stmt_delete->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt_delete->bindParam(':name', $name, PDO::PARAM_STR);

        if ($stmt_delete->execute()) {
            $resposta['title'] = "Sucesso!";
            $resposta['msg'] = "Filme deletado com sucesso!";
            $resposta['icon'] = "success";
        } else {
            $resposta['title'] = "Erro!";
            $resposta['msg'] = "Erro ao deletar filme.";
            $resposta['icon'] = "error";
        }

        return $resposta;
    } else {
        return 0;
    }
}

function adicionar_filmes()
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

        $categoria = $conexao->query("SELECT * FROM categoria WHERE admin_id = '$admin_id' and type = 'movie' ORDER BY nome ASC");
        $categoriasOptions = "";
        while ($categorias = $categoria->fetch()) {

            $categoriasOptions .= '<option value="'.$categorias['id'].'">'.$categorias['nome'].'</option>';

        }
        $modal_body = '';
        $modal_body .= '<input type="hidden" name="confirme_adicionar_filmes" value="">';
        $modal_body .= '<div class="form-row row">';
            $modal_body .= '<div class="align-content-center col col-12 col-lg-3 d-flex form-group">
                            <img class="logo-img img-thumbnail mx-auto rounded" src="http://plone.ufpb.br/labeet/contents/paginas/acervo-brazinst/copy_of_cordofones/udecra/sem-imagem.jpg/@@images/image.jpeg" alt="" style=" width: 100%; height: 290px; background-color: black; background-repeat: no-repeat; background-position: 50% 50%; background-size: 100%; margin: 5px 0px; float: left; box-shadow: #ff0000 0px 0px 4px 0px; border-radius: 5px;">
                            </div>';
            $modal_body .= '<div class="form-group col-md col">';
                $modal_body .= '<div class="input-group-text mb-1 m-1">
                                    <span class="input-group-text" style=" min-width: 200px">Nome do Filme:</span>
                                    <input type="text" class="form-control " id="nome" name="nome" value="" autocomplete="off">
                                </div>';
                $modal_body .= '<div class="input-group-text mb-1 m-1">
                                    <span class="input-group-text" style=" min-width: 200px">Dirigido Por:</span>
                                    <input type="text" class="form-control " name="director" value="" autocomplete="off" placeholder="ex: Riley Flanagan, Jaume Collet-Serra, Jaume Collet-Serra">
                                </div>';
                $modal_body .= '<div class="input-group-text mb-1 m-1">
                                    <span class="input-group-text" style=" min-width: 200px">Data de lançamento:</span>
                                    <input type="date" class="form-control " name="releasedate" value="" autocomplete="off" placeholder="ex: 05-12-24">
                                </div>';
                $modal_body .= '<div class="input-group-text mb-1 m-1">
                                    <span class="input-group-text" style=" min-width: 200px">Duraçao:</span>
                                    <input type="time" step="2" class="form-control " name="duration" value="" autocomplete="off" placeholder="ex: 01:59:40">
                                </div>';
                $modal_body .= '<div class="input-group-text mb-1 m-1">
                                    <span class="input-group-text" style="min-width: 200px;">Ano:</span>
                                    <select class="form-control" name="year">
                                        <option value="" disabled selected>Selecione o ano</option>';

                                        for ($i = '1' + date('Y'); $i >= 1900; $i--) {
                                            $modal_body .= "<option value=\"$i\">$i</option>";
                                        }
                $modal_body .= '</select></div>';

                $modal_body .= '<div class="input-group-text mb-1 m-1">
                                    <span class="input-group-text" style=" min-width: 200px">Gênero:</span>
                                    <input type="text" class="form-control " name="genre" value="" autocomplete="off" placeholder="ex: Mistério, Thriller, Ação">
                                </div>';
            $modal_body .= '</div>';
        $modal_body .= '</div>';

        $modal_body .= '<div class="form-row input-group-text m-1">
                        <span class="input-group-text" style=" min-width: 200px">Elenco:</span>
                        <input type="text" class="form-control " name="actors" value="" autocomplete="off" placeholder="ex: Taron Egerton, Jason Bateman, Sofia Carson, Danielle Deadwyler, Dean Norris">
                        </div>';
        $modal_body .= '<div class="form-row input-group-text m-1 row">
                        <span class="input-group-text" style=" min-width: 200px">Descriçao:</span>
                        <textarea type="text" class="form-control " name="plot" style="max-height: 150px;height: 100px;" placeholder="Digite ou cole a Descrição aqui"></textarea>
                        </div>';
        $modal_body .= '<div class="form-row input-group-text m-1">
                        <span class="input-group-text" style=" min-width: 200px">Avaliação:</span>
                        <input type="text" class="form-control " name="rating" value="" placeholder="6.9">
                        </div>';
        $modal_body .= '<div class="form-row input-group-text m-1">
                        <span class="input-group-text" style=" min-width: 200px">Classificação de 5:</span>
                        <input type="text" class="form-control " name="rating_5based" value="" placeholder="valor maximo 5">
                        </div>';
        $modal_body .= '<div class="form-row input-group-text m-1">
                        <span class="input-group-text" style=" min-width: 200px">Link da (capa do Filme):</span>
                        <input type="text" class="form-control" id="logo" name="logo" value="" autocomplete="off" placeholder="http://servidor.com/filme.png">
                        </div>';
        $modal_body .= '<div class="form-row input-group-text m-1">
                        <span class="input-group-text" style=" min-width: 200px">background:</span>
                        <input type="text" class="form-control" id="backdrop_path" name="backdrop_path" value="" autocomplete="off" placeholder="http://servidor.com/filme.png">
                        </div>';
        $modal_body .= '<div class="form-row row input-group-text m-1">';
            $modal_body .= '<div class="form-group col-md col text-center">
                            <label for="Categoria">Categoria</label>
                            <select class="form-control form-select"name="Categoria">
                        '.$categoriasOptions.'
                        </select>
                            </div>';
            $modal_body .= ' <div class="form-group col-md col text-center">
                            <label for="adulto">É um filme adulto?</label>
                            <select class="form-control form-select" name="adulto">
                            <option value="0"> NAO </option>
                            <option value="1"> SIM </option>';
            $modal_body .= '</select>
                            </div>';
        $modal_body .= '</div>';
        $modal_body .= '<div class="form-row input-group-text m-1">
                        <span class="input-group-text" style=" min-width: 200px">Link do Filme:</span>
                        <input type="text" class="form-control" name="link" value="" autocomplete="off" placeholder="http://servidor.com/live/1234/1234/234.ts">
                        </div>';

        $modal_body .= '<script>
    document.getElementById("logo").addEventListener("input", function() {
        var newLogoUrl = this.value;
        var capaFilme = document.querySelector("#modal_master img.logo-img");
        if (capaFilme) {
            capaFilme.src = newLogoUrl;
        }
    });

    document.getElementById("backdrop_path").addEventListener("input", function() {
        var newLogoBackground = this.value;
        var modalBody = document.getElementById("modal_master-body");
        if (modalBody) {
            var existingStyle = modalBody.style.cssText; 
            var logoBackgroundStyle = `
                background-image: url(\'${newLogoBackground}\');
                background-color: black;
                background-repeat: no-repeat;
                background-position: 50% 50%;
                background-size: cover;
            `;

            modalBody.style.cssText = existingStyle + logoBackgroundStyle;
        }
    });
</script>';

        $modal_footer = "<button type='button' class='btn btn-outline-danger btn-sm' data-bs-dismiss='modal' aria-label='Close'>Cancelar</button><button type='button' class='btn btn-primary btn-sm' onclick='enviardados(\"modal_master_form\", \"filmes.php\")'>Adicionar</button>";

        $resposta = [
            'modal_header_class'=> "d-block modal-header bg-primary text-white m-2",
            'modal_master_body_style'=> "max-height: 720px;",
            'modal_titulo'=> "Adiconar Filme",
            'modal_body'=> $modal_body,
            'modal_footer'=> $modal_footer
        ];

        return $resposta;
    } else {
        return 0;
    }
}

function confirme_adicionar_filmes($nome, $link, $logo, $Categoria, $is_adult, $director, $genre, $actors, $plot, $backdrop_path, $releasedate, $duration, $rating, $rating_5based, $year)
{
    $conexao = conectar_bd();
    $resposta = []; 

    $token = isset($_SESSION['token']) ? $_SESSION['token'] : "0";
    $admin_id = isset($_SESSION['admin_id']) ? $_SESSION['admin_id'] : null;

    if (!$nome) {
        $resposta['title'] = "Erro!";
        $resposta['msg'] = "É necessario preenceher o nome!";
        $resposta['icon'] = "error";
        return $resposta;
    }
    if (!$Categoria) {
        $resposta['title'] = "Erro!";
        $resposta['msg'] = "A categoria nao foi informada!";
        $resposta['icon'] = "error";
        return $resposta;
    }
    $is_adult = preg_replace("/[^0-9]/", "", $is_adult);
    $Categoria = preg_replace("/[^0-9]/", "", $Categoria);
    $rating = preg_replace("/[^0-9]/", "", $rating);
    if ($rating === "10") {
        $rating = "10";
    } elseif (strlen($rating) >= 2) {
        $rating = $rating[0] . '.' . $rating[1];
    } else {
        $rating = strlen($rating) === 1 ? $rating . '.0' : '0.0';
    }

    $sql = "SELECT *
            FROM admin
            WHERE id = :admin_id AND token = :token";
        $stmt = $conexao->prepare($sql);
        $stmt->bindParam(':admin_id', $admin_id, PDO::PARAM_INT);
        $stmt->bindParam(':token', $token);
        $stmt->execute();

    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

        extract($row);

        $sql_check = "SELECT * FROM streams WHERE link = :link";
        $stmt_check = $conexao->prepare($sql_check);
        $stmt_check->bindParam(':link', $link, PDO::PARAM_STR);
        $stmt_check->execute();

        if ($stmt_check->fetch(PDO::FETCH_ASSOC)) {
            $resposta['title'] = "Erro!";
            $resposta['msg'] = "Filme já Exite!";
            $resposta['icon'] = "error";
            return $resposta;
        }

        $sql_insert = "INSERT INTO streams (name, link, stream_icon, stream_type, category_id, is_adult, director, genre, actors, plot, backdrop_path, releasedate, duration, rating, rating_5based, year) 
                        VALUES (:name, :link, :stream_icon, :stream_type, :category_id, :is_adult, :director, :genre, :actors, :plot, :backdrop_path, :releasedate, :duration, :rating, :rating_5based, :year)";
        $stmt_insert = $conexao->prepare($sql_insert);

        $stmt_insert->bindParam(':name', $nome, PDO::PARAM_STR);
        $stmt_insert->bindParam(':link', $link, PDO::PARAM_STR);
        $stmt_insert->bindParam(':stream_icon', $logo, PDO::PARAM_STR);
        $stmt_insert->bindValue(':stream_type', 'movie', PDO::PARAM_STR);
        $stmt_insert->bindValue(':category_id',  $Categoria, PDO::PARAM_STR);
        $stmt_insert->bindParam(':is_adult', $is_adult, PDO::PARAM_STR);

        $stmt_insert->bindParam(':director', $director, PDO::PARAM_STR);
        $stmt_insert->bindParam(':genre', $genre, PDO::PARAM_STR);
        $stmt_insert->bindParam(':actors', $actors, PDO::PARAM_STR);
        $stmt_insert->bindParam(':plot', $plot, PDO::PARAM_STR);
        $stmt_insert->bindParam(':backdrop_path', $backdrop_path, PDO::PARAM_STR);
        $stmt_insert->bindParam(':releasedate', $releasedate, PDO::PARAM_STR);
        $stmt_insert->bindParam(':duration', $duration, PDO::PARAM_STR);
        $stmt_insert->bindParam(':rating', $rating, PDO::PARAM_STR);
        $stmt_insert->bindParam(':rating_5based', $rating_5based, PDO::PARAM_STR);
        $stmt_insert->bindParam(':year', $year, PDO::PARAM_STR);

        if ($admin == 1) {

            if ($stmt_insert->execute()) {
                $lastInsertedId = $conexao->lastInsertId();

                $resposta['title'] = "Concluído!";
                $resposta['msg'] = "Filme inserido com sucesso";
                $resposta['icon'] = "success";
                return $resposta;
            } else {
                $resposta['title'] = "Erro!";
                $resposta['msg'] = "Erro ao adiconar filme";
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