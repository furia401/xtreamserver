<?php
function info_cliente($id)
{
    $conexao = conectar_bd();

    $token = isset($_SESSION['token']) ? $_SESSION['token'] : "0";

    $sql = "SELECT c.* 
            FROM clientes c 
            LEFT JOIN admin a ON c.admin_id = a.id 
            WHERE c.id = :id AND a.token = :token";
        $stmt = $conexao->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':token', $token);
        $stmt->execute();

    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

        extract($row);

        $modal_body = '<pre class="pre-unique text-wrap" id="pre-'.$id.'" >';

        $modal_body .= '✅ *Usuário:* '.$usuario.'<br>';
        $modal_body .= '✅ *Senha:* '.$senha.'<br>';
        $modal_body .= '🟠 *URL/Port:*  http://'.$_SERVER['HTTP_HOST'].'<br>';

        $modal_body .= '📶 *Máximo de conexões:* '.$conexoes.'<br>';
        $modal_body .= '🗓 *Data de Criado:* '.date('d-m-Y H:i:s', strtotime($Criado_em)).'<br>';
        $modal_body .= '🗓 *Data de validade:* '.date('d-m-Y H:i:s', strtotime($Vencimento)).'<br>';

        $modal_body .= '<br>';
        $modal_body .= '🟢 *Link (M3U):* <br> http://'.$_SERVER['HTTP_HOST'].'/get.php?username='.$usuario.'&password='.$senha.'&type=m3u_plus&amp;output=ts<br>';
        $modal_body .= '🟢 *Link (HLS):* <br> http://'.$_SERVER['HTTP_HOST'].'/get.php?username='.$usuario.'&password='.$senha.'&type=m3u_plus&amp;output=m3u8<br>';
        $modal_body .= '<br>';
        $modal_body .= '🟢 *Link (M3U Encurtado):* <br> http://'.$_SERVER['HTTP_HOST'].'/m3u-ts/'.$usuario.'/'.$senha.'<br>';
        $modal_body .= '🟢 *Link (HLS Encurtado):* <br> http://'.$_SERVER['HTTP_HOST'].'/m3u-m3u8/'.$usuario.'/'.$senha.'<br>';
        $modal_body .= '<br>';
        $modal_body .= '🟢 *Link (M3U SSIPTV Encurtado):* <br> http://'.$_SERVER['HTTP_HOST'].'/ss-ts/'.$usuario.'/'.$senha.'<br>';
        $modal_body .= '🟢 *Link (HLS SSIPTV Encurtado):* <br> http://'.$_SERVER['HTTP_HOST'].'/ss-m3u8/'.$usuario.'/'.$senha.'<br>';
        $modal_body .= '<br>';
        $modal_body .= '♦️ *DNS STB:* Indisponivel <br>';
        $modal_body .= '</pre>';

        $modal_footer = "<button type='button' class='btn btn-info waves-effect waves-light j_copy_clipboard' onclick='copyText(\"pre-".$id."\")'>Copiar</button>";

        $resposta = [
            'modal_header_class'=> "d-block modal-header bg-info text-white",
            'modal_titulo'=> "lista do usuario (".$usuario.")",
            'modal_body'=> $modal_body,
            'modal_footer'=> $modal_footer
        ];

    return $resposta;
    } else {
        return 0;
    }
}

