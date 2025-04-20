document.addEventListener("DOMContentLoaded", function() {

    function addOverflowAuto() {
        var wrapper = document.getElementById("data_table_wrapper");
        if (wrapper) {
            var layoutCell = wrapper.querySelector(".dt-layout-table");
            var dt_control = wrapper.querySelector(".dt-control");
            if (layoutCell) {
                layoutCell.classList.add("overflow-auto");

            }if (dt_control) {
                dt_control.classList.remove("dt-ordering-desc");

            }
        }
    }

    var observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {

            if (mutation.target.id === "data_table_wrapper") {
                addOverflowAuto(); 
            }
        });
    });

    observer.observe(document.body, { childList: true, subtree: true });

    addOverflowAuto();
});

async function updateinfo() {
    try {

        const response = await fetch(`api/listar-clientes.php?info_admin`);

        if (!response.ok) {
            throw new Error('Erro ao carregar dados');
        }

        const jsonResponse = await response.json();

        if (jsonResponse.icon === 'error') {

            SweetAlert2(jsonResponse.title, jsonResponse.msg, jsonResponse.icon);
        } else {

            $('#creditos').html(jsonResponse.creditos);
            $('#creditos2').html(jsonResponse.creditos);
            $('#tipo_admin').html(jsonResponse.tipo_admin);
            if (jsonResponse.vencimento) {
                $('#vencimento').html('Vencimento <span style="font-size: .7rem; letter-spacing: 1px; font-weight: 500;">' + jsonResponse.vencimento + '</span>');
            }
        }
    } catch (error) {

        try {
            await fetch(`?sair`);
            SweetAlert2('Erro!', 'Erro ao buscar info do admin, usuário será deslogado.', 'error');
            console.warn('Falha na atualização, usuário será deslogado.');
            setTimeout(() => {
                window.location.href = window.location.href;
            }, 5000); 

        } catch (logoutError) {
            console.error('Erro ao chamar API de logout:', logoutError);
        }
    }
}

updateinfo();

async function modal_master(url, parametro1, valor1, parametro2, valor2, parametro3, valor3){
    if (parametro2 == "msg_info") {
        SweetAlert3(valor2, 'info', parametro3);
    }
    if (parametro1 == "reenviar") {
        var dados = $("#modal_master_form").serialize();
    }else{
        var dados = {
            [parametro1]: valor1,
            [parametro2]: valor2,
            [parametro3]: valor3
        }
    }
        $.ajax({
            type: "POST",
            url: url,
            data: dados,
            success: function(response) {
                if (!response) {
                    SweetAlert2('Erro!', 'Resposta do servidor vazia.', 'error');

                } else {

                    if (response.icon === 'error') {
                        SweetAlert2(response.title, response.msg, response.icon);
                    }
                    if (response.icon === 'success') {
                        SweetAlert2(response.title, response.msg, response.icon);
                        if (response.modal === 'hide'){
                            $("#modal_master").modal('hide');
                        }
                    }
                    if (response.modal_titulo){
                        $('#modal_master-header').removeClass().addClass(response.modal_header_class);
                        $('#modal_master-titulo').html(response.modal_titulo);
                        $('#modal_master-body').html(response.modal_body);
                        $('#modal_master-footer').html(response.modal_footer);
                        if (response.modal_master_body_style) {
                            document.getElementById('modal_master-body').style = response.modal_master_body_style;
                        }

                        $('#modal_master').modal('show');

                    }
                    if (response.data_table === 'atualizar') {
                        data_table = $('#data_table').DataTable();
                        var pagina_atual = data_table.page(); 

                        data_table.draw();

                        data_table.page(pagina_atual).draw('page');
                    }
                }
            },
            error: function(data) {
                SweetAlert2('Erro!', 'Erro na solicitação', 'error');

            },
            complete: function() {

            }
        });

}

$(document).ready(function() {
    $("#cadastro_form").submit(function(event) {
        event.preventDefault(); 
    });

});

var solicitacaoPendente = false;
function enviardados(id_formulario, url) {
    if (solicitacaoPendente) {
        SweetAlert2('Aguarde!', 'Uma solicitação já está em andamento. Por favor, aguarde.', 'warning');
        setTimeout(function() {
                solicitacaoPendente = false;
            }, 3000);
        return;
    }
    solicitacaoPendente = true;

    var dados = $("#" + id_formulario).serialize();
    $.ajax({
        type: "POST",
        url: "api/" + url,
        data: dados,
        success: function(response) {
            if (!response) {
                SweetAlert2('Erro!', 'Resposta do servidor vazia.', 'error');

            } else {
                if (response.icon === 'error') {
                    SweetAlert2(response.title, response.msg, response.icon);

                }else{
                    SweetAlert2(response.title, response.msg, response.icon);
                    if (response.url) {
                        window.location.href = response.url;
                    }
                    var currentURL = window.location.pathname.split('/').pop();

                    if ((response.icon === 'success') && currentURL === 'dashboard.php' || currentURL === 'dashboard') {
                        solicitacaoPendente = false;
                        window.location.href = currentURL;
                    }
                    $("#modal_master").modal('hide');
                    data_table = $('#data_table').DataTable();
                    var pagina_atual = data_table.page(); 

                    data_table.draw();

                    data_table.page(pagina_atual).draw('page');
                    updateinfo();

                }
            }
        },
        error: function(data) {
            SweetAlert2('Erro!', 'Erro na solicitação', 'error');
        },
        complete: function() {
            solicitacaoPendente = false; 
        }
    });
}
function SweetAlert2(title, text, icon){
    Swal.fire({
  title: title,
  text: text,
  icon:  icon 
});
}

function SweetAlert3(title, icon, timer){
    if (timer) {
    }else{
        var timer = 5000;
    }
    const Toast = Swal.mixin({
    toast: true,
    position: "top-end",
    showConfirmButton: false,
    timer: timer,
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

function alerta(status, time, msg) {
    var timer = 5000;
    var $alerta = $('<div class="alert ' + status + '" role="alert" style="position: absolute;bottom: ' + position_bottom + 'px;right: 0px;margin-right: 20px;z-index: 1050;display: block;">'+ msg +'</div>');
    position_bottom += 70;
    $('#alerta').append($alerta);
    console.log(position_bottom);
    setTimeout(function() {
        $alerta.remove();
        position_bottom -= position_bottom;
        solicitacaoPendente = false;
    }, time);
}