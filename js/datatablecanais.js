$(document).ready(function() {
    var table = $('#data_table').DataTable({
        ajax: './api/listar-clientes.php?listar_canais',
        processing: true,
        serverSide: true,
        language: {
        url: './js/datatables/pt_br.json'
    },
    layout: {
        topStart: null,
        bottom: 'paging',
        bottomStart: "info",
        bottomEnd: null
    },
        columns: [
            {
                data: "id",
                className: "text-center"
            }, // ID
            {
                data: "name",
                className: "text-center"
            }, // URL
            {
                orderable: false,
                data: "usuario",
                className: "text-center"
            },
            {
                orderable: false,
                data: "indicados",
                className: "text-center"
            },
            {
                orderable: false,
                data: "status",
                className: "text-center"
            },
            {
                //orderable: false,
                data: "is_adult",
                className: "text-center"
            },
            {
                orderable: false,
                data: "acao",
                className: "text-center acao"
            }, // Logo URL
            /*{ orderable: false,
            data: null, // Ações (nenhuma data associada)
            render: function(data, type, row) {
              botao_editar = '<a id="' + row.id + '" class="btn btn-info btn-sm">EDITAR</a>';
              botao_excluir = '<a class="btn btn-danger btn-sm">Excluir</a>';
            return botao_editar + botao_excluir;
            }
            }*/
        ],
    order: [[0, 'asc']]
    });
  });