function edite_cliente($id)
{
    $conexao = conectar_bd();

    $token = isset($_SESSION['token']) ? $_SESSION['token'] : "0";

    $sql = "SELECT c.*
            FROM clientes c 
            LEFT JOIN admin a ON c.admin_id = a.id 
            WHERE c.id = :id AND a.token = :token";
        $stmt = $conexao->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':token', $token, PDO::PARAM_STR);
        $stmt->execute();

    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

        extract($row);

        $plano1 = $conexao->query("SELECT * FROM planos WHERE admin_id = '$admin_id'");
        $planoOptions = "";
        $planoOptions2 = "";
        while ($lista_plano = $plano1->fetch()) {

            if ($lista_plano['id'] == $plano) {
                $planoOptions .= '<option value="'.$lista_plano['id'].'"> Plano Atual => '.$lista_plano['nome'].' [R$: '.$lista_plano['valor'].']</option>';
            }else{
                $planoOptions2 .= '<option value="'.$lista_plano['id'].'">'.$lista_plano['nome'].' [R$: '.$lista_plano['valor'].']</option>';
            }
        }

        $stmt = $conexao->query("SELECT * FROM devices_apps");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $devices = array();
        foreach ($rows as $row) {
          $deviceName = $row['device_name'];
          $appName = $row['app_name'];

          if (!isset($devices[$deviceName])) {
            $devices[$deviceName] = array();
          }

          $devices[$deviceName][] = array("name" => $appName);
        }

        $data = array("devices" => array());
        foreach ($devices as $deviceName => $apps) {
          $data["devices"][] = array("name" => $deviceName, "apps" => $apps);
        }

        $jsonData = json_encode($data);

        $modal_body = '';
        $modal_body .= '<input type="hidden" name="confirme_edite_cliente" value="">';
        $modal_body .= '<input type="hidden" name="id" value="'.$id.'">';
        $modal_body .= '<div class="form-group">
                        <label for="name">Nome do cliente:</label>
                        <input type="text" class="form-control" id="name" name="name" value="'.$name.'">
                    </div>';
        $modal_body .= '<div class="form-row row">';
        $modal_body .= '<div class="form-group col-md col">
        <label for="usuario">Usuario</label>
        <input type="text" class="form-control" name="usuario" value="'.$usuario.'">
    </div>';
        $modal_body .= ' <div class="form-group col-md col">
        <label for="senha">Senha</label>
        <input type="text" class="form-control" name="senha" value="'.$senha.'">
    </div>';
        $modal_body .= '</div>';
        $modal_body .= '<div class="form-group"> <label for="adulto">Conteudo adulto?</label>
                <select class="form-control form-select" name="adulto" id="adulto">
                                            <option value="'.$adulto.'"">('. ($adulto == 0 ? "NÃO" : "SIM") .')</option>';

        switch ($adulto) {
            case "0":
                $modal_body .= "<option value='1'>Mudar PARA ( SIM )</option>";
                break;
            case "1":
                $modal_body .= "<option value='0'>Mudar PARA ( NAO )</option>";
                break;
        }

        $modal_body .= '</select></div>';

        $modal_body .= '<div class="form-group">
            <label for="plano">plano:</label>
            <select class="form-control form-select" id="plano" name="plano">
                '.$planoOptions.' '.$planoOptions2.'
            </select>
    </div>';

        $modal_body .= '<div class="bform-row row">';
        $modal_body .= '<div class="col-6 form-group">';
            $modal_body .= '<label for="Dispositivo">Dispositivo:</label>
            <select class="form-control form-select" id="Dispositivo" name="dispositivo">
                <option value="'.$Dispositivo.'">'.$Dispositivo.' [Atual]</option>

            </select>';
            $modal_body .= '</div>';
            $modal_body .= '<div class="col-6 form-group">';
            $modal_body .= '<label for="App">App:</label>
            <select class="form-control form-select" id="App" name="app">
            <option value="'.$App.'">'.$App.' [Atual]</option>
            </select>';
            $modal_body .= '</div>';

        $modal_body .= '</div>';
        $modal_body .= '<div class="form-group">

            <label for="Forma-de-pagamento">Forma de pagamento:</label>
                <select class="form-control form-select" id="forma-de-pagamento" name="forma-de-pagamento">
                <option value="PIX">PIX</option>
                <option value="boleto">Boleto</option>
                </select>

        <label for="nome-do-pagador">Nome do pagador:</label>
        <input type="text" class="form-control" id="nome-do-pagador" name="nome-do-pagador" value="'.$nome_do_pagador.'" placeholder="Exemplo: josé joão da silva">

    </div>';
        $modal_body .= '<div class="form-group">
        <label for="Whatsapp">Whatsapp:</label>
            <input type="text"class="form-control" id="Whatsapp" name="whatsapp" value="'.$Whatsapp.'" placeholder="Exemplo: +55 11 99999-9999">
    </div>';
    $modal_body .= '<div class="form-group">
        <label for="Whatsapp">Indicado por:</label>
            <input type="text"class="form-control" id="indicacao" name="indicacao" value="'.$indicado_por.'" placeholder="Coloque o ID do Usuário que fez a indicação">
    </div>';
    if ($App == "SMART ONE" || $App == "IBO PLAYER PRO" || $App == "IBO PRO") {
        $modal_body .= '<div class="form-group">
        <label for="device_mac">MAC:</label>
            <input type="text"class="form-control" id="device_mac" maxlength="17" name="device_mac" value="'.$device_mac.'" placeholder="Exemplo:  D4:1B:11:22:B0:44 ou D41B1122B044">
    </div>';
    }
    if ($App == "ClouddY") {
        $modal_body .= '<div class="form-group">
        <label for="email_app">EMAIL-APP:</label>
            <input type="text"class="form-control" id="email_app" name="email_app" value="'.$email_app.'" placeholder="Exemplo: josefulano@gmail.com">
    </div>';
    $modal_body .= '<div class="form-group">
        <label for="senha_app">Senha-APP:</label>
            <input type="text"class="form-control" id="senha_app"  name="senha_app" value="'.$senha_app.'" placeholder="coloque a senha do aplicativo aqui">
    </div>';
    }
    if ($App == "IBO PLAYER PRO" || $App == "IBO PRO") {
        $modal_body .= '<div class="form-group">
        <label for="device_key">Device key:</label>
            <input type="text"class="form-control" id="device_key" name="device_key" value="'.$device_key.'" placeholder="Exemplo:  112233">
    </div>';
    }
    if ($App == "SMART ONE" || $App == "IBO PLAYER PRO" || $App == "IBO PRO" || $App == "ClouddY") {
        $modal_body .= '<div class="form-group">
        <label for="data_app">Vencimento App:</label>
            <input type="date"class="form-control" id="data_app" name="data_app" value="'.$validade_app.'">
    </div>';
    }

        $modal_body .= '<script>
  var data = ' . $jsonData . ';
  data.devices.forEach(device => {
    const option = document.createElement("option");
    option.value = device.name;
    option.text = device.name;
    document.querySelector("#Dispositivo").appendChild(option);
  });

  document.querySelector("#Dispositivo").addEventListener("change", function () {
    const selectedDevice = data.devices.find(device => device.name === this.value);
    document.querySelector("#App").innerHTML = "";
    const option = document.createElement("option");
    option.value = "";
    option.text = "Selecione um app";
    document.querySelector("#App").appendChild(option);
    if (selectedDevice) {
      selectedDevice.apps.forEach(app => {
        const option = document.createElement("option");
        option.value = app.name;
        option.text = app.name;
        document.querySelector("#App").appendChild(option);
      });
    }
  });

    document.querySelector("#App").addEventListener("change", function () {
    const selectedApp = this.value;
    const deviceMacInput = document.querySelector("#device_mac");
    const deviceKeyInput = document.querySelector("#device_key");
    const demailappInput = document.querySelector("#email_app");
    const senhaappInput = document.querySelector("#senha_app");
    const dataappInput = document.querySelector("#data_app");

    if (deviceMacInput) {
        deviceMacInput.disabled = true;
    }
    if (deviceKeyInput) {
        deviceKeyInput.disabled = true;
    }
    if (demailappInput) {
        demailappInput.disabled = true;
    }
    if (senhaappInput) {
        senhaappInput.disabled = true;
    }
    if (dataappInput) {
        dataappInput.disabled = true;
    }

    if (selectedApp === "SMART ONE") {
        if (deviceMacInput) {
            deviceMacInput.disabled = false;
        }
    }
    if (selectedApp === "IBO PLAYER PRO" || selectedApp === "IBO PRO") {
        if (deviceMacInput) {
            deviceMacInput.disabled = false;
        }
        if (deviceKeyInput) {
            deviceKeyInput.disabled = false;
        }
    }
    if (selectedApp === "ClouddY") {
        if (demailappInput) {
            demailappInput.disabled = false;
        }
        if (senhaappInput) {
            senhaappInput.disabled = false;
        }
    }
    if (selectedApp === "IBO PLAYER PRO" || selectedApp === "IBO PRO" || selectedApp === "ClouddY") {
        if (dataappInput) {
            dataappInput.disabled = false;
        }
    }
    });</script>';
        $modal_body .= '';

        $modal_footer = "<button type='button' onclick='enviardados(\"modal_master_form\", \"clientes.php\")' class='btn btn-info waves-effect waves-light' >Salvar</button><button type='button' class='btn btn-danger' data-bs-dismiss='modal' aria-label='Close'>Cancelar</button>";

        $resposta = [
            'modal_header_class'=> "d-block modal-header bg-info text-white",
            'modal_titulo'=> "lista do usuario (".$usuario.")",
            'modal_body'=> $modal_body,
            'modal_footer'=> $modal_footer
        ];

    return $resposta;
    } else {
        return 0;
    }
}

