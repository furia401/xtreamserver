<?php

session_start();

// Verifica se a sessão está iniciada e se a variável de sessão existe e tem o valor desejado
if (isset($_SESSION['nivel_admin']) && $_SESSION['nivel_admin'] == 0) {
    // Redireciona para clientes.php
    header("Location: ./clientes.php");
    exit(); // Termina o script após o redirecionamento
}

require_once("menu.php");


?>

<body>
  <style>
        /* Estilos para a área de arrastar e soltar 
       #dropArea {
            width: 100%;
            height: 100vh;
            border: 2px dashed #888;
            display: none; /* Inicialmente escondido *
            justify-content: center;
            align-items: center;
            text-align: center;
            color: #888;
            font-size: 1.5rem;
            background-color: rgba(0, 0, 0, 0.1);
            position: fixed; /* Para aparecer sobre o conteúdo *
            top: 0;
            left: 0;
            opacity: 0;
        }

        #dropArea.hover {
            border-color: #4CAF50;
            background-color: rgb(238 255 239 / 75%);
            opacity: 1 !important;
            z-index: 1;
            color: #000000;
        }

        #dropArea p {
            margin: 0;
        }*/
    </style>
  <div class="container">
    <div id="status" class="mt-3 row">
      <div class="container text-center">


      <div class="" id="controles">
        <p id="partCount"></p>
        <button class="btn btn-primary" id="openFirstModal">
          <svg aria-hidden="true" focusable="false" fill="#607d8b" width="27" height="25" viewBox="0 0 27 25">
            <path d="M5.586 9.288a.313.313 0 0 0 .282.176h4.84v3.922c0 1.514 1.25 2.24 2.792 2.24 1.54 0 2.79-.726 2.79-2.24V9.464h4.84c.122 0 .23-.068.284-.176a.304.304 0 0 0-.046-.324L13.735.106a.316.316 0 0 0-.472 0l-7.63 8.857a.302.302 0 0 0-.047.325z"></path>
            <path d="M24.3 5.093c-.218-.76-.54-1.187-1.208-1.187h-4.856l1.018 1.18h3.948l2.043 11.038h-7.193v2.728H9.114v-2.725h-7.36l2.66-11.04h3.33l1.018-1.18H3.907c-.668 0-1.06.46-1.21 1.186L0 16.456v7.062C0 24.338.676 25 1.51 25h23.98c.833 0 1.51-.663 1.51-1.482v-7.062L24.3 5.093z"></path>
          </svg>
          Abrir o uploud
        </button>
      <button id="pauseBtn" class="btn btn-warning">Pausar</button>
      <button id="resumeBtn" class="btn btn-success" disabled>Continuar</button>
    </div>
    <div class="mt-4">
     
    </div>
  </div>
          <!-- Tempo Restante -->
          <div class="col-md-4 col-lg-4 mb-4">
            <div class="border-0 card h-100 rounded-0 shadow-sm">
              <div class="card-body">
                <div class="card-innerBody align-items-center">
                  <div class="float-start justify-content-center text-black-50" style="min-width: 50px;min-height: 50px;">
                    <i class="fa-3x fa-solid fa-hourglass-half"></i>
                  </div>
                  <div class="float-end ml-auto">
                    <p class="card-label small text-muted text-end">Tempo Total Estimado:</p>
                    <h4 class="card-text text-end" id="tempo_Total_Estimado">0</h4>
                  </div>
                </div>
              </div>
              <div class="card-footer">
                <small class="float-start text-muted" style="font-size: x-small;">Tempo Decorrido:</small>
                <small class="text-black float-end ml-auto" id="tempo_Decorrido"></small>
              </div>
              <div class="card-footer">
                <small class="float-start text-muted" style="font-size: x-small;">Tempo Restante:</small>
                <small class="text-black float-end ml-auto" id="tempo_Restante"></small>
              </div>
            </div>
          </div>
          <!-- Total de Tentativas -->
          <div class="col-md-8 col-lg-8 mb-4">
            <div class="border-0 card h-100 rounded-0 shadow-sm">
              <div class="card-body">
                <div class="card-innerBody align-items-center">
                  <div class="float-start justify-content-center text-black-50" style="min-width: 50px;min-height: 50px;">
                    <i class="fa-3x fa-solid fa-clipboard-check"></i>
                  </div>
                  <div class="float-end ml-auto">
                    <p class="card-label small text-muted text-end">Envios</p>
                    <h4 class="card-text text-end" id="totalRequests">0</h4>
                  </div>
                </div>
              </div>
              <div class="align-items-center card-footer d-flex position-relative">
                <small class="float-start mb-0 me-2 text-muted" style=" font-size: 80%;">Leitura do arquivo:</small>
                <div class="flex-grow-1 progress" style=" min-width: 60%; height: 30px;">
                  <div id="progressBar" class="progress-bar" role="progressbar" style="width: 0%; color: black;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">100%</div>
                </div>
              </div>
              <div class="align-items-center card-footer d-flex position-relative">
                <small class="float-start mb-0 me-2 text-muted" style=" font-size: 80%;">Progresso da parte atual:</small>
                <div class="flex-grow-1 progress" style=" min-width: 60%; height: 30px;">
                  <div id="partProgressBar" class="progress-bar" role="progressbar" style="width: 0%; color: black;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
                </div>
              </div>
            </div>
          </div>
          <!-- Add URLs -->
          <div class="col-md-4 col-lg-4 mb-4">
            <div class="border-0 card h-100 rounded-0 shadow-sm">
              <div class="card-body">
                <div class="card-innerBody align-items-center">
                  <div class="float-start justify-content-center text-black-50" style="min-width: 50px;min-height: 50px;">
                    <i class="fa-3x fa-solid fa-link"></i>
                  </div>
                  <div class="float-end ml-auto">
                    <p class="card-label small text-muted text-end">Total de urls Enviadas</p>
                    <h4 class="card-text text-end" id="add_urls">0</h4>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- Canais Adicionando -->
          <div class="col-md-4 col-lg-4 mb-4">
            <div class="border-0 card h-100 rounded-0 shadow-sm">
              <div class="card-body">
                <div class="card-innerBody align-items-center">
                  <div class="float-start justify-content-center text-black-50" style="min-width: 50px;min-height: 50px;">
                    <i class="fa-3x fa-solid fa-tv"></i>
                  </div>
                  <div class="float-end ml-auto">
                    <p class="card-label small text-muted text-end">Canais Adicionando</p>
                    <h4 class="card-text text-end" id="canais">0</h4>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- Filmes Adicionando -->
          <div class="col-md-4 col-lg-4 mb-4">
            <div class="border-0 card h-100 rounded-0 shadow-sm">
              <div class="card-body">
                <div class="card-innerBody align-items-center">
                  <div class="float-start justify-content-center text-black-50" style="min-width: 50px;min-height: 50px;">
                    <i class="fa-3x fa-solid fa-film"></i>
                  </div>
                  <div class="float-end ml-auto">
                    <p class="card-label small text-muted text-end">Filmes Adicionando</p>
                    <h4 class="card-text text-end" id="filmes">0</h4>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- Series Adicionando -->
          <div class="col-md-4 col-lg-4 mb-4">
            <div class="border-0 card h-100 rounded-0 shadow-sm">
              <div class="card-body">
                <div class="card-innerBody align-items-center">
                  <div class="float-start justify-content-center text-black-50" style="min-width: 50px;min-height: 50px;">
                    <i class="fa-3x fa-solid fa-tv"></i>
                  </div>
                  <div class="float-end ml-auto">
                    <p class="card-label small text-muted text-end">Series Adicionando</p>
                    <h4 class="card-text text-end" id="series_adicionando">0</h4>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- Temporadas Adicionando -->
          <div class="col-md-4 col-lg-4 mb-4">
            <div class="border-0 card h-100 rounded-0 shadow-sm">
              <div class="card-body">
                <div class="card-innerBody align-items-center">
                  <div class="float-start justify-content-center text-black-50" style="min-width: 50px;min-height: 50px;">
                    <i class="fa-3x fa-solid fa-clone"></i>
                  </div>
                  <div class="float-end ml-auto">
                    <p class="card-label small text-muted text-end">Temporadas Adicionando</p>
                    <h4 class="card-text text-end" id="temporadas_adicionando">0</h4>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- Episódios Adicionando -->
          <div class="col-md-4 col-lg-4 mb-4">
            <div class="border-0 card h-100 rounded-0 shadow-sm">
              <div class="card-body">
                <div class="card-innerBody align-items-center">
                  <div class="float-start justify-content-center text-black-50" style="min-width: 50px;min-height: 50px;">
                    <i class="fa-3x fa-solid fa-tv"></i>
                  </div>
                  <div class="float-end ml-auto">
                    <p class="card-label small text-muted text-end">Episódios Adicionando</p>
                    <h4 class="card-text text-end" id="episodios_adicionando">0</h4>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- Conteúdo Existente / Duplicado -->
          <div class="col-md-4 col-lg-4 mb-4">
            <div class="border-0 card h-100 rounded-0 shadow-sm">
              <div class="card-body">
                <div class="card-innerBody align-items-center">
                  <div class="float-start justify-content-center text-black-50" style="min-width: 50px;min-height: 50px;">
                    <i class="fa-3x fa-solid fa-exclamation-triangle"></i>
                  </div>
                  <div class="float-end ml-auto">
                    <p class="card-label small text-muted text-end">Conteúdo Duplicado</p>
                    <h4 class="card-text text-end" id="exitente">0</h4>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- Erros -->
          <div class="col-md-4 col-lg-4 mb-4">
            <div class="border-0 card h-100 rounded-0 shadow-sm">
              <div class="card-body">
                <div class="card-innerBody align-items-center">
                  <div class="float-start justify-content-center text-black-50" style="min-width: 50px;min-height: 50px;">
                    <i class="fa-3x fa-solid fa-times-circle"></i>
                  </div>
                  <div class="float-end ml-auto">
                    <p class="card-label small text-muted text-end">Erros</p>
                    <h4 class="card-text text-end" id="Erro">0</h4>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- epg -->
          <div class="col-md-4 col-lg-4 mb-4">
            <div class="border-0 card h-100 rounded-0 shadow-sm">
              <div class="card-body">
                <div class="card-innerBody align-items-center">
                  <div class="float-start justify-content-center text-black-50" style="min-width: 50px;min-height: 50px;">
                    <i class="fa-3x fa-solid fa-note-sticky"></i>
                  </div>
                  <div class="float-end ml-auto">
                    <p class="card-label small text-muted text-end">EPG</p>
                    <h4 class="card-text text-end" id="epg_adicionando">0</h4>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
  </div>
