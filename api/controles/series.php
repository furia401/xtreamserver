<?php
function editar_series($id)
{
    $conexao = conectar_bd();

    $token = isset($_SESSION['token']) ? $_SESSION['token'] : "0";
    $admin_id = isset($_SESSION['admin_id']) ? $_SESSION['admin_id'] : null;

    $sql = "SELECT s.*
            FROM series s 
            LEFT JOIN admin a ON a.id = :admin_id 
            WHERE s.id = :id AND a.token = :token";
        $stmt = $conexao->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':admin_id', $admin_id, PDO::PARAM_INT);
        $stmt->bindParam(':token', $token, PDO::PARAM_STR);
        $stmt->execute();

    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

        extract($row);

        $categoria = $conexao->query("SELECT * FROM categoria WHERE admin_id = '$admin_id' and type = 'series' ORDER BY nome ASC");
        $categoriaOptions = "";
        $categoriaOptions2 = "";
        while ($lista_categorias = $categoria->fetch()) {            

            if ($lista_categorias['id'] == $category_id) {
                $categoriaOptions .= '<option value="'.$lista_categorias['id'].'"> Categoria Atual => '.$lista_categorias['nome'].' </option>';
            }else{
                $categoriaOptions2 .= '<option value="'.$lista_categorias['id'].'">'.$lista_categorias['nome'].'</option>';
            }
        }
        $temporadas = $conexao->query("SELECT * FROM series_seasons WHERE series_id = '$id' ORDER BY season_number ASC");
        $temporadasOptions = "";
        while ($lista_temporadas = $temporadas->fetch()) {
            $bg = $lista_temporadas['cover'];
            if (!$lista_temporadas['cover']) {
                $bg = 'https://www.themoviedb.org/assets/2/v4/glyphicons/basic/glyphicons-basic-38-picture-grey-c2ebdbb057f2a7614185931650f8cee23fa137b93812ccb132b9df511df1cfac.svg';
            }

            $temporadasOptions .= '<div id="temporada-'.$lista_temporadas['season_number'].'" class="card m-2" style="width: 150px; min-width: 150px;">
                                    <div class="d-flex justify-content-end p-1" style="z-index: 1;">
                                        <button class="btn btn-sm text-danger" title="Excluir" onclick=\'modal_master("api/series.php", "series_id", "'.$lista_temporadas['series_id'].'", "detetar_temporada", "'.$lista_temporadas['season_number'].'")\'>
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                    <div class="p-1" style="position: relative; top: -41px;" onclick=\'modal_master("api/series.php", "ver_episodios", "'.$lista_temporadas['series_id'].'", "temporada", "'.$lista_temporadas['season_number'].'", "ep", "0")\'>
                                        <img src="'.$bg.'" 
                                                 class="card-img-top" 
                                                 alt="Capa Temporada 1" style=" height: 210px; min-height: 210px; background: url(https://www.themoviedb.org/assets/2/v4/glyphicons/basic/glyphicons-basic-38-picture-grey-c2ebdbb057f2a7614185931650f8cee23fa137b93812ccb132b9df511df1cfac.svg); background-size: contain; background-color: #dbdbdb; background-repeat: no-repeat; background-position: 50%;">
                                            <div class="card-body p-2 text-center">
                                                <h6 class="card-title">Temporada '.$lista_temporadas['season_number'].'</h6>
                                                <h6 class="card-title">Quant. EP: '.$lista_temporadas['episode_count'].'</h6>
                                            </div>
                                    </div>
                                </div>';
        }

        $modal_body = '';
        $modal_body .= '<input type="hidden" name="confirme_editar_series" value="'.$id.'">';
        $modal_body .= '<div class="form-row row">';
            $modal_body .= '<div class="align-content-center col col-12 col-lg-3 d-flex form-group">
                            <img class="logo-img img-thumbnail mx-auto rounded" src="'.$cover.'" alt="'.$name.'" style=" width: 100%; height: 290px; background-color: black; background-repeat: no-repeat; background-position: 50% 50%; background-size: 100%; margin: 5px 0px; float: left; box-shadow: #ff0000 0px 0px 4px 0px; border-radius: 5px;">
                            </div>';
            $modal_body .= '<div class="form-group col-md col">';
                $modal_body .= '<div class="input-group-text mb-1 m-1">
                                    <span class="input-group-text" style=" min-width: 200px">Nome do Serie:</span>
                                    <input type="text" class="form-control " id="nome" name="nome" value="'.$name.'" autocomplete="off">
                                </div>';
                $modal_body .= '<div class="input-group-text mb-1 m-1">
                                    <span class="input-group-text" style=" min-width: 200px">Dirigido Por:</span>
                                    <input type="text" class="form-control " name="director" value="'.$director.'" autocomplete="off" placeholder="ex: Riley Flanagan, Jaume Collet-Serra, Jaume Collet-Serra">
                                </div>';
                $modal_body .= '<div class="input-group-text mb-1 m-1">
                                    <span class="input-group-text" style=" min-width: 200px">Data de lançamento:</span>
                                    <input type="date" class="form-control " name="releasedate" value="'.$release_date.'" autocomplete="off" placeholder="ex: 05-12-24">
                                </div>';
                $modal_body .= '<div class="input-group-text mb-1 m-1">
                                    <span class="input-group-text" style=" min-width: 200px">Duraçao:</span>
                                    <input type="time" step="2" class="form-control" name="duration" value="'.$episode_run_time.'" autocomplete="off" placeholder="ex: 120">
                                </div>';
                $modal_body .= '<div class="input-group-text mb-1 m-1">
                                    <span class="input-group-text" style="min-width: 200px;">Ano:</span>
                                    <select class="form-control" name="year">
                                        <option value="" disabled selected>Selecione o ano</option>';

                                        for ($i = '1' + date('Y'); $i >= 1900; $i--) {
                                            if ($year == $i) {
                                                $modal_body .= "<option value=\"$i\" selected>$i</option>";
                                            }
                                            $modal_body .= "<option value=\"$i\">$i</option>";
                                        }
                $modal_body .= '</select></div>';
                $modal_body .= '<div class="input-group-text mb-1 m-1">
                                    <span class="input-group-text" style=" min-width: 200px">Gênero:</span>
                                    <input type="text" class="form-control " name="genre" value="'.$genre.'" autocomplete="off" placeholder="ex: Mistério, Thriller, Ação">
                                </div>';
            $modal_body .= '</div>';
        $modal_body .= '</div>';

        $modal_body .= '<div class="form-row input-group-text m-1">
                        <span class="input-group-text" style=" min-width: 200px">Elenco:</span>
                        <input type="text" class="form-control " name="actors" value="'.$cast.'" autocomplete="off" placeholder="ex: Taron Egerton, Jason Bateman, Sofia Carson, Danielle Deadwyler, Dean Norris">
                        </div>';
        $modal_body .= '<div class="form-row input-group-text m-1 row">
                        <span class="input-group-text" style=" min-width: 200px">Descriçao:</span>
                        <textarea type="text" class="form-control " name="plot" style="max-height: 150px;height: 100px;" placeholder="Digite ou cole a Descrição aqui">'.$plot.'</textarea>
                        </div>';
        $modal_body .= '<div class="form-row input-group-text m-1">
                        <span class="input-group-text" style=" min-width: 200px">Avaliação:</span>
                        <input type="text" class="form-control " name="rating" value="'.$rating.'" placeholder="6.9">
                        </div>';
        $modal_body .= '<div class="form-row input-group-text m-1">
                        <span class="input-group-text" style=" min-width: 200px">Classificação de 5:</span>
                        <input type="text" class="form-control " name="rating_5based" value="'.$rating_5based.'" placeholder="valor maximo 5">
                        </div>';
        $modal_body .= '<div class="form-row input-group-text m-1">
                        <span class="input-group-text" style=" min-width: 200px">Link da (capa da Serie):</span>
                        <input type="text" class="form-control" id="logo" name="logo" value="'.$cover.'" autocomplete="off" placeholder="http://servidor.com/filme.png">
                        </div>';
        $modal_body .= '<div class="form-row input-group-text m-1">
                        <span class="input-group-text" style=" min-width: 200px">background:</span>
                        <input type="text" class="form-control" id="backdrop_path" name="backdrop_path" value="'.$backdrop_path.'" autocomplete="off" placeholder="http://servidor.com/series.png">
                        </div>';
        $modal_body .= '<div class="form-row row input-group-text m-1">';
            $modal_body .= '<div class="form-group col-md col text-center">
                            <label for="Categoria">Categoria</label>
                            <select class="form-control form-select"name="Categoria">
                        '.$categoriaOptions.' '.$categoriaOptions2.'
                        </select>
                            </div>';
            $modal_body .= ' <div class="form-group col-md col text-center">
                            <label for="adulto">É uma serie adulto?</label>
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
        $modal_body .= '
                        <div class="form-row row input-group-text m-1">
                            <!-- Título -->
                            <div class="mt-4">
                                <h3 class="d-flex justify-content-around mb-4 text-uppercase">
                                    Lista de Temporadas
                                    <button type="button" class="btn btn-outline-success fas fa-plus" onclick="modal_master(\'api/series.php\',\'confirme_adicionar_temporadas\',\''.$id.'\')"></button>
                                </h3>
                            </div>
                            <!-- Lista de Temporadas -->
                            <div class="d-flex overflow-auto" style="height: 300px;">
                                '.$temporadasOptions.'
                            </div>
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

        $modal_footer = "<button type='button' onclick='enviardados(\"modal_master_form\", \"series.php\")' class='btn btn-info waves-effect waves-light' >Salvar</button><button type='button' class='btn btn-danger' data-bs-dismiss='modal' aria-label='Close'>Cancelar</button>";

        $modal_master_body_style = "max-height: 720px;";
        if ($backdrop_path) {
            $modal_master_body_style = "max-height: 720px; background-image: url(".$backdrop_path."); background-color: black; background-repeat: no-repeat; background-position: 50% 50%; background-size: cover;";
        }

        $resposta = [
            'modal_header_class'=> "d-block modal-header bg-info text-white m-2",
            'modal_master_body_style'=> $modal_master_body_style,
            'modal_titulo'=> "Editar Serie ( $name )",
            'modal_body'=> $modal_body,
            'modal_footer'=> $modal_footer
        ];

    return $resposta;
    } else {
        return 0;
    }
}

