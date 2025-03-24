$(document).ready(function() {
    var table = $('#data_table').DataTable({
        ajax: './api/listar-clientes.php?listar_revendedores',
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
                data: "usuario",
                className: "text-center"
            }, // ID
            {
                data: "creditos",
                className: "text-center"
            }, // ID
            {
                data: "tipo",
                className: "text-center"
            }, // ID
            {
                data: "qnt_revendedores",
                className: "text-center"
            }, // ID
            {
                data: "qnt_clientes",
                className: "text-center"
            }, // ID
            {
                data: "acao",
                className: "text-center"
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