function confirme_edite_cliente($id, $name, $usuario, $senha, $fonte, $adulto, $plano, $Dispositivo, $App, $Forma_de_pagamento, $nome_do_pagador, $Whatsapp, $indicacao, $mac, $key,$email_app, $senha_app, $validade_app)
{
    $conexao = conectar_bd();

    $token = isset($_SESSION['token']) ? $_SESSION['token'] : "0";
    $admin_id = isset($_SESSION['admin_id']) ? $_SESSION['admin_id'] : null;

    $id = preg_replace("/[^0-9]/", "", $id);
    $name = preg_replace("/[^a-zA-Z0-9 ]/", "", $name);
    $usuario = preg_replace("/[^a-zA-Z0-9]/", "", $usuario);
    $senha = preg_replace("/[^a-zA-Z0-9]/", "", $senha);
    $adulto = preg_replace("/[^0-9]/", "", $adulto);
    $plano = preg_replace("/[^0-9]/", "", $plano);
    $Dispositivo = preg_replace("/[^a-zA-Z0-9 ]/", "", $Dispositivo);
    $App = preg_replace("/[^a-zA-Z0-9 ]/", "", $App);
    $Forma_de_pagamento = preg_replace("/[^a-zA-Z0-9]/", "", $Forma_de_pagamento);
    $nome_do_pagador = preg_replace("/[^a-zA-Z0-9 ]/", "", $nome_do_pagador);

    if ($mac == 'não está presente') {

    }elseif($mac == 'vazio') {
        $mac = null;
    }else{
        $mac = preg_replace("/[^a-zA-Z0-9:]/", "", $mac);

        $mac_formatado = preg_replace('/[^a-fA-F0-9]/', '', $mac);

        $mac = strtoupper(implode(':', str_split($mac_formatado, 2)));
    }

    if (!empty($key)) {
        $key = preg_replace("/[^0-9]/", "", $key);
    } else {
        $key = null;
    }

    if (!empty($Whatsapp)) {
        $Whatsapp = preg_replace("/[^0-9+]/", "", $Whatsapp); 
        $Whatsapp = is_numeric($Whatsapp) ? $Whatsapp : null; 
    } else {
        $Whatsapp = null; 
    }

    if (!empty($indicacao)) {
        $indicacao = preg_replace("/[^0-9]/", "", $indicacao); 
        $indicacao = is_numeric($indicacao) ? $indicacao : null; 
    } else {
        $indicacao = null; 
    }

    if ($email_app == 'não está presente') {

    }elseif($email_app == 'vazio') {
        $email_app = null;
    }else{
        $email_app = preg_replace("/[^a-zA-Z0-9@. ]/", "", $email_app);
    }

    if ($senha_app == 'não está presente') {

    }elseif($senha_app == 'vazio') {
        $senha_app = null;
    }else{
        $senha_app = preg_replace("/[^a-zA-Z0-9@. ]/", "", $senha_app);
    }

    if ($validade_app == 'não está presente') {

    }elseif($validade_app == 'vazio') {
        $validade_app = null;
    }else{
        $validade_app = preg_replace("/[^0-9-]/", "", $validade_app);
    }

    $sql = "SELECT c.*,
                a.id as admin_id,
                p.valor as valor
            FROM clientes c 
            LEFT JOIN admin a ON c.admin_id = a.id 
            LEFT JOIN planos p ON p.id = :plano 
            WHERE c.id = :id AND a.token = :token";
        $stmt = $conexao->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':plano', $plano, PDO::PARAM_INT);
        $stmt->bindParam(':token', $token);
        $stmt->execute();

    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

            $hoje = date('Y-m-d');
            $valor_plano = $row['valor']; 

            $sql_update = "UPDATE clientes SET ";
            $updates = [];

            if (!empty($adulto)) {
                $updates[] = "adulto = :adulto";
            }else{
                $updates[] = "adulto = 0";
            }
            if (!empty($name)) {
                $updates[] = "name = :name";
            }
            if (!empty($usuario)) {
                $updates[] = "usuario = :usuario";
            }
            if (!empty($senha)) {
                $updates[] = "senha = :senha";
            }
            if (!empty($plano)) {
                $updates[] = "plano = :plano";
            }
            if (isset($valor_plano) || $valor_plano === 0) {
                $updates[] = "V_total = :valor_plano";
            }
            if (!empty($Dispositivo)) {
                $updates[] = "Dispositivo = :Dispositivo";
            }
            if (!empty($App)) {
                $updates[] = "App = :App";
            }
            if (!empty($Forma_de_pagamento)) {
                $updates[] = "Forma_de_pagamento = :Forma_de_pagamento";
            }
            if (!empty($nome_do_pagador)) {
                $updates[] = "nome_do_pagador = :nome_do_pagador";
            }else{
                $updates[] = "nome_do_pagador = null";
            }
            if (!empty($Whatsapp)) {
                $updates[] = "Whatsapp = :Whatsapp";
            }else{
                $updates[] = "Whatsapp = null";
            }
            if (!empty($indicacao)) {
                $updates[] = "indicado_por = :indicacao";
            }else{
                $updates[] = "indicado_por = null";
            }
            if (($App == "SMART ONE" || $App == "IBO PLAYER PRO" || $App == "IBO PRO") && $mac !== 'não está presente') {
                if (!empty($mac)) {
                    $updates[] = "device_mac = :device_mac";
                }else{
                        $updates[] = "device_mac = null";
                    }
                if (!empty($validade_app)) {
                    $updates[] = "validade_app = :validade_app";
                }else{
                    $updates[] = "validade_app = null";
                }
            }
            if (($App == "IBO PLAYER PRO" || $App == "IBO PRO") && $key !== 'não está presente') {
                if (!empty($key)) {
                    $updates[] = "device_key = :device_key";
                }else{
                        $updates[] = "device_key = null";
                    }
            }
            if ($App == "ClouddY" && $email_app !== 'não está presente') {
                if (!empty($email_app)) {
                    $updates[] = "email_app = :email_app";
                }else{
                    $updates[] = "email_app = null";
                }
                if (!empty($senha_app)) {
                    $updates[] = "senha_app = :senha_app";
                }else{
                    $updates[] = "senha_app = null";
                }
                if (!empty($validade_app)) {
                    $updates[] = "validade_app = :validade_app";
                }else{
                    $updates[] = "validade_app = null";
                }
            }

            $sql_update .= implode(", ", $updates);
            $sql_update .= " WHERE id = :id AND admin_id = :admin_id";

            $stmt_update = $conexao->prepare($sql_update);

            if (!empty($adulto)) {
                $stmt_update->bindParam(':adulto', $adulto, PDO::PARAM_INT);
            }
            if (!empty($name)) {
                $stmt_update->bindParam(':name', $name, PDO::PARAM_STR);
            }
            if (!empty($usuario)) {
                $stmt_update->bindParam(':usuario', $usuario, PDO::PARAM_STR);
            }
            if (!empty($senha)) {
                $stmt_update->bindParam(':senha', $senha, PDO::PARAM_STR);
            }
            if (!empty($plano)) {
                $stmt_update->bindParam(':plano', $plano, PDO::PARAM_INT);
            }
            if (isset($valor_plano) || $valor_plano === 0) {
                $stmt_update->bindParam(':valor_plano', $valor_plano);
            }
            if (!empty($Dispositivo)) {
                $stmt_update->bindParam(':Dispositivo', $Dispositivo, PDO::PARAM_STR);
            }
            if (!empty($App)) {
                $stmt_update->bindParam(':App', $App, PDO::PARAM_STR);
            }
            if (!empty($Forma_de_pagamento)) {
                $stmt_update->bindParam(':Forma_de_pagamento', $Forma_de_pagamento, PDO::PARAM_STR);
            }
            if (!empty($nome_do_pagador)) {
                $stmt_update->bindParam(':nome_do_pagador', $nome_do_pagador, PDO::PARAM_STR);
            }
            if (!empty($Whatsapp)) {
                $stmt_update->bindParam(':Whatsapp', $Whatsapp, PDO::PARAM_STR);
            }
            if (!empty($indicacao)) {
                $stmt_update->bindParam(':indicacao', $indicacao, PDO::PARAM_STR);
            }
            if (($App == "SMART ONE" || $App == "IBO PLAYER PRO" || $App == "IBO PRO") && $mac !== 'não está presente'){
                if (!empty($mac)) {
                    $stmt_update->bindParam(':device_mac', $mac, PDO::PARAM_STR);
                }
                if (!empty($validade_app)) {
                    $stmt_update->bindParam(':validade_app', $validade_app);
                }
            }
            if (($App == "IBO PLAYER PRO" || $App == "IBO PRO") && $key !== 'não está presente') {
                if (!empty($key)) {
                    $stmt_update->bindParam(':device_key', $key, PDO::PARAM_STR);
                }
            }
            if ($App == "ClouddY" && $email_app !== 'não está presente') {
                if (!empty($email_app)) {
                    $stmt_update->bindParam(':email_app', $email_app, PDO::PARAM_STR);
                }
                if (!empty($senha_app)) {
                    $stmt_update->bindParam(':senha_app', $senha_app, PDO::PARAM_STR);
                }
                if (!empty($validade_app)) {
                    $stmt_update->bindParam(':validade_app', $validade_app);
                }
            }

            $stmt_update->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt_update->bindParam(':admin_id', $admin_id, PDO::PARAM_INT);

            if ($stmt_update->execute()) {
                $resposta['title'] = "Concluído!";
                $resposta['msg'] = "Cliente editado com sucesso";
                $resposta['icon'] = "success";
            } else {
                $resposta['title'] = "Erro!";
                $resposta['msg'] = "Erro ao editar cliente";
                $resposta['icon'] = "error";
            }

            return $resposta;
        } else {

            $resposta['title'] = "Erro!";
            $resposta['msg'] = "Cliente não encontrado";
            $resposta['icon'] = "error";
            return $resposta;
        }

}
function renovar_cliente($id, $usuario)
{

        $modal_body = "<input type=\"hidden\"  id=\"confirme_renovar_cliente\" name=\"confirme_renovar_cliente\" value='$id'></div>";
        $modal_body .= "<label>Renovar por:</label>";
        $modal_body .= "<input type='number' name='meses' class='form-control' id='renovar-meses' placeholder='meses' min='-3' value='1'>";
        $modal_body .= "<small class='form-text text-muted'>meses</small>";

        $modal_footer = "<button type='button' class='btn btn-info waves-effect waves-light ' onclick='enviardados(\"modal_master_form\", \"clientes.php\")'>Renovar</button>";

        $resposta = [
            'modal_header_class'=> "d-block modal-header bg-info text-white",
            'modal_titulo'=> "Renovar Cliente ($usuario)",
            'modal_body'=> $modal_body,
            'modal_footer'=> $modal_footer
        ];

    return $resposta;

}

