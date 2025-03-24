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

        $modal_body = '<pre class="pre-unique text-wrap" id="pre-'.$id.'" style="max-height: 380px;">';

        $modal_body .= '*Usuário:* '.$usuario.'<br>';
        $modal_body .= '*Senha:* '.$senha.'<br>';
        $modal_body .= '*URL/Port:*  http://'.$_SERVER['HTTP_HOST'].'<br>';
        $modal_body .= '*Máximo de conexões:* '.$conexoes.'<br>';
        $modal_body .= '*Data de validade:* '.date('d-m-Y H:i:s', strtotime($Vencimento)).'<br>';
        $modal_body .= '<br>';
        $modal_body .= '*Link (M3U):* <br> http://'.$_SERVER['HTTP_HOST'].'/get.php?username='.$usuario.'&password='.$senha.'&type=m3u_plus&amp;output=ts<br>';
        $modal_body .= '*Link (HLS):* <br> http://'.$_SERVER['HTTP_HOST'].'/get.php?username='.$usuario.'&password='.$senha.'&type=m3u_plus&amp;output=m3u8<br>';
        $modal_body .= '<br>';
        $modal_body .= '*Link (M3U Encurtado):* <br> http://'.$_SERVER['HTTP_HOST'].'/m3u-ts/'.$usuario.'/'.$senha.'<br>';
        $modal_body .= '*Link (HLS Encurtado):* <br> http://'.$_SERVER['HTTP_HOST'].'/m3u-m3u8/'.$usuario.'/'.$senha.'<br>';
        $modal_body .= '<br>';
        $modal_body .= '*Link (M3U SSIPTV Encurtado):* <br> http://'.$_SERVER['HTTP_HOST'].'/ss-ts/'.$usuario.'/'.$senha.'<br>';
        $modal_body .= '*Link (HLS SSIPTV Encurtado):* <br> http://'.$_SERVER['HTTP_HOST'].'/ss-m3u8/'.$usuario.'/'.$senha.'<br>';
        $modal_body .= '<br>';
        $modal_body .= '*DNS STB:* Indisponivel <br>';
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
function add_tempo($id, $usuario)
{

        $modal_body = "<input type=\"hidden\"  id=\"confirme_add_tempo_testes\" name=\"confirme_add_tempo_testes\" value='$id'></div>";
        $modal_body .= "<label>Adicionar Horas:</label>";
        $modal_body .= "<input type='number' name='tempo' class='form-control' id='tempo' placeholder='Tempo' min='-72' value='1'>";
        $modal_body .= "<small class='form-text text-muted'>Adicionar Tempo em (HORAS)</small>";

        $modal_footer = "<button type='button' class='btn btn-success waves-effect waves-light' onclick='enviardados(\"modal_master_form\", \"testes.php\")'>Confirmar</button>";

        $resposta = [
            'modal_header_class'=> "d-block modal-header bg-success text-white",
            'modal_titulo'=> "Adicionar Tempo para ($usuario)",
            'modal_body'=> $modal_body,
            'modal_footer'=> $modal_footer
        ];

    return $resposta;
}

function confirme_add_tempo_testes($id, $tempo)
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

function ativar_teste($id, $usuario)
{

        $modal_body = "<input type=\"hidden\"  id=\"confirme_ativar_teste\" name=\"confirme_ativar_teste\" value='$id'></div>";
        $modal_body .= "<label>Ativar por:</label>";
        $modal_body .= "<input type='number' name='ativar_meses' class='form-control' id='ativar_meses' placeholder='meses' min='-3' value='1'>";
        $modal_body .= "<small class='form-text text-muted'>meses</small>";

        $modal_footer = "<button type='button' class='btn btn-success waves-effect waves-light j_copy_clipboard' onclick='enviardados(\"modal_master_form\", \"testes.php\")'>Renovar</button>";

        $resposta = [
            'modal_header_class'=> "d-block modal-header bg-success text-white",
            'modal_titulo'=> "Ativar Cliente ($usuario)",
            'modal_body'=> $modal_body,
            'modal_footer'=> $modal_footer
        ];

    return $resposta;
}

