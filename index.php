<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  
  <meta content="Furia401" name="author">
  <title>Login | Xtream Serve open source</title>
  <!-- App favicon -->
  <link rel="shortcut icon" href="./img/icon.png">
  <!-- Bootstrap CSS -->
  <link href="//cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <!-- jQuery - Biblioteca necessária para o Bootstrap -->
  <script src="./js/jquery-3.6.0.min.js"></script>
</head>
<body style="background: radial-gradient(circle, rgba(49,56,62,1) 0%, rgba(27,30,38,1) 100%);">
<section class="auth-bg-cover min-vh-100 p-4 d-flex align-items-center justify-content-center" style="background-image: url(./img/background.png);background-size: 190%;background-position: center;"></div>
  <div class="container-fluid px-0">
    <div class="g-0 row">
      <div class="col-lg-12 col-xl-12">
        <div class="d-flex flex-column flex-lg-wrap-reverse h-100 justify-content-between mb-0 p-4">
          <div class="align-items-center d-flex mb-3">
            <div class="flex-grow-1">
              <img src="./img/logo_1376x509.png" alt="" height="100">
            </div>
          </div>
          <div class="col-xl-4 col-lg-6">
            <div class="card mb-0" style=" background-color: #00060c;">
              <div class="card-body p-4 p-sm-5 m-lg-2">
                <div class="text-center mt-2">
                  <h5 class="fs-22 text-primary">Bem-vindo!</h5>
                  <p class="text-muted">Bem-vindo ao painel XtreamServer Open</p>
                </div>
                <div class="p-2 mt-5">
                  <form id="login_form" onsubmit="event.preventDefault();">
                    <div class="mb-3">
                      <input name="login" type="hidden" id="login">
                      <label for="username" class="form-label text-white">Usuário</label>
                      <input name="username" value="" type="text" class="form-control" id="username" placeholder="Coloque o Usuário" >
                    </div>
                    <div class="mb-3">
                      <label class="form-label text-white" for="password-input">Senha</label>
                      <div class="position-relative auth-pass-inputgroup mb-3">
                        <input name="password" type="password" class="form-control pe-5 password-input" placeholder="******" id="password-input" >
                        <button class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted password-addon" type="button" id="password-addon">
                          <i class="ri-eye-fill align-middle"></i>
                        </button>
                      </div>
                    </div>
                    <div class="mt-4 text-center">
                      <button type="submit" onclick="enviardados('login_form')" class="btn btn-primary w-100">Entrar</button>
                    </div>
                  </form>
                  <div class="text-center mt-5"></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <footer class="bg-gradient pt-5">
    <div class="container">
      <div class="row">
        <div class="col-lg-4 col-sm-12 col-md-12">
          <div class="mb-4 single-footer-widget">
            <div class="logo">
              <div class="d-flex align-items-end">
                <img src="./img/logo_tranparente2.png" alt="xtreame server open seouce" height="40">
                <span class="text-uppercase text-white" style="padding-left:0.5rem;font-size:1.25rem">Xtream Server OpenSource</span>
              </div>
            </div>
            
          <div class="social">
              <div class="d-flex align-items-end">
                
                
              <a href="https://t.me/FURIA401" class="fs-1 m-1">
                  <i class="fa-telegram fab" style="font-size: larger;"></i>
                </a><a class="fs-1 m-1 text-danger">
                  <i class="fab fa-youtube"></i>
                </a></div>
            </div></div>
        </div>
        
        <div class="col-lg-4 col-sm-6 col-md-6">
          <div class="single-footer-widget">
            <h3 class="mb-4 pb-3 text-white-50">Contato</h3>
            <ul class="list list-inline">
              
              
            <li>
                <div class="icon text-white">
                  <i class="fa-telegram fab" style=""></i>
                <span>Telegram: </span><a href="https://t.me/FURIA401" target="_blank">@FURIA401</a></div>
                
                
              </li></ul>
          </div>
        </div>
      </div>
      <div class="copyright-area my-4">
        
      <p class="py-4 text-center text-white" style="border-top: 1px solid #192129;">© <?php echo date("Y"); ?>. Criado com <i class="fa fa-heart text-danger"></i> por <a href="https://fxtream.xyz/">FXTREAM.XYZ</a>
            </p></div>
    </div>
  </footer>
    </div>
  </div>
</section>
<script src="./js/sweetalert2.js"></script>
<script>

var solicitacaoPendente = false;
function enviardados(id_formulario) {
    if (solicitacaoPendente) {
        SweetAlert2('Aguarde!', 'warning');
        setTimeout(function() {
                solicitacaoPendente = false;
            }, 3000);
        return;
    }

    solicitacaoPendente = false;

    //$('#cadastro_form').modal();
    // Serializa os dados do formulário
    var dados = $("#" + id_formulario).serialize();
    console.log(dados);
    $.ajax({
        type: "GET",
        url: "./api/login.php",
        data: dados,
        success: function(response) {
            if (response.trim() === '') {
                SweetAlert2('Resposta do servidor vazia.', 'error');
            } else {
                var jsonResponse = JSON.parse(response); // Analisa a resposta JSON
                if (jsonResponse.icon === 'error') {
                    SweetAlert2(jsonResponse.title, jsonResponse.icon);
                }else{
                    SweetAlert2(jsonResponse.title, jsonResponse.icon);
                    if (jsonResponse.url) {
                      setTimeout(function() {
                        window.location.href = jsonResponse.url;
                      }, parseInt(jsonResponse.time, 10));
                    }
                  }
                }
              },
        error: function(data) {
            SweetAlert2('Erro na solicitaçao!', 'error');
        },
        complete: function() {
            solicitacaoPendente = false; // Define a solicitação como concluída, independentemente do resultado
        }
    });
}

//SweetAlert2("teste de notificaçao", "success");
function SweetAlert2(title, icon){
    const Toast = Swal.mixin({
    toast: true,
    position: "top-end",
    showConfirmButton: false,
    timer: 2000,
    timerProgressBar: true,
    didOpen: (toast) => {
      toast.onmouseenter = Swal.stopTimer;
      toast.onmouseleave = Swal.resumeTimer;
    }
  });
  Toast.fire({
    icon: icon,
    title: title
  });
}
</script>
</body>
</html>