function confirme_editar_series($id, $name, $link, $logo, $category_id, $is_adult, $director, $genre, $actors, $plot, $backdrop_path, $releasedate, $duration, $rating, $rating_5based, $year)
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

            $sql_update = "UPDATE series SET ";
            $updates = [];

            if (!empty($is_adult)) {
                $updates[] = "is_adult = :is_adult";
            }else{
                $updates[] = "is_adult = 0";
            }
            if (!empty($name)) {
                $updates[] = "name = :name";
            }
            if (!empty($logo)) {
                $updates[] = "cover = :cover";
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
                $updates[] = "cast = :cast";
            }else{
                $updates[] = "cast = :cast";
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
                $updates[] = "release_date = :releasedate";
            }else{
                $updates[] = "release_date = :releasedate";
            }
            if (!empty($duration)) {
                $updates[] = "episode_run_time = :duration";
            }else{
                $updates[] = "episode_run_time = :duration";
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
                $stmt_update->bindParam(':cover', $logo, PDO::PARAM_STR);
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
                $stmt_update->bindParam(':cast', $actors, PDO::PARAM_STR);
            }else{
                $stmt_update->bindValue(':cast', null, PDO::PARAM_STR);
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
                $resposta['msg'] = "Serie editado com sucesso";
                $resposta['icon'] = "success";
            } else {
                $resposta['title'] = "Erro!";
                $resposta['msg'] = "Erro ao editar serie";
                $resposta['icon'] = "error";
            }

            return $resposta;
        } else {

            $resposta['title'] = "Erro!";
            $resposta['msg'] = "serie não encontrado";
            $resposta['icon'] = "error";
            return $resposta;
        }

}