function confirme_renovar_cliente($id, $meses)
{
    $conexao = conectar_bd();

    $token = isset($_SESSION['token']) ? $_SESSION['token'] : "0";

    $sql = "SELECT c.*,
                a.admin as admin, a.email as email, a.creditos as creditos,
                p.valor as valor
            FROM clientes c 
            LEFT JOIN admin a ON c.admin_id = a.id 
            LEFT JOIN planos p ON c.plano = p.id 
            WHERE c.id = :id AND a.token = :token";
        $stmt = $conexao->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':token', $token);
        $stmt->execute();

    $resposta = []; 

    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

        extract($row);

        $ultimopagamento = date("Y-m-d H:i:s");

        if ($meses < 0) {

            $nova_data = date("Y-m-d", strtotime("-" . abs($meses) . " month", strtotime($Vencimento)));
            $nova_data .= " 23:59:59";

        }else {

            $nova_data = date("Y-m-d", strtotime("$Vencimento +" . abs($meses) . " month"));

            if (strtotime($Vencimento) < strtotime(date("Y-m-d H:i:s"))) {

                $nova_data = date("Y-m-d", strtotime("+" . abs($meses) . " month"));
            }

            $nova_data .= " 23:59:59";
        }

        if ($admin == 1) {

            $sql_update ="UPDATE clientes SET Vencimento = '$nova_data', Ultimo_pagamento = '$ultimopagamento', V_total = '$valor' WHERE id = '$id'";

            if ($conexao->exec($sql_update)) {

                if ($meses > 0) {

                    $resposta['title'] = "Concluido! foi consumido ". $meses ." creditos";
                    $resposta['msg'] = "Cliente renovado com sucesso. Nova Data => ". date("d-m-Y H:i:s", strtotime($nova_data));
                    $resposta['icon'] = "success";
                    return $resposta;
                }else{

                        $resposta['title'] = "Concluido!";
                        $resposta['msg'] = $meses." mes removido . Nova Data => ". date("d-m-Y H:i:s", strtotime($nova_data));
                        $resposta['icon'] = "success";
                }

            }else{
                $resposta['title'] = "Erro!";
                $resposta['msg'] = "Nao foi possivel fazer a renovaçao";
                $resposta['icon'] = "error";
            }

        }elseif ($admin != 1 && $creditos >= 1 && $creditos>= $meses) {
            $sql_update ="UPDATE clientes SET Vencimento = '$nova_data', Ultimo_pagamento = '$ultimopagamento', V_total = '$valor' WHERE id = '$id'";
            if ($meses >= 0) {
                if ($conexao->exec($sql_update)) {

                    $sql_update = "UPDATE admin SET creditos = creditos - $meses WHERE id = '$admin_id'";

                    if ($conexao->exec($sql_update)) {

                        $resposta['title'] = "Concluido!";
                        $resposta['msg'] = "Cliente renovado com sucesso. Nova Data => ". $nova_data;
                        $resposta['icon'] = "success";
                    }
                }else{
                    $resposta['title'] = "Erro!";
                    $resposta['msg'] = "Nao foi possivel fazer a renovaçao";
                    $resposta['icon'] = "error";
                }

            }else{
                $resposta['title'] = "Aviso!";
                $resposta['msg'] = "voce nao tem permição para remover meses";
                $resposta['icon'] = "error";
            }

        }else{

            $resposta['title'] = "Erro!";
            $resposta['msg'] = "voce nao tem creditos suficientes";
            $resposta['icon'] = "error";

        }

        return $resposta;
    } else {
        return 0;
    }
}

