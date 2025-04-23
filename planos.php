<?php
session_start();
require_once('./api/controles/db.php');
require_once("menu.php");

$conexao = conectar_bd();

$sql = "SELECT * FROM planos Where admin_id = ".$_SESSION['admin_id']."";
$result = $conexao->query($sql);

if ($result->columnCount() > 0) {
    // mostrar as categorias existentes
    echo "<app-tables>
    <div>
        <h4 class='align-items-center d-flex justify-content-between mb-4 text-muted text-uppercase'> <button type=\"button\" class=\"btn btn-outline-success fa-plus fas\" onclick=\"modal_master('api/planos.php', 'adicionar_planos', 'add')\"></button></h4>
        <div class='row mb-4'>
            <div class='col-md-12'>
                <div class='border-0 card py-2 rounded-0'>
                <div class='card-title mb-1 p-3 d-flex'>
                    <h5>Todos os Planos</h5>
                    </div>
                    <div class='card-body overflow-auto py-0'>
                        <div class='table-responsive-md'>
                            <table class='table table-hover table-striped'>
                                <thead>
                                    <tr>
                                        <th>Nome</th>
                                        <th class='text-center'>Valor Padrao</th>
                                        <th>Total Clientes</th>
                                        <th>VT.Lucro</th>
                                        <th>VT.Custo</th>
                                        <th style='min-width: 140px;' class='text-center'>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>";
                                $valor_creditos2 = "0";
                                $V_Lucro2 = "0";
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $id = $row["id"];

        $data = date('Y-m-d');


        $sql_count = "SELECT COUNT(*) AS total FROM clientes WHERE plano ='$id' AND Vencimento >= '$data' and is_trial = 0";
        $result_count = $conexao->query($sql_count);
        $row_count = $result_count->fetch(PDO::FETCH_ASSOC);
        $total = $row_count["total"];

        //contas
        $custo_por_credito = isset($row["custo_por_credito"]) ? (float)$row["custo_por_credito"] : 0;
        $valor_creditos = $custo_por_credito * $total;
        $valor_plano = isset($row["valor"]) ? (float)$row["valor"] : 0;
        $V_Lucro = $valor_plano * $total - $valor_creditos;
        $valor_creditos2 += $valor_creditos;
        $V_Lucro2 += $V_Lucro;

        echo "<tr>";
        echo "<td>".$row["nome"]."</td>
        <td class='text-center'>".$row["valor"]."</td>";

        echo "<td>".$total."</td>";
        echo "<td>R$:".number_format($V_Lucro, 2, ',', '.')."</td>";
        echo "<td>R$:".$valor_creditos."</td>";

        echo '<td class="text-center" style="min-width: 160px;">
        
        <a class="btn btn-sm btn-outline-lightning rounded-0 mr-2"  data-placement="top" title="editar" onclick=\'modal_master("api/planos.php", "edite_planos", "' . $id . '")\'>
        <i class="fa fa-edit"></i>
        <a class="btn btn-sm btn-outline-lightning rounded-0 mr-2" data-placement="top" title="Apagar" onclick=\'modal_master("api/planos.php", "delete_plano", "' . $id . '", "nome", "' . $row["nome"] . '")\'>
        <i class="fa fa-trash"></i>
        </a>
        </td>';
        echo "</tr>";

    }
    echo "</tbody>
    <thead>
                                    <tr>
                                        <th>TOTAL</th>
                                        <th class='text-center'></th>
                                        <th class='text-center'></th>
                                        <th></th>
                                        <th>R$:".$V_Lucro2."</th>
                                        <th>R$:".$valor_creditos2."</th>
                                        <th style='min-width: 140px;' class='text-center'></th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</app-tables>";

}
?>

<script src="./js/sweetalert2.js"></script>
<script src="./js/custom.js"></script>
<!-- Modal master -->
<div class="modal fade" id="modal_master" tabindex="-1" aria-labelledby="modal_master" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="d-block modal-header" id="modal_master-header">
        <h5 class="float-start modal-title" id="modal_master-titulo"></h5>
        <button type="button" class="fa btn text-white fa-close fs-6 float-end" data-bs-dismiss="modal" aria-label="Close"></button>
        </button>
      </div>
      <form id="modal_master_form" onsubmit="event.preventDefault();" autocomplete="off">
        <div id="modal_master-body" class="modal-body overflow-auto" style="max-height: 421px;"></div>
        <div id="modal_master-footer" class="modal-footer"></div>
      </form>
    </div>
  </div>
</div>
<!-- Modal master Fim-->