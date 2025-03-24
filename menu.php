<?php 
require_once('./api/controles/checkLogout.php');

checkLogout();

if (isset($_GET['sair'])) {
  // Se a sessão expirou, destrói a sessão e redireciona para a página de login
  $_SESSION = array();
  session_unset();
  session_destroy();
        
  // Remove o cookie da sessão
  if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
  }
  header('Location: ./index.php');
  exit();

}

?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- App favicon -->
    <link rel="shortcut icon" href="./img/icon.png">
    <link rel="stylesheet" type="text/css" href="css/menu.css">
    <link rel="stylesheet" type="text/css" href="//cdn.datatables.net/2.0.7/css/dataTables.dataTables.css">
    <!-- jQuery - Biblioteca necessária para o Bootstrap -->
    <script src="//ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>
    <!-- Bootstrap CSS -->
    <link href="//cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <!-- Popper.js (necessary for Bootstrap's JavaScript plugins) -->
    <script src="//cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
    
    <!-- Bootstrap JS -->
    <script src="//cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  </head>
  <body>
    <header class="fixed-top navbar navbar-dark navbar-expand-lg" style="transition: 0.5s; padding: 0; background: #1B1E26;">
      <div class="container-fluid" style="height: 3em;display: flex;align-items: center;box-shadow: #282a2d 0px 0px 5px 3px;">
        <!-- Left side content -->
        <div class="align-items-center d-flex left-side-content">
          <div class="pl-2 pr-2">
            <div class="m-0 navbar-brand w-100" style="display: flex; align-items: flex-end;">
              <img alt="lightning logo" src="./img/logo_tranparente2.png" width="35px" height="35px" class="logo">
              <span class="ml-1 text-logo">XTREAM SERVER OPENSOURCE</span>
            </div>
          </div>
          <div class="col-md-auto menuToggle btn1" style="width: 40px;height: 40px;">
            <i class="fa fa-bars"></i>
          </div>
        </div>
        <!-- Right side content -->
        <div class="align-items-center d-flex right-side-content">
          <div class="badge bg-success">
            <span class="j_credits" style="margin-right: 5px; opacity: 1;" id="creditos"> </span> Créditos </div>
          <div class="dropdown ms-2">
            <button class="btn header-item waves-effect dropdown-toggle" type="button" id="dropdownUser" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="color: #EEF5FD;">
              <img class="rounded-circle header-profile-user" src="//fxtream.xyz/dashboard/img/user.png" alt="<?php echo $_SESSION['username']; ?>" style="width: 25px; height: 23px;">
              <span class="d-none d-xl-inline-block ms-1"> <?php echo  $_SESSION['username']; ?> </span>
              <i class="mdi mdi-chevron-down d-none d-xl-inline-block"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownUser">
              <li>
                <?php if ($_SESSION['nivel_admin'] == 1): ?>
                  <a class="dropdown-item" onclick='modal_master("api/revendedores.php", "edite_admin", "edite")'>Editar Admin</a>
                <?php endif ?>
                <?php if ($_SESSION['nivel_admin'] == 0): ?>
                  <a class="dropdown-item" onclick='modal_master("api/revendedores.php", "edite_admin_revenda", "edite")'>Editar Senha</a>
                <?php endif ?>
                
                <a class="dropdown-item" href="?sair">Sair</a>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </header>
    <div class="navigation">
      <div class="container-fluid mb-3 menu-perfil p-0">
        <div class="align-items-center d-flex justify-content-center mb-1 menu-perfil1 p-2 pb-3 pt-4">
          <div class="justify-content-center align-items-center menu-perfil1">
            <div class="mb-4 perfil-foto">
              <div class="m-auto mb-1 rounded-circle overflow-hidden">
                <img class="img-fluid" src="//fxtream.xyz/dashboard/img/user.png" alt="">
              </div>
            </div>
            <div class="text-center perfil-info" style="border-bottom: #000 1px; border-bottom-style: solid;">
              <p class="mb-0 text-uppercase" style="font-size: .9rem; font-weight: 800;"> <?php echo $_SESSION['username']; ?> </p>
              <small class="text-uppercase role" style="font-size: .7rem; letter-spacing: 1px; font-weight: 500;" id="tipo_admin">  </small>
              <br />
            </div>
            <div class="perfil-info text-center">
              <div class="mb-0 text-uppercase d-flex justify-content-between align-items-center" style="font-size: .9rem;font-weight: bolder;font-family: monospace;"> Creditos <span class="badge bg-success">
                  <i class="fa-solid fa-cent-sign pr-1">:</i> <span id="creditos2"> </span> </span>
              </div>
              <div class="mb-0 text-uppercase d-flex justify-content-between align-items-center" style="font-size: .9rem;font-weight: bolder;font-family: monospace;" id="vencimento">
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="menuToggle2"></div>
      <ul class="p-0">
        <li class="list" id="dashboard">
          <a href="dashboard.php" class="clr">
            <span class="icon">
              <i class="fa-solid fa-chart-line"></i>
            </span>
            <span class="text color">Dashboard</span>
          </a>
        </li>
        <?php if (isset($_SESSION['nivel_admin']) && $_SESSION['nivel_admin'] == true) { ?>
        <li class="list sub" id="Conteudos">
          <a class="clr">
            <span class="icon">
              <i class="fa-solid fa-gear fa-spin"></i>
            </span>
            <span class="text color">Conteudos</span>
            <i class="fa-solid fa-caret-right"></i>
          </a>
          <div class="sidebar-submenu">
            <ul class="ps-2">
              <li>
                <a href="categorias.php">
                  <i class="fas fa-table-list"></i>
                  <span class="text color">Categorias</span>
                </a>
              </li>
              <li>
                <a href="canais.php">
                  <i class="fas fa-tv"></i>
                  <span class="text color">Canais</span>
                </a>
              </li>
              <li>
                <a href="filmes.php">
                  <i class="fas fa-clapperboard"></i>
                  <span class="text color">Filmes</span>
                </a>
              </li>
              <li>
                <a href="serie.php">
                  <i class="fas fa-film"></i>
                  <span class="text color">Series</span>
                </a>
              </li>
              <li>
                <a href="uploud.php">
                  <i class="fa-solid fa-arrow-up-from-bracket"></i>
                  <span class="text color">Uploud</span>
                </a>
              </li>
            </ul>
          </div>
        </li>
        <li class="list sub" id="ferramentas">
          <a class="clr">
            <span class="icon">
              <i class="fa-solid fa-cogs fa-spin"></i>
            </span>
            <span class="text color">Ferramentas</span>
            <i class="fa-solid fa-caret-right"></i>
          </a>
          <div class="sidebar-submenu">
            <ul class="ps-2 text-nowrap">
              <li>
                <a onclick='modal_master("api/categorias.php", "delete_tudo", "tudo", "msg_info", "aguarde", "10000")'>
                  <i class="fa-trash fas text-danger"></i>
                  <span class="text color">Deletar tudo</span>
                </a>
              </li>
              <li>
                <a onclick='modal_master("api/categorias.php", "delete_tudo", "canais", "msg_info", "aguarde", "10000")'>
                  <i class="fa-trash fas text-danger"></i>
                  <span class="text color">Deletar canais</span>
                </a>
              </li>
              <li>
                <a onclick='modal_master("api/categorias.php", "delete_tudo", "filmes", "msg_info", "aguarde", "10000")'>
                  <i class="fa-trash fas text-danger"></i>
                  <span class="text color">Deletar Filmes</span>
                </a>
              </li>
              <li>
                <a onclick='modal_master("api/categorias.php", "delete_tudo", "series", "msg_info", "aguarde", "10000")'>
                  <i class="fa-trash fas text-danger"></i>
                  <span class="text color">Deletar series</span>
                </a>
              </li>
            </ul>
          </div>
        </li>
        <?php } ?>
        <li class="list sub" id="clientes">
          <a class="clr">
            <span class="icon">
              <i class="fa-solid fa-user-group hydrated md"></i>
            </span>
            <span class="text color">Clientes</span>
            <span class="badge badge-pill badge-success info">1</span>
            <i class="fa-solid fa-caret-right"></i>
          </a>
          <div class="sidebar-submenu">
            <ul class="ps-2">
              <li>
                <a href="clientes.php">
                  <i class="fa-solid fa-user"></i>
                  <span class="text color">Clientes</span>
                </a>
              </li>
              <li>
                <a href="testes.php">
                  <i class="fa-solid fa-user"></i>
                  <span class="text color">Testes</span>
                </a>
              </li>
              <?php if (isset($_SESSION['plano_admin']) && $_SESSION['plano_admin'] != 1) { ?>
              <li>
                <a href="revendedores.php">
                  <i class="fa-solid fa-users-gear"></i>
                  <span class="text color">Revendedores</span>
                </a>
              <li>
              <?php } ?>
            </ul>
          </div>
        </li>
        <li class='list sub' id='configuracoes'>
          <a class='clr'>
            <span class='icon'>
              <i class="fa-solid fa-gear fa-spin"></i>
            </span>
            <span class='text color'>Settings</span>
            <i class="fa-solid fa-caret-right"></i>
          </a>
          <div class='sidebar-submenu'>
            <ul class="ps-2">
              <li>
                <a href='planos.php'>
                  <i class='fa-solid fa-server'></i>
                  <span class='text color'>Planos</span>
                </a>
              </li>
            </ul>
          </div>
        </li>
        <div class="indicator"></div>
      </ul>
    </div>
    