function confirme_ativar_teste($id, $meses)
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

        $ultimopagamento = date("Y-m-d");

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

            $sql_update ="UPDATE clientes SET Vencimento = '$nova_data', Ultimo_pagamento = '$ultimopagamento', V_total = '$valor', is_trial = '0' WHERE id = '$id'";

            if ($conexao->exec($sql_update)) {

                if ($meses > 0) {

                    $resposta['title'] = "Concluido! foi consumido ". $meses ." credito(s)";
                    $resposta['msg'] = "Cliente Ativado com sucesso. Nova Data => ". date("d-m-Y H:i:s", strtotime($nova_data));
                    $resposta['icon'] = "success";
                    return $resposta;
                }else{

                        $resposta['title'] = "Concluido!";
                        $resposta['msg'] = $meses." mes removido . Nova Data => ". date("d-m-Y H:i:s", strtotime($nova_data));
                        $resposta['icon'] = "success";
                }
            }else{
                $resposta['title'] = "Erro!";
                $resposta['msg'] = "Nao foi possivel fazer a Ativaçao";
                $resposta['icon'] = "error";
            }

        }elseif ($admin != 1 && $creditos >= 1 && $creditos>= $meses) {
            $sql_update ="UPDATE clientes SET Vencimento = '$nova_data', Ultimo_pagamento = '$ultimopagamento', V_total = '$valor', is_trial = '0' WHERE id = '$id'";
            if ($meses >= 0) {
                if ($conexao->exec($sql_update)) {

                    $sql_update = "UPDATE admin SET creditos = creditos - $meses WHERE id = '$admin_id'";

                    if ($conexao->exec($sql_update)) {

                        $resposta['title'] = "Concluido!";
                        $resposta['msg'] = "Cliente Ativado com sucesso. Nova Data => ". $nova_data;
                        $resposta['icon'] = "success";
                    }
                }else{
                    $resposta['title'] = "Erro!";
                    $resposta['msg'] = "Nao foi possivel fazer a Ativaçao";
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

function delete_cliente($id, $usuario)
{

        $modal_body = "<input type=\"hidden\"  id=\"confirme_delete_cliente\" name=\"confirme_delete_cliente\" value='$id'></div>";
        $modal_body .= "<input type=\"hidden\"  id=\"usuario\" name=\"usuario\" value='$usuario'></div>";
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

function confirme_delete_cliente($id, $usuariot)
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

        $sql_delete = "DELETE FROM clientes WHERE id = '$id' and admin_id = '$admin_id'";

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

function adicionar_testes()
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
        $modal_body .= '<input type="hidden"  id="confirme_adicionar_testes" name="confirme_adicionar_testes" value=""></div>';
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
        $modal_body .= '<div class="form-group">
                        <label for="tempo">Duaração em (Hora)</label>
                        <input type="number" class="form-control" min="1" max="12" name="tempo" value="3">
                        </div>';

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

        $modal_footer = "<button type='button' class='btn btn-outline-danger btn-sm' data-bs-dismiss='modal' aria-label='Close'>Cancelar</button><button type='button' class='btn btn-primary btn-sm' onclick='enviardados(\"modal_master_form\", \"testes.php\")'>Adicionar</button>";

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

function confirme_adicionar_testes($name, $usuario, $senha, $adulto, $plano, $Dispositivo, $App, $Forma_de_pagamento, $nome_do_pagador, $Whatsapp, $indicacao, $mac, $key, $tempo)
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
    $tempo = preg_replace("/[^0-9 ]/", "", $tempo);

    if ($tempo > 72) {
        $tempo = 72;
    }

    if ($tempo < -72) {
        $tempo = -72;
    }

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

    $sql = "SELECT a.id as admin_id, a.creditos as creditos,
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
        $vencimento = date("Y-m-d H:i:s", strtotime("+" . abs($tempo) . " hour"));

        $sql_insert = "INSERT INTO clientes (name, usuario, senha, Criado_em, Ultimo_pagamento, vencimento, admin_id, adulto, plano, V_total, Dispositivo, App, Forma_de_pagamento, nome_do_pagador, Whatsapp, indicado_por, device_mac, device_key, is_trial) 
                        VALUES (:name, :usuario, :senha, :Criado_em, :Ultimo_pagamento, :vencimento,  :admin_id, :adulto, :plano, :V_total, :Dispositivo, :App, :Forma_de_pagamento, :nome_do_pagador, :Whatsapp, :indicacao, :mac, :chavekay, :is_trial)";
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
        $stmt_insert->bindValue(':is_trial', "1", PDO::PARAM_INT);

        if ($stmt_insert->execute()) {
            $resposta['title'] = "Concluído!";
            $resposta['msg'] = "Cliente inserido com sucesso";
            $resposta['icon'] = "success";
        } else {
            $resposta['title'] = "Erro!";
            $resposta['msg'] = "Erro ao inserir cliente";
            $resposta['icon'] = "error";
        }

        return $resposta;
    } else {
        return 0;
    }
}