function delete_series($id, $nome)
{

        $modal_body = "<input type=\"hidden\"  name=\"confirme_delete_series\" value='$id'></div>";
        $modal_body .= "<input type=\"hidden\"  name=\"nome\" value='$nome'></div>";
        $modal_body .= "Tem certeza de que deseja excluir a Serie ($nome) ?";

        $modal_footer = "<button type='button' class='btn btn-primary btn-sm' data-bs-dismiss='modal' aria-label='Close'>Cancelar</button><button type='button' class='btn btn-danger btn-sm' onclick='enviardados(\"modal_master_form\", \"series.php\")'>EXCLUIR</button>";

        $resposta = [
            'modal_header_class'=> "d-block modal-header bg-danger text-white m-2",
            'modal_master_body_style'=> "background-color: #ffffff00;",
            'modal_titulo'=> "EXCLUIR SERIE",
            'modal_body'=> $modal_body,
            'modal_footer'=> $modal_footer
        ];

    return $resposta;
}

function confirme_delete_series($id, $name)
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

        $sql_delete = "DELETE FROM series WHERE id = :id AND name = :name";

        $stmt_delete = $conexao->prepare($sql_delete);

        $stmt_delete->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt_delete->bindParam(':name', $name, PDO::PARAM_STR);

        if ($stmt_delete->execute()) {

            $sql_delete_episodes = "DELETE FROM series_episodes WHERE series_id = :id";
            $stmt_episodes = $conexao->prepare($sql_delete_episodes);
            $stmt_episodes->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt_episodes->execute();

            $sql_delete_seasons = "DELETE FROM series_seasons WHERE series_id = :id";
            $stmt_seasons = $conexao->prepare($sql_delete_seasons);
            $stmt_seasons->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt_seasons->execute();

            $resposta['title'] = "Sucesso!";
            $resposta['msg'] = "Serie deletada com sucesso!";
            $resposta['icon'] = "success";
        } else {
            $resposta['title'] = "Erro!";
            $resposta['msg'] = "Erro ao deletar serie.";
            $resposta['icon'] = "error";
        }

        return $resposta;
    } else {
        return 0;
    }
}

function adicionar_series()
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

        $categoria = $conexao->query("SELECT * FROM categoria WHERE admin_id = '$admin_id' and type = 'series' ORDER BY nome ASC");
        $categoriasOptions = "";
        while ($categorias = $categoria->fetch()) {

            $categoriasOptions .= '<option value="'.$categorias['id'].'">'.$categorias['nome'].'</option>';

        }
        $modal_body = '';
        $modal_body .= '<input type="hidden" name="confirme_adicionar_series" value="">';
        $modal_body .= '<div class="form-row row">';
            $modal_body .= '<div class="align-content-center col col-12 col-lg-3 d-flex form-group">
                            <img class="logo-img img-thumbnail mx-auto rounded" src="http://plone.ufpb.br/labeet/contents/paginas/acervo-brazinst/copy_of_cordofones/udecra/sem-imagem.jpg/@@images/image.jpeg" alt="" style=" width: 100%; height: 290px; background-color: black; background-repeat: no-repeat; background-position: 50% 50%; background-size: 100%; margin: 5px 0px; float: left; box-shadow: #ff0000 0px 0px 4px 0px; border-radius: 5px;">
                            </div>';
            $modal_body .= '<div class="form-group col-md col">';
                $modal_body .= '<div class="input-group-text mb-1 m-1">
                                    <span class="input-group-text" style=" min-width: 200px">Nome da Serie:</span>
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
                        <span class="input-group-text" style=" min-width: 200px">Link da (capa da serie):</span>
                        <input type="text" class="form-control" id="logo" name="logo" value="" autocomplete="off" placeholder="http://servidor.com/serie.png">
                        </div>';
        $modal_body .= '<div class="form-row input-group-text m-1">
                        <span class="input-group-text" style=" min-width: 200px">background:</span>
                        <input type="text" class="form-control" id="backdrop_path" name="backdrop_path" value="" autocomplete="off" placeholder="http://servidor.com/serie.png">
                        </div>';
        $modal_body .= '<div class="form-row row input-group-text m-1">';
            $modal_body .= '<div class="form-group col-md col text-center">
                            <label for="Categoria">Categoria</label>
                            <select class="form-control form-select"name="Categoria">
                        '.$categoriasOptions.'
                        </select>
                            </div>';
            $modal_body .= ' <div class="form-group col-md col text-center">
                            <label for="adulto">É uma Serie adulto?</label>
                            <select class="form-control form-select" name="adulto">
                            <option value="0"> NAO </option>
                            <option value="1"> SIM </option>';
            $modal_body .= '</select>
                            </div>';
        $modal_body .= '</div>';

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

        $modal_footer = "<button type='button' class='btn btn-outline-danger btn-sm' data-bs-dismiss='modal' aria-label='Close'>Cancelar</button><button type='button' class='btn btn-primary btn-sm' onclick='enviardados(\"modal_master_form\", \"series.php\")'>Adicionar</button>";

        $resposta = [
            'modal_header_class'=> "d-block modal-header bg-primary text-white m-2",
            'modal_master_body_style'=> "max-height: 720px;",
            'modal_titulo'=> "Adiconar Serie",
            'modal_body'=> $modal_body,
            'modal_footer'=> $modal_footer
        ];

        return $resposta;
    } else {
        return 0;
    }
}