function add_tempo($id, $usuario)
{

        $modal_body = "<input type=\"hidden\" name=\"confirme_add_tempo_clientes\" value='$id'></div>";
        $modal_body .= "<label>Adicionar Horas:</label>";
        $modal_body .= "<input type='number' name='tempo' class='form-control' id='tempo' placeholder='Tempo' min='-72' value='1'>";
        $modal_body .= "<small class='form-text text-muted'>Adicionar Tempo em (HORAS)</small>";

        $modal_footer = "<button type='button' class='btn btn-success waves-effect waves-light' onclick='enviardados(\"modal_master_form\", \"clientes.php\")'>Confirmar</button>";

        $resposta = [
            'modal_header_class'=> "d-block modal-header bg-success text-white",
            'modal_titulo'=> "Adicionar Tempo para ($usuario)",
            'modal_body'=> $modal_body,
            'modal_footer'=> $modal_footer
        ];

    return $resposta;
}

function confirme_add_tempo_clientes($id, $tempo)
{
    $conexao = conectar_bd();

    $token = isset($_SESSION['token']) ? $_SESSION['token'] : "0";

    $tempo = preg_replace("/[^0-9 ]/", "", $tempo);

    if ($tempo > 72) {
        $tempo = 72;
    }

    if ($tempo < -72) {
        $tempo = -72;
    }

    $sql = "SELECT c.*,
                a.admin as admin, a.creditos as creditos,
                p.valor as valor
            FROM clientes c 
            LEFT JOIN admin a ON c.admin_id = a.id 
            LEFT JOIN planos p ON c.plano = p.id 
            WHERE c.id = :id AND a.token = :token";
        $stmt = $conexao->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':token', $token);
        $stmt->execute();

    $resposta = []; 

    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

        extract($row);

        if ($tempo < 0) {

            $nova_data = date("Y-m-d H:i:s", strtotime("-" . abs($tempo) . " hour", strtotime($Vencimento)));

        }else {

            $nova_data = date("Y-m-d H:i:s", strtotime("$Vencimento +" . abs($tempo) . " hour"));

            if (strtotime($Vencimento) < strtotime(date("Y-m-d H:i:s"))) {

                $nova_data = date("Y-m-d H:i:s", strtotime("+" . abs($tempo) . " hour"));
            }
        }

        if ($admin == 1) {

            $sql_update = "UPDATE clientes SET Vencimento = :nova_data WHERE id = :id";
            $stmt = $conexao->prepare($sql_update);
            $stmt->bindParam(':nova_data', $nova_data);
            $stmt->bindParam(':id', $id);

            if ($stmt->execute()) {

                if ($tempo > 0) {

                    $resposta['title'] = "Concluido!";
                    $resposta['msg'] = "Foi Adicionado ".$tempo." hora(s). ";
                    $resposta['icon'] = "success";
                }else{

                    $resposta['title'] = "Concluido!";
                    $resposta['msg'] = "Foi Removido ".$tempo." hora(s). ";
                    $resposta['icon'] = "success";
                }
            }else{
                $resposta['title'] = "Erro!";
                $resposta['msg'] = "Nao foi possivel fazer a renovaçao";
                $resposta['icon'] = "error";
            }

        }elseif ($admin != 1 && $creditos >= 0) {
            $sql_update = "UPDATE clientes SET Vencimento = :nova_data WHERE id = :id";
            $stmt = $conexao->prepare($sql_update);
            $stmt->bindParam(':nova_data', $nova_data);
            $stmt->bindParam(':id', $id);

            if ($stmt->execute()) {

                if ($tempo < 0) {

                    $resposta['title'] = "Concluido!";
                    $resposta['msg'] = "Foi Removido ".$tempo." hora(s). ";
                    $resposta['icon'] = "success";

                }else{

                    $resposta['title'] = "Concluido!";
                    $resposta['msg'] = "Foi Adicionado ".$tempo." hora(s). ";
                    $resposta['icon'] = "success";

                }

            }else{

                $resposta['title'] = "Erro!";
                $resposta['msg'] = "Nao foi possivel adicionar tempo";
                $resposta['icon'] = "error";

            }
        }else{

            $resposta['title'] = "Erro!";
            $resposta['msg'] = "voce nao tem creditos suficientes";
            $resposta['icon'] = "error";

        }

        return $resposta;
    } else {
        return 0;
    }
}

