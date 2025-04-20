<?php
session_start();
require_once('./api/controles/db.php');
require_once('./api/controles/dashboard.php');
require_once("menu.php");

$dadosAtivos = Dashboard();
$dadosTestes = testes();
$conteudos = conteudos();

?>

<style type="text/css">
  
  .scrollable-table {
  max-height: 300px; /* Defina a altura máxima desejada */
  overflow-y: auto;
}

</style>

<div class="p-2">

  <?php if ($_SESSION['nivel_admin'] == 1): ?>
  
  <div class="row">
    <h4 class="p-2 rounded-3 text-white bg-success">Informações De Conteudos </h4>
    <div class="col-md-6 col-lg-4 mb-4">
      <div class="border-0 card h-100 rounded-0 shadow-sm border-0 border-5 border-primary border-start">
       <div class="card-body">
        <div class="card-innerBody align-items-center">
         <div class="float-start justify-content-center text-black-50" style="min-width: 50px;min-height: 50px;">
          <i class="fa-3x fa-solid fa-tv text-primary"></i>
         </div>
         <div class="float-end ml-auto">
          <p class="card-label small text-end fs-4">Canais</p>
          <h4 class="card-text text-end"><?php echo $conteudos['TotalLiveStreams']; ?></h4>
         </div>
        </div>
       </div>
      </div>
    </div>
    <div class="col-md-6 col-lg-4 mb-4">
      <div class="border-0 card h-100 rounded-0 shadow-sm border-0 border-5 border-success border-start">
       <div class="card-body">
        <div class="card-innerBody align-items-center">
         <div class="float-start justify-content-center text-black-50" style="min-width: 50px;min-height: 50px;">
          <i class="fa-3x fa-solid fa-film text-success"></i>
         </div>
         <div class="float-end ml-auto">
          <p class="card-label small text-end fs-4">Filmes</p>
          <h4 class="card-text text-end"><?php echo $conteudos['TotalMovieStreams']; ?></h4>
         </div>
        </div>
       </div>
      </div>
    </div>
    <div class="col-md-6 col-lg-4 mb-4">
      <div class="border-0 card h-100 rounded-0 shadow-sm border-0 border-5 border-warning border-start">
       <div class="card-body">
        <div class="card-innerBody align-items-center">
         <div class="float-start justify-content-center text-black-50" style="min-width: 50px;min-height: 50px;">
          <i class="fa-3x fa-solid fa-clapperboard text-warning"></i>
         </div>
         <div class="float-end ml-auto">
          <p class="card-label small text-end fs-4">Series</p>
          <h4 class="card-text text-end"><?php echo $conteudos['TotalSeries']; ?></h4>
         </div>
        </div>
       </div>
      </div>
    </div>
    <div class="col-md-6 col-lg-4 mb-4">
      <div class="border-0 card h-100 rounded-0 shadow-sm border-0 border-5 border-info border-start">
       <div class="card-body">
        <div class="card-innerBody align-items-center">
         <div class="float-start justify-content-center text-black-50" style="min-width: 50px;min-height: 50px;">
          <i class="fa-3x fa-solid fa-photo-film text-info"></i>
         </div>
         <div class="float-end ml-auto">
          <p class="card-label small text-end fs-4">Episodios</p>
          <h4 class="card-text text-end"><?php echo $conteudos['TotalEpisodes']; ?></h4>
         </div>
        </div>
       </div>
      </div>
    </div>
  </div>
  <?php endif ?>
  
  <div class="row">
    <h4 class="p-2 rounded-3 text-white bg-primary">Informações Finançeiras</h4>
   <div class="col-12 mb-4 col-lg-6">
    <div class="border-0 card h-100 rounded-0 shadow-sm">
     <div class="card-body">
      <div class="card-innerBody align-items-center">
       <div class="float-start justify-content-center text-black-50" style="min-width: 50px;min-height: 50px;">
        <i class="fa-eye fa-solid fa-3x"></i>
       </div>
       <div class="ml-auto">
        <p class="small text-center text-muted w-100">Usuários Online</p>
        <h4 class="card-text j_content_online_users text-center">Desativado...</h4>
       </div>
      </div>
     </div>
     <div class="listaClientesOnline" style="max-height: 90px; overflow: auto;zoom: 90%;"></div>
    </div>
   </div>
   <div class="col-12 mb-4 col-lg-6">
    <div class="border-0 card h-100 rounded-0 shadow-sm">
     <div class="card-body">
      <div class="card-innerBody align-items-center">
       <div class="float-start justify-content-center text-black-50" style="min-width: 50px;min-height: 50px;">
        <i class="fa-eye fa-solid fa-3x"></i>
       </div>
       <div class="ml-auto">
        <p class="small text-center text-muted w-100">Testes Online</p>
        <h4 class="card-text j_content_online_users text-center">Desativado...</h4>
       </div>
      </div>
     </div>
     <div class="listaClientesOnline" style="max-height: 90px; overflow: auto;zoom: 90%;"></div>
    </div>
   </div>
   <div class="col-md-4 col-lg-4 mb-4">
    <div class="border-0 card h-100 rounded-0 shadow-sm">
     <div class="card-body">
      <div class="card-innerBody align-items-center">
       <div class="float-start justify-content-center text-black-50" style="min-width: 50px;min-height: 50px;">
        <i class="fa-3x fa-solid fa-users"></i>
       </div>
       <div class="float-end ml-auto">
        <p class="card-label small text-muted text-end">Total de Usuários</p>
        <h4 class="card-text text-end"><?php echo $dadosAtivos['Totaldeclientes']; ?></h4>
       </div>
      </div>
     </div>
     <div class="card-footer">
      <small class="float-start text-muted" style="font-size: x-small;">Total</small>
      <small class="text-black float-end ml-auto">
       <i class="fa fa-caret-up"></i><i class="fa fa-caret-down"></i>R$: <?php echo $dadosAtivos['Totaldeclientes_valor']; ?></small>
     </div>
     <div class="card-footer">
      <small class="float-start text-muted" style="font-size: x-small;">P/unitário:</small>
      <small class="text-black float-end ml-auto">R$: <?php echo $dadosAtivos['Totaldeclientes_valor_unidade']; ?></small>
     </div>
    </div>
   </div>
   <div class="col-md-4 col-lg-4 mb-4">
    <div class="border-0 card h-100 rounded-0 shadow-sm">
     <div class="card-body">
      <div class="card-innerBody align-items-center">
       <div class="float-start justify-content-center text-black-50" style="min-width: 50px;min-height: 50px;">
        <i class="fa-3x fa-solid fa-users"></i>
       </div>
       <div class="float-end ml-auto">
        <p class="card-label small text-muted text-end">Clientes ATIVOS</p>
        <h4 class="card-text text-end"><?php echo $dadosAtivos['clientesAtivos']; ?></h4>
       </div>
      </div>
     </div>
     <div class="card-footer">
      <small class="float-start text-muted" style=" font-size: x-small;">Total</small>
      <small class="text-success float-end ml-auto">
       <i aria-hidden="true" class="fa fa-caret-up"></i>R$: <?php echo $dadosAtivos['clientesAtivos_valor']; ?></small>
     </div>
     <div class="card-footer">
      <small class="float-start text-muted" style=" font-size: x-small;">P/unitário:</small>
      <small class="text-success float-end ml-auto">R$: <?php echo $dadosAtivos['clientesAtivos_valor_unidade']; ?></small>
     </div>
    </div>
   </div>
   <div class="col-md-4 col-lg-4 mb-4">
    <div class="border-0 card h-100 rounded-0 shadow-sm">
     <div class="card-body">
      <div class="card-innerBody align-items-center">
       <div class="float-start justify-content-center text-black-50" style="min-width: 50px;min-height: 50px;">
        <i class="fa-3x fa-solid fa-users-slash"></i>
       </div>
       <div class="float-end ml-auto">
        <p class="card-label small text-muted text-end">Total Vencidos</p>
        <h4 class="card-text text-end"><?php echo $dadosAtivos['clientesvencidostotal']; ?></h4>
       </div>
      </div>
     </div>
     <div class="card-footer">
      <small class="float-start text-muted" style="font-size: x-small;">Total</small>
      <small class="text-danger float-end ml-auto">
       <i aria-hidden="true" class="fa fa-caret-down"></i>R$: <?php echo $dadosAtivos['clientesvencidostotal_valor']; ?></small>
     </div>
     <div class="card-footer">
      <small class="float-start text-muted" style="font-size: x-small;">P/unitário:</small>
      <small class="text-danger float-end ml-auto">R$: <?php echo $dadosAtivos['clientesvencidostotal_valor_unidade']; ?></small>
     </div>
    </div>
   </div>
   <div class="col-md-4 col-lg-4 mb-4">
    <div class="border-0 card h-100 rounded-0 shadow-sm">
     <div class="card-body">
      <div class="card-innerBody align-items-center">
       <div class="float-start justify-content-center text-black-50" style="min-width: 50px;min-height: 50px;">
        <i class="fa-3x fa-solid fa-user-check"></i>
       </div>
       <div class="float-end ml-auto">
        <p class="card-label small text-muted text-end">Renovados</p>
        <h4 class="card-text text-end"><?php echo $dadosAtivos['clientesrenovados']; ?></h4>
       </div>
      </div>
     </div>
     <div class="card-footer">
      <small class="float-start text-muted" style=" font-size: x-small;">Total</small>
      <small class="text-success float-end ml-auto">
       <i aria-hidden="true" class="fa fa-caret-up"></i>R$: <?php echo $dadosAtivos['clientesrenovados_valor']; ?></small>
     </div>
     <div class="card-footer">
      <small class="float-start text-muted" style=" font-size: x-small;">P/unitário:</small>
      <small class="text-success float-end ml-auto">R$: <?php echo $dadosAtivos['clientesrenovados_valor_unidade']; ?></small>
     </div>
    </div>
   </div>
   <div class="col-md-4 col-lg-4 mb-4">
    <div class="border-0 card h-100 rounded-0 shadow-sm">
     <div class="card-body">
      <div class="card-innerBody align-items-center">
       <div class="float-start justify-content-center text-black-50" style="min-width: 50px;min-height: 50px;">
        <i class="fa-3x fa-solid fa-user-clock"></i>
       </div>
       <div class="float-end ml-auto">
        <p class="card-label small text-muted text-end">Total a renovar</p>
        <h4 class="card-text text-end"><?php echo $dadosAtivos['clientesarenovar']; ?></h4>
       </div>
      </div>
     </div>
     <div class="card-footer">
      <small class="float-start text-muted" style=" font-size: x-small;">Total</small>
      <small class="text-success float-end ml-auto">
       <i aria-hidden="true" class="fa fa-caret-up"></i>R$: <?php echo $dadosAtivos['clientesarenovar_valor']; ?></small>
     </div>
     <div class="card-footer">
      <small class="float-start text-muted" style=" font-size: x-small;">P/unitário:</small>
      <small class="text-success float-end ml-auto">R$: <?php echo $dadosAtivos['clientesarenovar_valor_unidade']; ?></small>
     </div>
    </div>
   </div>
   <div class="col-md-4 col-lg-4 mb-4">
    <div class="border-0 card h-100 rounded-0 shadow-sm">
     <div class="card-body">
      <div class="card-innerBody align-items-center">
       <div class="float-start justify-content-center text-black-50" style="min-width: 50px;min-height: 50px;">
        <i class="fa-3x fa-solid fa-user-plus"></i>
       </div>
       <div class="float-end ml-auto">
        <p class="card-label small text-muted text-end">Novos Usuários</p>
        <h4 class="card-text text-end"><?php echo $dadosAtivos['clientesnovos']; ?></h4>
       </div>
      </div>
     </div>
     <div class="card-footer">
      <small class="float-start text-muted" style=" font-size: x-small;">Total</small>
      <small class="text-success float-end ml-auto">
       <i aria-hidden="true" class="fa fa-caret-up"></i>R$: <?php echo $dadosAtivos['clientesnovos_valor']; ?></small>
     </div>
     <div class="card-footer">
      <small class="float-start text-muted" style=" font-size: x-small;">P/unitário:</small>
      <small class="text-success float-end ml-auto">R$: <?php echo $dadosAtivos['clientesnovos_valor_unidade']; ?></small>
     </div>
    </div>
   </div>
   <div class="col-md-4 col-lg-4 mb-4">
    <div class="border-0 card h-100 rounded-0 shadow-sm">
     <div class="card-body">
      <div class="card-innerBody align-items-center">
       <div class="float-start justify-content-center text-black-50" style="min-width: 50px;min-height: 50px;">
        <i class="fa-3x fa-solid fa-user-xmark"></i>
       </div>
       <div class="float-end ml-auto">
        <p class="card-label small text-muted text-end">Vencidos este mes</p>
        <h4 class="card-text text-end"><?php echo $dadosAtivos['clientesvencidos_este_mes']; ?></h4>
       </div>
      </div>
     </div>
     <div class="card-footer">
      <small class="float-start text-muted" style="font-size: x-small;">Total</small>
      <small class="text-danger float-end ml-auto">
       <i aria-hidden="true" class="fa fa-caret-down"></i>R$: <?php echo $dadosAtivos['clientesvencidos_este_mes_valor']; ?></small>
     </div>
     <div class="card-footer">
      <small class="float-start text-muted" style="font-size: x-small;">P/unitário:</small>
      <small class="text-danger float-end ml-auto">R$: <?php echo $dadosAtivos['clientesvencidos_este_mes_valor_unidade']; ?></small>
     </div>
    </div>
   </div>
   <div class="col-md-4 col-lg-4 mb-4">
    <div class="border-0 card h-100 rounded-0 shadow-sm">
     <div class="card-body">
      <div class="card-innerBody align-items-center">
       <div class="float-start justify-content-center text-black-50" style="min-width: 50px;min-height: 50px;">
        <i class="fa-3x fa-solid fa-users"></i>
       </div>
       <div class="float-end ml-auto">
        <p class="card-label small text-muted text-end">Total de Testes</p>
        <h4 class="card-text text-end"><?php echo $dadosTestes['Totaldetestes']; ?></h4>
       </div>
      </div>
     </div>
     <div class="card-footer">
      <small class="float-start text-muted" style="font-size: x-small;">Total</small>
      <small class="text-black float-end ml-auto">
       <i class="fa fa-caret-up"></i><i class="fa fa-caret-down"></i> R$: <?php echo $dadosTestes['Totaldetestes_valor']; ?></small>
     </div>
     <div class="card-footer">
      <small class="float-start text-muted" style="font-size: x-small;">P/unitário:</small>
      <small class="text-black float-end ml-auto">R$: <?php echo $dadosTestes['Totaldetestes_valor_unidade']; ?></small>
     </div>
    </div>
   </div>
   <div class="col-md-4 col-lg-4 mb-4">
    <div class="border-0 card h-100 rounded-0 shadow-sm">
     <div class="card-body">
      <div class="card-innerBody align-items-center">
       <div class="float-start justify-content-center text-black-50" style="min-width: 50px;min-height: 50px;">
        <i class="fa-3x fa-solid fa-users"></i>
       </div>
       <div class="float-end ml-auto">
        <p class="card-label small text-muted text-end">Testes ATIVOS</p>
        <h4 class="card-text text-end"><?php echo $dadosTestes['TestesAtivos']; ?></h4>
       </div>
      </div>
     </div>
     <div class="card-footer">
      <small class="float-start text-muted" style=" font-size: x-small;">Total</small>
      <small class="text-success float-end ml-auto">
       <i aria-hidden="true" class="fa fa-caret-up"></i>R$: <?php echo $dadosTestes['TestesAtivos_valor']; ?></small>
     </div>
     <div class="card-footer">
      <small class="float-start text-muted" style=" font-size: x-small;">P/unitário:</small>
      <small class="text-success float-end ml-auto">R$: <?php echo $dadosTestes['TestesAtivos_valor_unidade']; ?></small>
     </div>
    </div>
   </div>
   <div class="col-md-4 col-lg-4 mb-4">
    <div class="border-0 card h-100 rounded-0 shadow-sm">
     <div class="card-body">
      <div class="card-innerBody align-items-center">
       <div class="float-start justify-content-center text-black-50" style="min-width: 50px;min-height: 50px;">
        <i class="fa-3x fa-solid fa-users-slash"></i>
       </div>
       <div class="float-end ml-auto">
        <p class="card-label small text-muted text-end">Testes Vencidos</p>
        <h4 class="card-text text-end"><?php echo $dadosTestes['Testesvencidostotal']; ?></h4>
       </div>
      </div>
     </div>
     <div class="card-footer">
      <small class="float-start text-muted" style="font-size: x-small;">Total</small>
      <small class="text-danger float-end ml-auto">
       <i aria-hidden="true" class="fa fa-caret-down"></i>R$: <?php echo $dadosTestes['Testesvencidostotal_valor']; ?></small>
     </div>
     <div class="card-footer">
      <small class="float-start text-muted" style="font-size: x-small;">P/unitário:</small>
      <small class="text-danger float-end ml-auto">R$: <?php echo $dadosTestes['Testesvencidostotal_valor_unidade']; ?></small>
     </div>
    </div>
   </div>
 </div>
 </div>
  <!-------------hashboard2----------->
  <div class="row">
    <div class="align-items-stretch col-lg-6 mb-4">
      <div class="card h-100 border-0 rounded-0 ">
          <div class="card-title d-flex mb-1 p-3">
            <h5>Vencimento hoje</h5>
          </div>
          <div class="card-body"><div class="overflow-auto" style="max-height: 13em; min-height: 13em;">
                <table class="table scrollable-table table-responsive">
                  <thead>
                    <tr>
                      <th class="acao text-center" style="width: 22%;"> Ações</th>
                      <th style="width: 10%;min-width: 67px;">ID-C:</th>
                      <th class="number">Usuario:</th>
                      <th style="width:20%;">Vencimento:</th>
                      <th style="width: 20%;min-width: 90px;" class="text-center">Lucro:</th>
                      <th style="width: 20%;min-width: 90px;" class="text-center">Valor:</th>
                    </tr>
                  </thead>
                  <tbody class="no-border-x">
                    <?php if (empty($dadosAtivos['clientesvencidos_hoje_lista'])){ ?>
                        <tr>
                            <td colspan="5" class="text-center">Não há clientes Vencidos hoje para exibir.</td>
                        </tr>
                    <?php }else{ ?>
                    <?php foreach ($dadosAtivos['clientesvencidos_hoje_lista'] as $cliente) { ?>
                      <tr>
                        <!-- Tabela de clientesvencidos_este_mes -->                  
                        <td class="acao">
                          <button type="button" class="btn btn-outline-primary fw-bold p-1" style="font-size: 12px; width: 81px;" data-toggle="modal" data-placement="top" title="Renovar" onclick='modal_master("api/clientes.php", "renovar_cliente", "<?php echo $cliente['id']; ?>", "usuario", "<?php echo $cliente['usuario']; ?>")'><i class="btn-sm fa-retweet fas"></i> Renovar </button>
                        </td>
                        <td><?php echo $cliente['id']; ?></td>
                        <td class="number"><?php echo $cliente['usuario']; ?></td>
                        <td class="text-center"><?php echo $cliente['data']; ?></td>
                        <td class="text-center" style="width:20%;"><?php echo "R$: ". $cliente['lucro']; ?></td>
                        <td class="text-center" style="width:20%;"><?php echo "R$: ". $cliente['total']; ?></td>
                        
                      </tr>
                    <?php }} ?>
                  </tbody>
                </table>
            </div>
            <table>
                <thead>
                  <tr>
                    <th style="width: 10%;max-width: 60px;">TOTAL:</th>
                    <th style="width: 100%;"> </th>
                    <th style="width:20%;min-width: 140px;">Lucro: R$: <?php echo $dadosAtivos['clientesvencidos_hoje_lucro']; ?></th>
                    <th style="width:20%;min-width: 110px;">Faturamento: R$: <?php echo $dadosAtivos['clientesvencidos_valor_total']; ?></th>
                  </tr>
                </thead>
            </table>
          </div>
      </div>
    </div>
    <div class="align-items-stretch col-lg-6 mb-4">
      <div class="card h-100 border-0 rounded-0">
          <div class="card-title d-flex mb-1 p-3">
            <h5>Vencimento Amanha</h5>
          </div>
          <div class="card-body"><div class="overflow-auto" style="max-height: 13em; min-height: 13em;">
                <table class="table scrollable-table table-responsive">
                  <thead>
                    <tr>
                      <th class="acao text-center" style="width: 22%;"> Ações</th>
                      <th style="width: 10%;min-width: 67px;">ID-C:</th>
                      <th class="number">Usuario:</th>
                      <th style="width:20%;">Vencimento:</th>
                      <th style="width: 20%;min-width: 90px;" class="text-center">Lucro:</th>
                      <th style="width: 20%;min-width: 90px;" class="text-center">Valor:</th>
                    </tr>
                  </thead>
                  <tbody class="no-border-x">
                    <?php if (empty($dadosAtivos['clientesvencidos_amanha_lista'])){ ?>
                      <tr>
                          <td colspan="5" class="text-center">Não há clientes com vencimento amanha para exibir.</td>
                      </tr>
                  <?php }else{ ?>
                    <?php foreach ($dadosAtivos['clientesvencidos_amanha_lista'] as $cliente) { ?>
                      <tr>
                        <!-- Tabela de clientesvencidos_este_mes -->                  
                        <td class="acao">
                          <button type="button" class="btn btn-outline-primary fw-bold p-1" style="font-size: 12px; width: 81px;" data-toggle="modal" data-placement="top" title="Renovar" onclick='modal_master("api/clientes.php", "renovar_cliente", "<?php echo $cliente['id']; ?>", "usuario", "<?php echo $cliente['usuario']; ?>")'><i class="btn-sm fa-retweet fas"></i> Renovar </button>
                        </td>
                        <td><?php echo $cliente['id']; ?></td>
                        <td class="number"><?php echo $cliente['usuario']; ?></td>
                        <td class="text-center"><?php echo $cliente['data']; ?></td>
                        <td class="text-center" style="width:20%;"><?php echo "R$: ". $cliente['lucro']; ?></td>
                        <td class="text-center" style="width:20%;"><?php echo "R$: ". $cliente['total']; ?></td>
                        
                      </tr>
                    <?php }} ?>
                  </tbody>
                </table>
            </div>
            <table>
                <thead>
                  <tr>
                    <th style="width: 10%;max-width: 60px;">TOTAL:</th>
                    <th style="width: 100%;"> </th>
                    <th style="width:20%;min-width: 140px;">Lucro: R$: <?php echo $dadosAtivos['clientesvencidos_amanha_lucro']; ?></th>
                    <th style="width:20%;min-width: 110px;">Faturamento: R$: <?php echo $dadosAtivos['clientesvencidos_amanha_valor_total']; ?></th>
                  </tr>
                </thead>
            </table>
          </div>
      </div>
    </div>
    <div class="align-items-stretch col-lg-6 mb-4">
      <div class="card h-100 border-0 rounded-0">
          <div class="card-title d-flex mb-1 p-3">
            <h5>Proximos 7 Dias</h5>
          </div>
          <div class="card-body"><div class="overflow-auto" style="max-height: 13em; min-height: 13em;">
                <table class="table scrollable-table table-responsive">
                  <thead>
                    <tr>
                      <th class="acao text-center" style="width: 22%;"> Ações</th>
                      <th style="width: 10%;min-width: 67px;">ID-C:</th>
                      <th class="number">Usuario:</th>
                      <th style="width:20%;">Vencimento:</th>
                      <th style="width: 20%;min-width: 90px;" class="text-center">Lucro:</th>
                      <th style="width: 20%;min-width: 90px;" class="text-center">Valor:</th>
                    </tr>
                  </thead>
                  <tbody class="no-border-x">
                    <?php if (empty($dadosAtivos['clientesvencidos_proximos'])){ ?>
                      <tr>
                          <td colspan="5" class="text-center">Não há clientes com vencimento nos proximo 7 dias para exibir.</td>
                      </tr>
                  <?php }else{ ?>
                    <?php foreach ($dadosAtivos['clientesvencidos_proximos'] as $cliente) { ?>
                      <tr>
                        <!-- Tabela de clientesvencidos_este_mes -->                  
                        <td class="acao">
                          <button type="button" class="btn btn-outline-primary fw-bold p-1" style="font-size: 12px; width: 81px;" data-toggle="modal" data-placement="top" title="Renovar" onclick='modal_master("api/clientes.php", "renovar_cliente", "<?php echo $cliente['id']; ?>", "usuario", "<?php echo $cliente['usuario']; ?>")'><i class="btn-sm fa-retweet fas"></i> Renovar </button>
                        </td>
                        <td><?php echo $cliente['id']; ?></td>
                        <td class="number"><?php echo $cliente['usuario']; ?></td>
                        <td class="text-center"><?php echo $cliente['data']; ?></td>
                        <td class="text-center" style="width:20%;"><?php echo "R$: ". $cliente['lucro']; ?></td>
                        <td class="text-center" style="width:20%;"><?php echo "R$: ". $cliente['total']; ?></td>
                        
                      </tr>
                    <?php }} ?>
                  </tbody>
                </table>
            </div>
            <table>
                <thead>
                  <tr>
                    <th style="width: 10%;max-width: 60px;">TOTAL:</th>
                    <th style="width: 100%;"> </th>
                    <th style="width:20%;min-width: 140px;">Lucro: R$: <?php echo $dadosAtivos['clientesvencidos_proximos_lucro']; ?></th>
                    <th style="width:20%;min-width: 110px;">Faturamento: R$: <?php echo $dadosAtivos['clientesvencidos_proximos_valor_total']; ?></th>
                  </tr>
                </thead>
            </table>
          </div>
      </div>
    </div>
    <div class="align-items-stretch col-lg-6 mb-4">
      <div class="card h-100 border-0 rounded-0">
          <div class="card-title d-flex mb-1 p-3">
            <h5 class="text-danger">Vencidos Este Mes</h5>
          </div>
          <div class="card-body"><div class="overflow-auto" style="max-height: 13em; min-height: 13em;">
                <table class="table scrollable-table table-responsive">
                  <thead>
                    <tr>
                      <th class="acao text-center" style="width: 22%;"> Ações</th>
                      <th style="width: 10%;min-width: 67px;">ID-C:</th>
                      <th class="number">Usuario:</th>
                      <th style="width:20%;">Vencimento:</th>
                      <th style="width: 20%;min-width: 90px;" class="text-center">Lucro:</th>
                      <th style="width: 20%;min-width: 90px;" class="text-center">Valor:</th>
                    </tr>
                  </thead>
                  <tbody class="no-border-x">
                    <?php if (empty($dadosAtivos['clientesvencidos_este_mes_lista'])){ ?>
                      <tr>
                          <td colspan="5" class="text-center">Não há clientes vencidos este mes para exibir.</td>
                      </tr>
                  <?php }else{ ?>
                    <?php foreach ($dadosAtivos['clientesvencidos_este_mes_lista'] as $cliente) { ?>
                      <tr>
                        <!-- Tabela de clientesvencidos_este_mes -->                  
                        <td class="acao">
                          <button type="button" class="btn btn-outline-primary fw-bold p-1" style="font-size: 12px; width: 81px;" data-toggle="modal" data-placement="top" title="Renovar" onclick='modal_master("api/clientes.php", "renovar_cliente", "<?php echo $cliente['id']; ?>", "usuario", "<?php echo $cliente['usuario']; ?>")'><i class="btn-sm fa-retweet fas"></i> Renovar </button>
                        </td>
                        <td><?php echo $cliente['id']; ?></td>
                        <td class="number"><?php echo $cliente['usuario']; ?></td>
                        <td class="text-center"><?php echo $cliente['data']; ?></td>
                        <td class="text-center" style="width:20%;"><?php echo "R$: ". $cliente['lucro']; ?></td>
                        <td class="text-center" style="width:20%;"><?php echo "R$: ". $cliente['total']; ?></td>
                        
                      </tr>
                    <?php }} ?>
                  </tbody>
                </table>
            </div>
          </div>
      </div>
    </div>
    <div class="align-items-stretch col-lg-6 mb-4">
      <div class="card h-100 border-0 rounded-0">
          <div class="card-title d-flex mb-1 p-3">
            <h5 class="">Renovados Este Mes</h5>
          </div>
          <div class="card-body"><div class="overflow-auto" style="max-height: 13em; min-height: 13em;">
                <table class="table scrollable-table table-responsive">
                  <thead>
                    <tr>
                      <th style="width: 10%;min-width: 67px;">ID-C:</th>
                      <th class="number">Usuario:</th>
                      <th style="width:20%;">Vencimento:</th>
                      <th style="width: 20%;min-width: 90px;" class="text-center">Lucro:</th>
                      <th style="width: 20%;min-width: 90px;" class="text-center">Valor:</th>
                    </tr>
                  </thead>
                  <tbody class="no-border-x">
                    <?php if (empty($dadosAtivos['clientesrenovados_lista'])){ ?>
                      <tr>
                          <td colspan="5" class="text-center">Não há clientes renovados este mes para exibir.</td>
                      </tr>
                  <?php }else{ ?>
                    <?php foreach ($dadosAtivos['clientesrenovados_lista'] as $cliente) { ?>
                      <tr>
                        <!-- Tabela de clientesvencidos_este_mes -->                  
                        
                        <td class="text-center"><?php echo $cliente['id']; ?></td>
                        <td class="number "><?php echo $cliente['usuario']; ?></td>
                        <td class="text-center"><?php echo $cliente['data']; ?></td>
                        <td class="text-center" style="width:20%;"><?php echo "R$: ". $cliente['lucro']; ?></td>
                        <td class="text-center" style="width:20%;"><?php echo "R$: ". $cliente['total']; ?></td>
                        
                      </tr>
                    <?php }} ?>
                  </tbody>
                </table>
            </div>
            <table>
              <thead>
                <tr>
                  <th style="width: 10%;max-width: 60px;">TOTAL:</th>
                  <th style="width: 100%;"> </th>
                  <th style="width:20%;min-width: 140px;">Lucro: R$: <?php echo $dadosAtivos['clientesrenovados_lista_valor']; ?></th>
                  <th style="width:20%;min-width: 110px;">Faturamento: R$: <?php echo $dadosAtivos['clientesrenovados_lista_valor_total']; ?></th>
                </tr>
              </thead>
          </table>
          </div>
      </div>
    </div>
  </div>
</div>

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
<app-footer><hr class="border-1 border-secondary border-top mb-4 mt-4">
<div class="container-fluid"><div class="row pt-4"><div class="col-md-12 text-center mb-2"><small> © <?php echo date("Y"); ?> Feito Com <i aria-hidden="true" class="fa fa-heart text-danger"></i> por <span class="text-secondary font-weight-bold">Furia</span></small></div><div class="col-md-12 text-center mb-3"><a class="bg-light btn-sm m-2 shadow-sm text-dark" target="_blank"><i class="fa-telegram-plane fab"></i></a></div></div></div></app-footer>