function confirme_adicionar_series($nome, $link, $logo, $Categoria, $is_adult, $director, $genre, $actors, $plot, $backdrop_path, $releasedate, $duration, $rating, $rating_5based, $year)
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

        $sql_check = "SELECT * FROM series WHERE name = :nome";
        $stmt_check = $conexao->prepare($sql_check);
        $stmt_check->bindParam(':nome', $nome, PDO::PARAM_STR);
        $stmt_check->execute();

        if ($stmt_check->fetch(PDO::FETCH_ASSOC)) {
            $resposta['title'] = "Erro!";
            $resposta['msg'] = "Serie já Exite!";
            $resposta['icon'] = "error";
            return $resposta;
        }

        $sql_insert = "INSERT INTO series (name, cover, stream_type, category_id, is_adult, director, genre, cast, plot, backdrop_path, release_date, episode_run_time, rating, rating_5based, year) 
                        VALUES (:name, :cover, :stream_type, :category_id, :is_adult, :director, :genre, :cast, :plot, :backdrop_path, :release_date, :episode_run_time, :rating, :rating_5based, :year)";
        $stmt_insert = $conexao->prepare($sql_insert);

        $stmt_insert->bindParam(':name', $nome, PDO::PARAM_STR);
        $stmt_insert->bindParam(':cover', $logo, PDO::PARAM_STR);
        $stmt_insert->bindValue(':stream_type', 'series', PDO::PARAM_STR);
        $stmt_insert->bindValue(':category_id',  $Categoria, PDO::PARAM_STR);
        $stmt_insert->bindParam(':is_adult', $is_adult, PDO::PARAM_STR);

        $stmt_insert->bindParam(':director', $director, PDO::PARAM_STR);
        $stmt_insert->bindParam(':genre', $genre, PDO::PARAM_STR);
        $stmt_insert->bindParam(':cast', $actors, PDO::PARAM_STR);
        $stmt_insert->bindParam(':plot', $plot, PDO::PARAM_STR);
        $stmt_insert->bindParam(':backdrop_path', $backdrop_path, PDO::PARAM_STR);
        $stmt_insert->bindParam(':release_date', $releasedate, PDO::PARAM_STR);
        $stmt_insert->bindParam(':episode_run_time', $duration, PDO::PARAM_STR);
        $stmt_insert->bindParam(':rating', $rating, PDO::PARAM_STR);
        $stmt_insert->bindParam(':rating_5based', $rating_5based, PDO::PARAM_STR);
        $stmt_insert->bindParam(':year', $year, PDO::PARAM_STR);

        if ($admin == 1) {

            if ($stmt_insert->execute()) {
                $lastInsertedId = $conexao->lastInsertId();

                $resposta['title'] = "Concluído!";
                $resposta['msg'] = "Serie inserida com sucesso";
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

function confirme_adicionar_temporadas($series_id) {
    $conexao = conectar_bd();
    $resposta = []; 

    $token = isset($_SESSION['token']) ? $_SESSION['token'] : "0";
    $admin_id = isset($_SESSION['admin_id']) ? $_SESSION['admin_id'] : null;

    if (!$series_id) {
        $resposta['title'] = "Erro!";
        $resposta['msg'] = "Não foi possível identificar a série.";
        $resposta['icon'] = "error";
        return $resposta;
    }

    $series_id = preg_replace("/[^0-9]/", "", $series_id);

    $sql = "SELECT * FROM admin WHERE id = :admin_id AND token = :token";
    $stmt = $conexao->prepare($sql);
    $stmt->bindParam(':admin_id', $admin_id, PDO::PARAM_INT);
    $stmt->bindParam(':token', $token, PDO::PARAM_STR);
    $stmt->execute();

    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        if ($row['admin'] == 1) {

            $sql_season = "SELECT MAX(season_number) AS max_season FROM series_seasons WHERE series_id = :series_id";
            $stmt_season = $conexao->prepare($sql_season);
            $stmt_season->bindParam(':series_id', $series_id, PDO::PARAM_INT);
            $stmt_season->execute();

            $max_season = $stmt_season->fetch(PDO::FETCH_ASSOC)['max_season'];
            $new_season_number = $max_season ? $max_season + 1 : 1;

            $sql_insert = "INSERT INTO series_seasons (series_id, season_number, episode_count, name) VALUES (:series_id, :season_number, :episode_count, :name)";
            $stmt_insert = $conexao->prepare($sql_insert);

            $episode_count = 0;
            $name = "temporada ".$new_season_number;

            $stmt_insert->bindParam(':series_id', $series_id, PDO::PARAM_INT);
            $stmt_insert->bindParam(':season_number', $new_season_number, PDO::PARAM_INT);
            $stmt_insert->bindParam(':episode_count', $episode_count, PDO::PARAM_INT);
            $stmt_insert->bindParam(':name', $name, PDO::PARAM_STR);

            if ($stmt_insert->execute()) {
                return editar_series($series_id);
                $resposta['title'] = "Concluído!";
                $resposta['msg'] = "Temporada adicionada com sucesso!";
                $resposta['icon'] = "success";
            } else {
                $resposta['title'] = "Erro!";
                $resposta['msg'] = "Erro ao adicionar nova temporada.";
                $resposta['icon'] = "error";
            }
        } else {
            $resposta['title'] = "Erro!";
            $resposta['msg'] = "Você não tem permissão para realizar esta ação.";
            $resposta['icon'] = "error";
        }
    } else {
        $resposta['title'] = "Erro!";
        $resposta['msg'] = "Admin não encontrado ou token inválido.";
        $resposta['icon'] = "error";
    }

    return $resposta;
}

function detetar_temporada($series_id, $Temporada)
{
    $conexao = conectar_bd();
    $token = isset($_SESSION['token']) ? $_SESSION['token'] : "0";
    $admin_id = isset($_SESSION['admin_id']) ? $_SESSION['admin_id'] : null;

    $series_id = preg_replace("/[^0-9]/", "", $series_id);
    $Temporada = preg_replace("/[^0-9]/", "", $Temporada);

    $sql = "SELECT *
            FROM admin
            WHERE id = :admin_id AND token = :token";
        $stmt = $conexao->prepare($sql);
        $stmt->bindParam(':admin_id', $admin_id, PDO::PARAM_INT);
        $stmt->bindParam(':token', $token);
        $stmt->execute();

    $resposta = []; 

    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

        $sql_delete = "DELETE FROM series_seasons WHERE series_id = :series_id AND season_number = :season_number";

        $stmt_delete = $conexao->prepare($sql_delete);

        $stmt_delete->bindParam(':series_id', $series_id, PDO::PARAM_INT);
        $stmt_delete->bindParam(':season_number', $Temporada, PDO::PARAM_STR);

        if ($stmt_delete->execute()) {

            $sql_delete_episodes = "DELETE FROM series_episodes WHERE series_id = :series_id and season = :season";
            $stmt_episodes = $conexao->prepare($sql_delete_episodes);
            $stmt_episodes->bindParam(':series_id', $series_id, PDO::PARAM_INT);
            $stmt_episodes->bindParam(':season', $Temporada, PDO::PARAM_INT);
            if ($stmt_episodes->execute()) {

                $episodios_deletados = $stmt_episodes->rowCount();

                $resposta = editar_series($series_id);
                $resposta['title'] = "Sucesso!";
                $resposta['msg'] = "Temporada deletada com sucesso! Episódios removidos: $episodios_deletados";
                $resposta['icon'] = "success";
                return $resposta;
            }

            $resposta = editar_series($series_id);
            $resposta['title'] = "Aviso!";
            $resposta['msg'] = "Temporada deletada mas nenhum episódio foi removido.";
            $resposta['icon'] = "warning";  
            return $resposta;
        } else {
            $resposta['title'] = "Erro!";
            $resposta['msg'] = "Erro ao deletar temporada.";
            $resposta['icon'] = "error";
        }

        return $resposta;
    } else {
        return 0;
    }
}