function delete_cliente($id, $usuario)
{

        $modal_body = "<input type=\"hidden\"  name=\"confirme_delete_cliente\" value='$id'></div>";
        $modal_body .= "<input type=\"hidden\"  name=\"usuario\" value='$usuario'></div>";
        $modal_body .= "Tem certeza de que deseja excluir o cliente ($usuario) ?";

        $modal_footer = "<button type='button' class='btn btn-primary btn-sm' data-bs-dismiss='modal' aria-label='Close'>Cancelar</button><button type='button' class='btn btn-danger btn-sm' onclick='enviardados(\"modal_master_form\", \"clientes.php\")'>EXCLUIR</button>";

        $resposta = [
            'modal_header_class'=> "d-block modal-header bg-danger text-white",
            'modal_titulo'=> "EXCLUIR CLIENTE",
            'modal_body'=> $modal_body,
            'modal_footer'=> $modal_footer
        ];

    return $resposta;
}

function confirme_delete_cliente($id, $usuario)
{
    $conexao = conectar_bd();
    $token = isset($_SESSION['token']) ? $_SESSION['token'] : "0";

    $sql = "SELECT c.*, a.id as admin_id
            FROM clientes c 
            LEFT JOIN admin a ON c.admin_id = a.id  
            WHERE c.id = :id AND a.token = :token";
        $stmt = $conexao->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':token', $token);
        $stmt->execute();

    $resposta = []; 

    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

        extract($row);

        $sql_delete = "DELETE FROM clientes WHERE id = '$id' and '$admin_id'";

        if ($conexao->exec($sql_delete)) {
            $resposta['title'] = "Sucesso!";
            $resposta['msg'] = "Cliente deletado com sucesso!";
            $resposta['icon'] = "success";
        } else {
            $resposta['title'] = "Erro!";
            $resposta['msg'] = "Erro ao deletar cliente.";
            $resposta['icon'] = "error";
        }

        return $resposta;
    } else {
        return 0;
    }
}