<script>
  $(document).ready(function () {

      var currentURL = window.location.pathname.split('/').pop();
      currentURL = currentURL.replace('.php', '').replace('.html', '');
      $('li').each(function () {
        if ($(this).attr('id') === currentURL) {
          $(this).addClass('active');
        }
      });

      var currentURL = window.location.pathname.split('/').pop();
      if (currentURL === 'live.php' || currentURL === 'vod.php' || currentURL === 'series.php') {
        $('#vereditarpagar').addClass('active');
      }
      if (currentURL === 'importar-live.php' || currentURL === 'importar-vod.php' || currentURL === 'importar-series.php') {
        $('#importar').addClass('active');
      }
      if (currentURL === 'clientes.php' || currentURL ===  'testes.php' || currentURL ===  'revendedores.php' || currentURL ===  'sub-revenda.php') {
        $('#clientes').addClass('active');
      }
      if (currentURL === 'admin.php' || currentURL === 'servidores.php' || currentURL === 'planos.php' || currentURL === 'planos') {
        $('#configuracoes').addClass('active');
      }
      if (currentURL === 'categorias.php' || currentURL === 'canais.php' || currentURL === 'filmes.php' || currentURL === 'serie.php' || currentURL === 'uploud.php' || currentURL === 'divisor-m3u.php') {
        $('#Conteudos').addClass('active');
      }

        $('.menuToggle, .menuToggle2').click(function () {
            $('.navigation').toggleClass('active');
            $('.page-content').toggleClass('active');
            $('.navbar').toggleClass('active');
        });


        

        $('.list').click(function () {
          $('.list').removeClass('active');
          $(this).addClass('active');
          
        });
        $('.list.sub').click(function () {
          $('.navigation').addClass('active');
          $('.page-content').addClass('active');
          $('.navbar').addClass('active');
          
        });
        
    });


  function addActiveClassOnLargeScreen() {
    const screenWidth = window.innerWidth;
    const elements = $('.navigation, .page-content, .navbar, .text-logo'); // Seleciona todos os elementos de uma vez

    if (screenWidth >= 768) {
      elements.addClass('active'); // Adiciona a classe 'active' a todos os elementos selecionados
    } else {
      elements.removeClass('active'); // Remove a classe 'active' de todos os elementos selecionados
    }
  }

  // Chama a função inicialmente quando a página carrega
  $(document).ready(function () {
    addActiveClassOnLargeScreen();
  });

  // Adiciona um event listener para detectar mudanças no tamanho da tela
  window.addEventListener('resize', () => {
    addActiveClassOnLargeScreen();
  });

</script>
<script src="./js/sweetalert2.js"></script>
<script src="./js/custom.js"></script>


  <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
  <main class="active bg-dark-subtle bg-gradient overflow-auto page-content w-100" style="position: absolute;background: #f2f3f8;height: 100%;">
  <div class="container-fluid">