function ver_episodios($series_id, $temporada, $episodio)
{
    $conexao = conectar_bd();

    $token = isset($_SESSION['token']) ? $_SESSION['token'] : "0";
    $admin_id = isset($_SESSION['admin_id']) ? $_SESSION['admin_id'] : null;

    $sql = "SELECT s.*
            FROM series_seasons s 
            LEFT JOIN admin a ON a.id = :admin_id 
            WHERE s.series_id = :series_id AND s.season_number = :season_number AND a.token = :token";
        $stmt = $conexao->prepare($sql);
        $stmt->bindParam(':series_id', $series_id, PDO::PARAM_INT);
        $stmt->bindParam(':season_number', $temporada, PDO::PARAM_INT);
        $stmt->bindParam(':admin_id', $admin_id, PDO::PARAM_INT);
        $stmt->bindParam(':token', $token, PDO::PARAM_STR);
        $stmt->execute();

    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

        extract($row);

        $capa = "";
        $nomeep = "";
        $link = "";
        $id_episodio = 0;
        $ep_encontrado = false;

        $episode = $conexao->query("SELECT * FROM series_episodes WHERE series_id = '$series_id' AND season ='$temporada'  ORDER BY episode_num ASC");
        $episodeOptions = "";
        while ($lista_episode = $episode->fetch()) {
            $bg = $lista_episode['movie_image'];
            $active = "";
            if (!$lista_episode['movie_image']) {
                $bg = 'https://www.themoviedb.org/assets/2/v4/glyphicons/basic/glyphicons-basic-38-picture-grey-c2ebdbb057f2a7614185931650f8cee23fa137b93812ccb132b9df511df1cfac.svg';
            }
            if ($episodio == $lista_episode['episode_num']) {
                $capa = $lista_episode['movie_image'];
                $nomeep = $lista_episode['title'];
                $link = $lista_episode['link'];
                $active = "bg-secondary";
                $id_episodio = $lista_episode['id'];
                $duration = $lista_episode['duration'];
                $ep_encontrado = true;
            }

            $episodeOptions .= '<div id="episode-'.$lista_episode['episode_num'].'" class="'.$active.' card m-2" style="width: 150px; min-width: 150px;">
                                    <div class="d-flex justify-content-end p-1" style="z-index: 1;">
                                        <button class="btn btn-sm text-danger" title="Excluir" onclick=\'modal_master("api/series.php", "detetar_episodio", "'.$lista_episode['episode_num'].'", "series_id", "'.$lista_episode['series_id'].'", "temporada", "'.$lista_episode['season'].'")\'>
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                    <div class="p-1" style="position: relative; top: -41px;" onclick=\'modal_master("api/series.php", "ver_episodios", "'.$lista_episode['series_id'].'", "temporada", "'.$lista_episode['season'].'", "ep", "'.$lista_episode['episode_num'].'")\'>
                                        <img src="'.$bg.'" 
                                                 class="card-img-top" 
                                                 alt="Capa Temporada 1" style=" height: 210px; min-height: 210px; background: url(https://www.themoviedb.org/assets/2/v4/glyphicons/basic/glyphicons-basic-38-picture-grey-c2ebdbb057f2a7614185931650f8cee23fa137b93812ccb132b9df511df1cfac.svg); background-size: contain; background-color: #dbdbdb; background-repeat: no-repeat; background-position: 50%;">
                                            <div class="card-body p-2 text-center">
                                                <h6 class="card-title">Episodio Nº '.$lista_episode['episode_num'].'</h6>
                                            </div>
                                    </div>
                                </div>';

        }
        $modal_body = '';        
        $modal_body .= '<input type="hidden" name="confirme_editar_episodio" value="'.$id_episodio.'">';
        $modal_body .= '<input type="hidden" name="temporada" value="'.$temporada.'">';
        $modal_body .= '<input type="hidden" name="series_id" value="'.$series_id.'">';

        $modal_body .= '<div class="form-row row input-group-text m-1">
                            <!-- Título -->
                            <div class="mt-4">
                                <h3 class="d-flex justify-content-around mb-4 text-uppercase">
                                    Lista de Episodios
                                    <button type="button" class="btn btn-outline-success fas fa-plus" onclick="modal_master(\'api/series.php\',\'confirme_adicionar_episodios\',\''.$series_id.'\', \'temporada\', \''.$temporada.'\')"></button>
                                </h3>
                            </div>
                            <!-- Lista de Episodios -->
                            <div class="d-flex overflow-auto" style="height: 300px;">
                                '.$episodeOptions.'
                            </div>
                        </div>';
        if ($ep_encontrado) {
        $modal_body .= '<div class="form-row row input-group-text m-1">';
            $modal_body .= '<div class="align-content-center col col-12 col-lg-3 d-flex form-group">
                            <img class="logo-img img-thumbnail mx-auto rounded" src="'.$capa.'" alt="" style=" width: 100%; height: 290px; background-color: black; background-repeat: no-repeat; background-position: 50% 50%; background-size: 100%; margin: 5px 0px; float: left; box-shadow: #ff0000 0px 0px 4px 0px; border-radius: 5px;">
                            </div>';
            $modal_body .= '<div class="form-group col-md col">';
                $modal_body .= '<div class="input-group-text mb-1 m-1">
                                    <span class="input-group-text" style=" min-width: 200px">Nome do Episodio:</span>
                                    <input type="text" class="form-control " id="nome" name="nome" value="'.$nomeep.'" autocomplete="off">
                                </div>';
                $modal_body .= '<div class="input-group-text mb-1 m-1">
                                    <span class="input-group-text" style=" min-width: 200px">Link</span>
                                    <input type="text" class="form-control " name="link" value="'.$link.'" autocomplete="off" placeholder="http://servidor.com/series/12345/12345/123.mp4" style=" filter: blur(0px);">
                                </div>';
                $modal_body .= '<div class="input-group-text mb-1 m-1">
                                    <span class="input-group-text" style=" min-width: 200px">Link da capa</span>
                                    <input type="text" class="form-control " name="capa" value="'.$capa.'" autocomplete="off" placeholder="http://servidor.com/img/series.png">
                                </div>';
                $modal_body .= '<div class="input-group-text mb-1 m-1">
                                    <span class="input-group-text" style=" min-width: 200px">Numero do ep.</span>
                                    <input type="text" class="form-control " name="episodio" value="'.$episodio.'" autocomplete="off" placeholder="Exemplo: 01">
                                </div>';
                $modal_body .= '<div class="input-group-text mb-1 m-1">
                                    <span class="input-group-text" style=" min-width: 200px">Duraçao:</span>
                                    <input type="time" step="2" class="form-control" name="duration" value="'.$duration.'" autocomplete="off" placeholder="ex: 120">
                                </div>';
            $modal_body .= '</div>';
        $modal_body .= '</div>';
        }

        $modal_footer = "<button type='button' onclick='modal_master( \"api/series.php\", \"reenviar\")' class='btn btn-info waves-effect waves-light' >Editar Episodio</button><button type='button' class='btn btn-danger' data-bs-dismiss='modal' aria-label='Close'>Cancelar</button>";

        $modal_master_body_style = "max-height: 720px;";

        $resposta = [
            'modal_header_class'=> "d-block modal-header bg-info text-white m-2",
            'modal_master_body_style'=> "$modal_master_body_style",
            'modal_titulo'=> "Episodios da ( $name )",
            'modal_body'=> $modal_body,
            'modal_footer'=> $modal_footer
        ];

    return $resposta;
    } else {
        return 0;
    }
}
function confirme_editar_episodio($id, $series_id, $temporada, $title, $link, $capa, $episodio, $duration)
{
    $conexao = conectar_bd();

    $token = isset($_SESSION['token']) ? $_SESSION['token'] : "0";
    $admin_id = isset($_SESSION['admin_id']) ? $_SESSION['admin_id'] : null;

    $id = preg_replace("/[^0-9]/", "", $id);
    $episodio = preg_replace("/[^0-9]/", "", $episodio);

    $sql = "SELECT *
            FROM admin
            WHERE id = :admin_id AND token = :token";
        $stmt = $conexao->prepare($sql);
        $stmt->bindParam(':admin_id', $admin_id, PDO::PARAM_INT);
        $stmt->bindParam(':token', $token);
        $stmt->execute();

        $resposta = [];
    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

            $sql_update = "UPDATE series_episodes SET ";
            $updates = [];

            if (!empty($title)) {
                $updates[] = "title = :title";
            }
            if (!empty($link)) {
                $updates[] = "link = :link";
            }
            if (!empty($capa)) {
                $updates[] = "movie_image = :capa";
            }
            if (!empty($episodio)) {
                $updates[] = "episode_num = :episodio";
            }
            if (!empty($duration)) {
                $updates[] = "duration = :duration";
            }else{
                $updates[] = "duration = :duration";
            }

            $sql_update .= implode(", ", $updates);
            $sql_update .= " WHERE id = :id AND series_id = :series_id";

            $stmt_update = $conexao->prepare($sql_update);

            if (!empty($title)) {
                $stmt_update->bindParam(':title', $title, PDO::PARAM_STR);
            }
            if (!empty($link)) {
                $stmt_update->bindParam(':link', $link, PDO::PARAM_STR);
            }
            if (!empty($capa)) {
                $stmt_update->bindParam(':capa', $capa, PDO::PARAM_STR);
            }
            if (!empty($episodio)) {
                $stmt_update->bindParam(':episodio', $episodio, PDO::PARAM_INT);
            }

            if (!empty($duration)) {
                $stmt_update->bindParam(':duration', $duration, PDO::PARAM_STR);
            }else{
                $stmt_update->bindValue(':duration', null, PDO::PARAM_STR);
            }

            $stmt_update->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt_update->bindParam(':series_id', $series_id, PDO::PARAM_INT);

            if ($stmt_update->execute()) {
                $resposta = ver_episodios($series_id, $temporada, $episodio);
                $resposta['title'] = "Concluído!";
                $resposta['msg'] = "episodio editado com sucesso";
                $resposta['icon'] = "success";
                return $resposta;
            } else {
                $resposta['title'] = "Erro!";
                $resposta['msg'] = "Erro ao editar episodio";
                $resposta['icon'] = "error";
            }

            return $resposta;
        } else {

            $resposta['title'] = "Erro!";
            $resposta['msg'] = "serie não encontrado";
            $resposta['icon'] = "error";
            return $resposta;
        }

}