</body>

    <!-- Script do Bootstrap e o seu código JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

 

  <!-- Modal de Conclusão -->
  <div class="modal fade" id="completionModal" tabindex="-1" aria-labelledby="completionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="completionModalLabel">Processamento Concluído</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          O processamento do arquivo foi concluído com sucesso.
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal 1 -->
  <div class="modal fade" id="modal_arquivo"  aria-labelledby="exampleModalToggleLabel" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content">
        <div class="align-content-center m-2 modal-body p-0 text-center" id="dropArea" style="display: none;border: 2px dashed #0d6efd;border-radius: 3px;min-height: 300px;background: #fff;z-index: 1;">
          <button type="button" class="btn p-0" data-bs-dismiss="modal" aria-label="Close" style=" position: absolute; font-size: 33px; top: -13px; right: -33px; color: white;">
            <span>×</span></button>
          <h6>Arraste e solte o arquivo M3U aqui</h6>
        </div>
        <div class="align-content-center m-2 modal-body p-5" id="dropArea2" style=" border: 1px dashed #696c6e; border-radius: 3px;min-height: 300px;">
          <button type="button" class="btn p-0" data-bs-dismiss="modal" aria-label="Close" style=" position: absolute; font-size: 33px; top: -13px; right: -33px; color: white;">
            <span>×</span>
          </button>
          <h6 class="text-center">Arraste arquivos, ou importe de</h6>
          <div class="row text-center">
            <button class="btn col-6 text-center" id="btnSelectFile">
                <input hidden type="file" class="form-control" id="m3uFile" accept=".m3u,audio/x-mpegurl" >
                <svg aria-hidden="true" focusable="false" fill="#607d8b" width="27" height="25" viewBox="0 0 27 25"><path d="M5.586 9.288a.313.313 0 0 0 .282.176h4.84v3.922c0 1.514 1.25 2.24 2.792 2.24 1.54 0 2.79-.726 2.79-2.24V9.464h4.84c.122 0 .23-.068.284-.176a.304.304 0 0 0-.046-.324L13.735.106a.316.316 0 0 0-.472 0l-7.63 8.857a.302.302 0 0 0-.047.325z"></path><path d="M24.3 5.093c-.218-.76-.54-1.187-1.208-1.187h-4.856l1.018 1.18h3.948l2.043 11.038h-7.193v2.728H9.114v-2.725h-7.36l2.66-11.04h3.33l1.018-1.18H3.907c-.668 0-1.06.46-1.21 1.186L0 16.456v7.062C0 24.338.676 25 1.51 25h23.98c.833 0 1.51-.663 1.51-1.482v-7.062L24.3 5.093z"></path></svg>
                <span>Meu Dispositivo</span>
            </button>
            <button class="btn col-6 text-center" id="openSecondModal">
                <svg aria-hidden="true" focusable="false" width="23" height="23" viewBox="0 0 23 23"><path d="M20.485 11.236l-2.748 2.737c-.184.182-.367.365-.642.547-1.007.73-2.107 1.095-3.298 1.095-1.65 0-3.298-.73-4.398-2.19-.275-.365-.183-1.003.183-1.277.367-.273 1.008-.182 1.283.183 1.191 1.642 3.482 1.915 5.13.73a.714.714 0 0 0 .367-.365l2.75-2.737c1.373-1.46 1.373-3.74-.093-5.108a3.72 3.72 0 0 0-5.13 0L12.33 6.4a.888.888 0 0 1-1.283 0 .88.88 0 0 1 0-1.277l1.558-1.55a5.38 5.38 0 0 1 7.605 0c2.29 2.006 2.382 5.564.274 7.662zm-8.979 6.294L9.95 19.081a3.72 3.72 0 0 1-5.13 0c-1.467-1.368-1.467-3.74-.093-5.108l2.75-2.737.366-.365c.824-.547 1.74-.82 2.748-.73 1.008.183 1.833.639 2.382 1.46.275.365.917.456 1.283.182.367-.273.458-.912.183-1.277-.916-1.186-2.199-1.915-3.573-2.098-1.374-.273-2.84.091-4.031 1.004l-.55.547-2.749 2.737c-2.107 2.189-2.015 5.655.092 7.753C4.727 21.453 6.101 22 7.475 22c1.374 0 2.749-.547 3.848-1.55l1.558-1.551a.88.88 0 0 0 0-1.278c-.367-.364-1.008-.456-1.375-.09z" fill="#FF814F" fill-rule="nonzero"></path></svg>
                <span>Link</span>
            </button>
          </div>
        </div>
        <h6 id="autor1" class="text-center">Feito por: 💡 FURIA</h6>
      </div>
    </div>
  </div>

  <!-- Modal 2 -->
  <div class="modal fade" id="modal_url"  aria-labelledby="exampleModalToggleLabel2" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content">
        <div class="d-flex justify-content-between modal-header text-center">
          <button class="btn btn-sm btn-primary" id="backToFirstModal">Voltar</button>
          <p class="position-absolute start-50 translate-middle" style=" top: 28px; ">Importar por Link</p>
        </div>
        <div class="align-content-center m-2 modal-body p-5" id="dropArea2" style=" border: 1px dashed #696c6e; border-radius: 3px;min-height: 300px;">
          <button type="button" class="btn p-0" data-bs-dismiss="modal" aria-label="Close" style=" position: absolute; font-size: 33px; top: -83px; right: -33px; color: white;">
            <span>×</span>
          </button>
          <div class="row text-center">
            <div class="mb-3">
              <input type="url" class="form-control" id="m3uUrl" placeholder="Digite a URL do arquivo .m3u">
            </div>
            <div class="mt-3">
              <button class="btn btn-primary" id="processFileBtn">Importar</button>
            </div>
            <p id="result"></p>
          </div>
        </div>
        <h6 id="autor2" class="text-center">Feito por: 💡 FURIA</h6>
      </div>
    </div>
  </div>

<!-- JavaScript -->
<script src="./js/uploud.js"></script>


</body>
</html>