function adicionar_clientes()
{
    function gerarValoresAleatorios() {

        $randomValue1 = substr(str_shuffle('0123456789'), 0, 6); 

        return $randomValue1;
    }

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

        $plano1 = $conexao->query("SELECT * FROM planos WHERE admin_id = '$admin_id'");
        $planoOptions = "";
        while ($lista_plano = $plano1->fetch()) {

            $planoOptions .= '<option value="'.$lista_plano['id'].'">'.$lista_plano['nome'].' [R$: '.$lista_plano['valor'].']</option>';

        }

        $stmt = $conexao->query("SELECT * FROM devices_apps");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $devices = array();
        foreach ($rows as $rows) {
          $deviceName = $rows['device_name'];
          $appName = $rows['app_name'];

          if (!isset($devices[$deviceName])) {
            $devices[$deviceName] = array();
          }

          $devices[$deviceName][] = array("name" => $appName);
        }

        $data = array("devices" => array());
        foreach ($devices as $deviceName => $apps) {
          $data["devices"][] = array("name" => $deviceName, "apps" => $apps);
        }

        $jsonData = json_encode($data);

        $modal_body = "";
        $modal_body .= '<input type="hidden"  id="confirme_adicionar_clientes" name="confirme_adicionar_clientes" value=""></div>';
        $modal_body .= '<div class="form-group">
                        <label for="name">Nome do cliente:</label>
                        <input type="text" class="form-control" id="name" name="name" value="" autocomplete="off">
                        </div>';
        $modal_body .= '<div class="form-row row">';
            $modal_body .= '<div class="form-group col-md col">
                            <label for="usuario">Usuario</label>
                            <input type="text" class="form-control" name="usuario" value="'.gerarValoresAleatorios().'" autocomplete="off">
                            </div>';
            $modal_body .= ' <div class="form-group col-md col">
                            <label for="senha">Senha</label>
                            <input type="text" class="form-control" name="senha" value="'.gerarValoresAleatorios().'" autocomplete="off">
                            </div>';
        $modal_body .= '</div>';

        $modal_body .= '<div class="form-group">';
            $modal_body .= '<label for="adulto">Conteudo adulto?</label>';
                $modal_body .= '<select class="form-control form-select" name="adulto" id="adulto">';
                $modal_body .= '<option value="0"> NAO </option>';
                $modal_body .= '<option value="1"> SIM </option>';
            $modal_body .= '</select>';
        $modal_body .= '</div>';
        $modal_body .= '<div class="form-group">
                        <label for="plano">planos:</label>
                        <select class="form-control form-select" id="plano" name="plano">
                        '.$planoOptions.'
                        </select>';
        $modal_body .= '</div>';
        $modal_body .= '<div class="bform-row row">';
            $modal_body .= '<div class="col-6 form-group">';
            $modal_body .= '<label for="Dispositivo">Dispositivo:</label>
            <select class="form-control form-select" id="Dispositivo" name="dispositivo">
                <option value="">Selecione um dispositivo</option>

            </select>';
            $modal_body .= '</div>';
            $modal_body .= '<div class="col-6 form-group">';
            $modal_body .= '<label for="App">App:</label>
            <select class="form-control form-select" id="App" name="app">
            <option value="">Selecione um app</option>
            </select>';
            $modal_body .= '</div>';
        $modal_body .= '</div>';
        $modal_body .= '<div class="form-group">
                        <label for="Forma-de-pagamento">Forma de pagamento:</label>
                        <select class="form-control form-select" id="forma-de-pagamento" name="forma-de-pagamento">
                        <option value="PIX">PIX</option>
                        <option value="boleto">Boleto</option>
                        </select>';
        $modal_body .= '</div>';
        $modal_body .= '<div class="form-group">
                        <label for="nome-do-pagador">Nome do pagador:</label>
                        <input type="text" class="form-control" id="nome-do-pagador" name="nome-do-pagador" value="" placeholder="Exemplo: josé joão da silva" autocomplete="off">';
        $modal_body .= '</div>';
        $modal_body .= '<div class="form-group">
                        <label for="Whatsapp">Whatsapp:</label>
                        <input type="text"class="form-control" id="Whatsapp" name="whatsapp" value="" placeholder="Exemplo: +55 11 99999-9999" autocomplete="off">';
        $modal_body .= '</div>';
        $modal_body .= '<div class="form-group">
                        <label for="Whatsapp">Indicado por:</label>
                        <input type="text"class="form-control" id="indicacao" name="indicacao" value="" placeholder="Coloque o ID do Usuário que fez a indicação" autocomplete="off">';
        $modal_body .= '</div>';
        $modal_body .= '<div class="form-group">
                        <label for="device_mac">MAC:</label>
                        <input type="text"class="form-control" id="device_mac" maxlength="17" name="device_mac" value="" placeholder="Exemplo:  D4:1B:11:22:B0:44 ou D41B1122B044" autocomplete="off" disabled>';
        $modal_body .= '</div>';
        $modal_body .= '<div class="form-group">
                        <label for="device_key">Device key:</label>
                        <input type="text"class="form-control" id="device_key" name="device_key" value="" placeholder="Exemplo:  112233" autocomplete="off" disabled>';
        $modal_body .= '</div>';

        $modal_body .= '<script>
  var data = ' . $jsonData . ';
  data.devices.forEach(device => {
    const option = document.createElement("option");
    option.value = device.name;
    option.text = device.name;
    document.querySelector("#Dispositivo").appendChild(option);
  });

  document.querySelector("#Dispositivo").addEventListener("change", function () {
    const selectedDevice = data.devices.find(device => device.name === this.value);
    document.querySelector("#App").innerHTML = "";
    const option = document.createElement("option");
    option.value = "";
    option.text = "Selecione um app";
    document.querySelector("#App").appendChild(option);
    if (selectedDevice) {
      selectedDevice.apps.forEach(app => {
        const option = document.createElement("option");
        option.value = app.name;
        option.text = app.name;
        option.dataset.deviceType = app.deviceType; 
        document.querySelector("#App").appendChild(option);
      });
    }
  });

  document.querySelector("#App").addEventListener("change", function () {
    const selectedApp = this.value;
    const deviceMacInput = document.querySelector("#device_mac");
    const deviceKeyInput = document.querySelector("#device_key");

    deviceMacInput.value = "";
    deviceKeyInput.value = "";

    deviceMacInput.disabled = true;
    deviceKeyInput.disabled = true;

    if (selectedApp === "SMART ONE") {
        deviceMacInput.disabled = false;
    } else if (selectedApp === "IBO PLAYER PRO" || selectedApp === "IBO PRO") {
        deviceMacInput.disabled = false;
        deviceKeyInput.disabled = false;
    }
    });</script>';

        $modal_footer = "<button type='button' class='btn btn-outline-danger btn-sm' data-bs-dismiss='modal' aria-label='Close'>Cancelar</button><button type='button' class='btn btn-primary btn-sm' onclick='enviardados(\"modal_master_form\", \"clientes.php\")'>Adicionar</button>";

        $resposta = [
            'modal_header_class'=> "d-block modal-header bg-primary text-white",
            'modal_titulo'=> "Preencha com os dados do cliente",
            'modal_body'=> $modal_body,
            'modal_footer'=> $modal_footer
        ];

        return $resposta;
    } else {
        return 0;
    }
}