function confirme_adicionar_episodios($series_id, $temporada) {
    $conexao = conectar_bd();
    $resposta = []; 

    $token = isset($_SESSION['token']) ? $_SESSION['token'] : "0";
    $admin_id = isset($_SESSION['admin_id']) ? $_SESSION['admin_id'] : null;

    if (!$series_id) {
        $resposta['title'] = "Erro!";
        $resposta['msg'] = "Não foi possível identificar a série.";
        $resposta['icon'] = "error";
        return $resposta;
    }
    if (!$temporada) {
        $resposta['title'] = "Erro!";
        $resposta['msg'] = "Não foi possível identificar a temporada.";
        $resposta['icon'] = "error";
        return $resposta;
    }

    $series_id = preg_replace("/[^0-9]/", "", $series_id);
    $temporada = preg_replace("/[^0-9]/", "", $temporada);

    $sql = "SELECT * FROM admin WHERE id = :admin_id AND token = :token";
    $stmt = $conexao->prepare($sql);
    $stmt->bindParam(':admin_id', $admin_id, PDO::PARAM_INT);
    $stmt->bindParam(':token', $token, PDO::PARAM_STR);
    $stmt->execute();

    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        if ($row['admin'] == 1) {

            $sql_episodios = "SELECT MAX(episode_num) AS max_episodio FROM series_episodes WHERE series_id = :series_id AND season = :season";
            $stmt_episodio = $conexao->prepare($sql_episodios);
            $stmt_episodio->bindParam(':series_id', $series_id, PDO::PARAM_INT);
            $stmt_episodio->bindParam(':season', $temporada, PDO::PARAM_INT);
            $stmt_episodio->execute();

            $max_episodio = $stmt_episodio->fetch(PDO::FETCH_ASSOC)['max_episodio'];
            $new_episodio_number = $max_episodio ? $max_episodio + 1 : 1;

            $sql_insert = "INSERT INTO series_episodes (series_id, episode_num, title, season) VALUES (:series_id, :episode_num, :title, :season)";
            $stmt_insert = $conexao->prepare($sql_insert);

            $title = "";
            if ($temporada < 10) {
                $title .= "S0".$temporada;
            }else{
                $title .= "S".$temporada;
            }
            if ($new_episodio_number < 10) {
                $title .= "E0".$new_episodio_number;
            }else{
                $title .= "E".$new_episodio_number;
            }

            $stmt_insert->bindParam(':series_id', $series_id, PDO::PARAM_INT);
            $stmt_insert->bindParam(':episode_num', $new_episodio_number, PDO::PARAM_INT);
            $stmt_insert->bindParam(':title', $title, PDO::PARAM_STR);
            $stmt_insert->bindParam(':season', $temporada, PDO::PARAM_INT);

            if ($stmt_insert->execute()) {
                $sql_update = "UPDATE series_seasons SET episode_count = episode_count + :episode_count WHERE series_id = :series_id AND season_number = :season_number";
                $stmt = $conexao->prepare($sql_update);
                $stmt->bindValue(':episode_count', 1, PDO::PARAM_INT);
                $stmt->bindParam(':series_id', $series_id, PDO::PARAM_INT);
                $stmt->bindParam(':season_number', $temporada, PDO::PARAM_INT);
                if ($stmt->execute()) {
                    $resposta = ver_episodios($series_id, $temporada, $new_episodio_number);
                    $resposta['title'] = "Concluido!";
                    $resposta['msg'] = "Episodio adicionado com sucesso e temporada atualizada!";
                    $resposta['icon'] = "success";
                    return $resposta;
                }
                $resposta = ver_episodios($series_id, $temporada, $new_episodio_number);
                $resposta['title'] = "Concluído!";
                $resposta['msg'] = "Episodio adicionado com sucesso!";
                $resposta['icon'] = "success";
                return $resposta;
            } else {
                $resposta['title'] = "Erro!";
                $resposta['msg'] = "Erro ao adicionar novo episodio.";
                $resposta['icon'] = "error";
            }
        } else {
            $resposta['title'] = "Erro!";
            $resposta['msg'] = "Você não tem permissão para realizar esta ação.";
            $resposta['icon'] = "error";
        }
    } else {
        $resposta['title'] = "Erro!";
        $resposta['msg'] = "Admin não encontrado ou token inválido.";
        $resposta['icon'] = "error";
    }

    return $resposta;
}

