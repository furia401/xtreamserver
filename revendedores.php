<?php

session_start();

// Verifica se a sessão está iniciada e se a variável de sessão existe e tem o valor desejado
if (isset($_SESSION['plano_admin']) && $_SESSION['plano_admin'] == 1) {
    // Redireciona para clientes.php
    header("Location: ./clientes.php");
    exit(); // Termina o script após o redirecionamento
}

require_once("menu.php");


?>

<h4 class="align-items-center d-flex justify-content-between mb-4 text-muted text-uppercase">
  LISTAR REVENDEDORES
  <button type="button" class="btn btn-outline-success fa-plus fas" onclick='modal_master("api/revendedores.php", "add_revendedor", "add")'></button>

</h4>
<table id="data_table" class="display overflow-auto table" style="width: 100%;">
  <thead class="table-dark">
    <tr><!--<th></th> descomentar para usar childs -->
      <th style="min-width: 75px;">#</th>
      <th>Usuario</th>
      <th>creditos</th>
      <th>tipo</th>
      <th>Total_Revendedores</th>
      <th>Total_Clientes</th>
      <th>Açoes</th>
    </tr>
  </thead>
</table>


<script src="//cdn.datatables.net/2.0.7/js/dataTables.js"></script>
<script src="./js/sweetalert2.js"></script>
<script src="./js/revendedores.js?sfd"></script>
<script src="./js/custom.js"></script>

</div>
</main>
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

<script>
function copyText(elementId) {
  var preElement = document.getElementById(elementId);
  var range = document.createRange();
  range.selectNodeContents(preElement);
  var selection = window.getSelection();
  selection.removeAllRanges();
  selection.addRange(range);
  document.execCommand('copy');
  selection.removeAllRanges();
  //alert('Texto copiado para a área de transferência!');
  SweetAlert3('Texto copiado para a área de transferência!', 'success');
}
</script>

</body>
</html>