function confirme_adicionar_clientes($name, $usuario, $senha, $adulto, $plano, $Dispositivo, $App, $Forma_de_pagamento, $nome_do_pagador, $Whatsapp, $indicacao, $mac, $key)
{
    $conexao = conectar_bd();

    $token = isset($_SESSION['token']) ? $_SESSION['token'] : "0";
    $admin_id = isset($_SESSION['admin_id']) ? $_SESSION['admin_id'] : null;

    $name = preg_replace("/[^a-zA-Z0-9 ]/", "", $name);
    $usuario = preg_replace("/[^a-zA-Z0-9]/", "", $usuario);
    $senha = preg_replace("/[^a-zA-Z0-9]/", "", $senha);
    $adulto = preg_replace("/[^0-9]/", "", $adulto);
    $plano = preg_replace("/[^0-9]/", "", $plano);
    $Dispositivo = preg_replace("/[^a-zA-Z0-9 ]/", "", $Dispositivo);
    $App = preg_replace("/[^a-zA-Z0-9 ]/", "", $App);
    $Forma_de_pagamento = preg_replace("/[^a-zA-Z0-9]/", "", $Forma_de_pagamento);
    $nome_do_pagador = preg_replace("/[^a-zA-Z0-9 ]/", "", $nome_do_pagador);

    if ($mac == 'não está presente') {
        $mac = null;
    }elseif($mac == 'vazio') {
        $mac = null;
    }else{
        $mac = preg_replace("/[^a-zA-Z0-9:]/", "", $mac);

        $mac_formatado = preg_replace('/[^a-fA-F0-9]/', '', $mac);

        $mac = strtoupper(implode(':', str_split($mac_formatado, 2)));
    }

    if ($key == 'não está presente') {
        $key = null;
    }elseif($key == 'vazio') {
        $key = null;
    }else{
        $key = preg_replace("/[^0-9]/", "", $key);
        $key = is_numeric($key) ? $key : null; 
    }

    if (!empty($Whatsapp)) {
        $Whatsapp = preg_replace("/[^0-9+]/", "", $Whatsapp); 
        $Whatsapp = is_numeric($Whatsapp) ? $Whatsapp : null; 
    } else {
        $Whatsapp = null; 
    }

    if (!empty($indicacao)) {
        $indicacao = preg_replace("/[^0-9]/", "", $indicacao); 
        $indicacao = is_numeric($indicacao) ? $indicacao : null; 
    } else {
        $indicacao = null; 
    }

    $sql = "SELECT a.id as admin_id, a.admin as admin, a.creditos as creditos,
                p.valor as valor
            FROM admin a 
            LEFT JOIN planos p ON p.id = :plano
            WHERE a.id = :admin_id AND a.token = :token";
        $stmt = $conexao->prepare($sql);
        $stmt->bindParam(':admin_id', $admin_id, PDO::PARAM_INT);
        $stmt->bindParam(':token', $token);
        $stmt->bindParam(':plano', $plano);
        $stmt->execute();

    $resposta = []; 

    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

        extract($row);

        $sql_check = "SELECT id FROM clientes WHERE usuario = :usuario";
        $stmt_check = $conexao->prepare($sql_check);
        $stmt_check->bindParam(':usuario', $usuario, PDO::PARAM_STR);
        $stmt_check->execute();

        if ($stmt_check->fetch(PDO::FETCH_ASSOC)) {

            $resposta['title'] = "Erro!";
            $resposta['msg'] = "Cliente já cadastrado";
            $resposta['icon'] = "error";
            return $resposta;
        }
        $dataAtual = date("Y-m-d H:i:s"); 
        $vencimento = date("Y-m-d", strtotime("+1 month")) . " 23:59:59";

        $sql_insert = "INSERT INTO clientes (name, usuario, senha, Criado_em, Ultimo_pagamento, vencimento, admin_id, adulto, plano, V_total, Dispositivo, App, Forma_de_pagamento, nome_do_pagador, Whatsapp, indicado_por, device_mac, device_key) 
                        VALUES (:name, :usuario, :senha, :Criado_em, :Ultimo_pagamento, :vencimento, :admin_id, :adulto, :plano, :V_total, :Dispositivo, :App, :Forma_de_pagamento, :nome_do_pagador, :Whatsapp, :indicacao, :mac, :chavekay)";
        $stmt_insert = $conexao->prepare($sql_insert);

        $stmt_insert->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt_insert->bindParam(':usuario', $usuario, PDO::PARAM_STR);
        $stmt_insert->bindParam(':senha', $senha, PDO::PARAM_STR);
        $stmt_insert->bindValue(':Criado_em', date("Y-m-d H:i:s"), PDO::PARAM_STR);
        $stmt_insert->bindValue(':Ultimo_pagamento', date("Y-m-d H:i:s"), PDO::PARAM_STR);
        $stmt_insert->bindParam(':vencimento', $vencimento, PDO::PARAM_STR);
        $stmt_insert->bindParam(':admin_id', $admin_id, PDO::PARAM_INT);
        $stmt_insert->bindParam(':adulto', $adulto, PDO::PARAM_INT);
        $stmt_insert->bindParam(':plano', $plano, PDO::PARAM_INT);
        $stmt_insert->bindParam(':V_total', $valor, PDO::PARAM_INT);
        $stmt_insert->bindParam(':Dispositivo', $Dispositivo, PDO::PARAM_STR);
        $stmt_insert->bindParam(':App', $App, PDO::PARAM_STR);
        $stmt_insert->bindParam(':Forma_de_pagamento', $Forma_de_pagamento, PDO::PARAM_STR);
        $stmt_insert->bindParam(':nome_do_pagador', $nome_do_pagador, PDO::PARAM_STR);
        $stmt_insert->bindParam(':Whatsapp', $Whatsapp, PDO::PARAM_STR);
        $stmt_insert->bindParam(':indicacao', $indicacao, PDO::PARAM_INT);
        $stmt_insert->bindParam(':mac', $mac, PDO::PARAM_STR);
        $stmt_insert->bindParam(':chavekay', $key, PDO::PARAM_STR);

        if ($admin == 1) {

            if ($stmt_insert->execute()) {
                $resposta['title'] = "Concluído!";
                $resposta['msg'] = "Cliente inserido com sucesso";
                $resposta['icon'] = "success";
            } else {
                $resposta['title'] = "Erro!";
                $resposta['msg'] = "Erro ao inserir cliente";
                $resposta['icon'] = "error";
            }
        }elseif ($admin != 1 && $creditos >= 1) {

            if ($stmt_insert->execute()) {

               $sql_update = "UPDATE admin SET creditos = creditos - '1' WHERE id = '$admin_id'";

                if ($conexao->exec($sql_update)) {

                    $resposta['title'] = "Concluído!";
                    $resposta['msg'] = "Cliente inserido com sucesso";
                    $resposta['icon'] = "success";
                }
            } else {
                $resposta['title'] = "Erro!";
                $resposta['msg'] = "Erro ao inserir cliente";
                $resposta['icon'] = "error";
            }
        }else{

            $resposta['title'] = "Erro!";
            $resposta['msg'] = "voce nao tem creditos suficientes";
            $resposta['icon'] = "error";

        }

        return $resposta;
    } else {
        return 0;
    }
}