function detetar_episodio($series_id, $temporada, $episodio)
{
    $conexao = conectar_bd();
    $token = isset($_SESSION['token']) ? $_SESSION['token'] : "0";
    $admin_id = isset($_SESSION['admin_id']) ? $_SESSION['admin_id'] : null;

    $series_id = preg_replace("/[^0-9]/", "", $series_id);
    $temporada = preg_replace("/[^0-9]/", "", $temporada);
    $episodio = preg_replace("/[^0-9]/", "", $episodio);

    $sql = "SELECT *
            FROM admin
            WHERE id = :admin_id AND token = :token";
        $stmt = $conexao->prepare($sql);
        $stmt->bindParam(':admin_id', $admin_id, PDO::PARAM_INT);
        $stmt->bindParam(':token', $token);
        $stmt->execute();

    $resposta = []; 

    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

        $sql_delete = "DELETE FROM series_episodes WHERE series_id = :series_id AND episode_num = :episode_num AND season = :season";

        $stmt_delete = $conexao->prepare($sql_delete);

        $stmt_delete->bindParam(':series_id', $series_id, PDO::PARAM_INT);
        $stmt_delete->bindParam(':episode_num', $episodio, PDO::PARAM_INT);
        $stmt_delete->bindParam(':season', $temporada, PDO::PARAM_INT);

        if ($stmt_delete->execute()) {

            $next_episodio = $episodio + 1;
            $sql_update = "UPDATE series_seasons SET episode_count = episode_count - :episode_count WHERE series_id = :series_id AND season_number = :season_number";
            $stmt = $conexao->prepare($sql_update);
            $stmt->bindValue(':episode_count', 1, PDO::PARAM_INT);
            $stmt->bindParam(':series_id', $series_id, PDO::PARAM_INT);
            $stmt->bindParam(':season_number', $temporada, PDO::PARAM_INT);
            if ($stmt->execute()) {
                $resposta = ver_episodios($series_id, $temporada, $next_episodio);
                $resposta['title'] = "Concluido!";
                $resposta['msg'] = "Episodio deletado com sucesso";
                $resposta['icon'] = "success";
                return $resposta;
            }
            $resposta = ver_episodios($series_id, $temporada, $next_episodio);
            $resposta['title'] = "Concluído!";
            $resposta['msg'] = "Episodio deletado com sucesso!";
            $resposta['icon'] = "success";
            return $resposta;
        } else {
            $resposta['title'] = "Erro!";
            $resposta['msg'] = "Erro ao deletar episodio.";
            $resposta['icon'] = "error";
        }

        return $resposta;
    } else {
        return 0;
    }
}