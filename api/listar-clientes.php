<?php
session_start();
require_once('./controles/db.php');

if (isset($_GET['listar_clientes'])) {
    header('Content-Type: application/json; charset=utf-8');

    $conexao = conectar_bd();

    $dados_requisicao = $_REQUEST;
    $admin_id = $_SESSION['admin_id'];

    $colunas = [
        0 => 'id',
        1 => 'name',
        2 => 'usuario',
        3 => 'servidores',
        5 => 'Vencimento',
        6 => 'Vencimento',
    ];

    $query = "SELECT COUNT(id) AS qnt_usuarios FROM clientes WHERE (admin_id = :admin_id AND is_trial = 0)";

    if (!empty($dados_requisicao['search']['value'])) {
        $query .= " AND (id LIKE :id OR name LIKE :name OR usuario LIKE :usuario)";
    }

    $query = $conexao->prepare($query);
    $query->bindValue(':admin_id', $admin_id);
    if (!empty($dados_requisicao['search']['value'])) {
        $valor_pesq = "%" . $dados_requisicao['search']['value'] . "%";
        $query->bindValue(':id', $valor_pesq);
        $query->bindValue(':name', $valor_pesq);
        $query->bindValue(':usuario', $valor_pesq);
    }

    $query->execute();
    $result = $query->fetch(PDO::FETCH_ASSOC);

    $inicio = (int)$dados_requisicao['start'];
    $quantidade = (int)$dados_requisicao['length'];

    $list_query = "SELECT *
                        FROM clientes  
                        WHERE (BINARY admin_id = :admin_id AND is_trial = 0)";

    if (!empty($dados_requisicao['search']['value'])) {
        $list_query .= " AND (id LIKE :id OR clientes.name LIKE :name OR clientes.usuario LIKE :usuario)";
    }

    $list_query .= " ORDER BY " . $colunas[$dados_requisicao['order'][0]['column']] . " " . $dados_requisicao['order'][0]['dir'] . " LIMIT :quantidade OFFSET :inicio";

    $list_query = $conexao->prepare($list_query);
    $list_query->bindValue(':admin_id', $admin_id);
    $list_query->bindValue(':inicio', $inicio, PDO::PARAM_INT);
    $list_query->bindValue(':quantidade', $quantidade, PDO::PARAM_INT); 

    if (!empty($dados_requisicao['search']['value'])) {
        $valor_pesq = "%" . $dados_requisicao['search']['value'] . "%";
        $list_query->bindValue(':id', $valor_pesq);
        $list_query->bindValue(':name', $valor_pesq);
        $list_query->bindValue(':usuario', $valor_pesq);
    }

    $list_query->execute();

    $dados = [];
    while ($row = $list_query->fetch(PDO::FETCH_ASSOC)) {
        extract($row);

        if (strtotime($Vencimento) < strtotime(date("Y-m-d"))) {
            $status = '<span class="badge bg-warning w-100 text-dark"> Expirado </span>';
        } else {
            $status = '<span class="badge bg-success w-100 text-dark"> Ativo </span>';
        }

        $acoes = '<a class="btn btn-sm btn-outline-lightning rounded-0 mr-2" onclick=\'modal_master("api/clientes.php", "info_cliente", "' . $id . '")\'><i class="fa-solid fa-eye"></i></a>';

        $acoes .= '<a class="btn btn-sm btn-outline-lightning rounded-0 mr-2" onclick=\'modal_master("api/clientes.php", "edite_cliente", "' . $id . '")\'><i class="fa fa-edit"></i></a>';

        $acoes .= '<button class="btn" type="button" id="dropdownUser" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa-caret-down fa-solid"></i></button>';

        $dropdown_menu = '<ul class="dropdown-menu dropdown-menu-left" aria-labelledby="dropdownUser" style="">';

       $dropdown_menu .= '<li><button type="button" class="btn btn-primary dropdown-item" data-toggle="modal" data-placement="top" title="Renovar" onclick=\'modal_master("api/clientes.php", "renovar_cliente", "' . $id . '", "usuario", "'.$usuario.'")\'><i class="fas fa-retweet"></i> Renovar </button></li> ';

       $dropdown_menu .= '<li> <button type="button" class="btn btn-primary dropdown-item"  data-placement="top" title="Apagar" onclick=\'modal_master("api/clientes.php", "delete_cliente", "' . $id . '", "usuario", "'.$usuario.'")\'> <i class="far fa-trash-alt text-danger"></i> Apagar  </button></li>';
       $dropdown_menu .= '</ul>';

       $acoes .= $dropdown_menu;

        $registros_com_childs_rows = [
            "id"       => $id,
            "name"      => $name,
            "usuario" => $usuario,
            "indicados" => '',
            "status"      => $status,
            "vencimento"      => date('d-m-y H:i:s', strtotime($Vencimento)),

            "acao"     => $acoes
        ];

        $dados[] = $registros_com_childs_rows;
    }

    $resultado = [
        "draw"            => intval($dados_requisicao['draw']),
        "recordsTotal"    => intval($result['qnt_usuarios']),
        "recordsFiltered" => intval($result['qnt_usuarios']),
        "data"            => $dados
    ];

    echo json_encode($resultado);
}

if (isset($_GET['listar_testes'])) {
    header('Content-Type: application/json; charset=utf-8');

    $conexao = conectar_bd();

    $dados_requisicao = $_REQUEST;
    $admin_id = $_SESSION['admin_id'];

    $colunas = [
        0 => 'id',
        1 => 'name',
        2 => 'usuario',
        3 => 'servidores',
        4 => 'Vencimento',
        5 => 'Vencimento',
    ];

    $query = "SELECT COUNT(id) AS qnt_usuarios FROM clientes WHERE (admin_id = :admin_id AND is_trial = 1)";

    if (!empty($dados_requisicao['search']['value'])) {
        $query .= " AND (id LIKE :valor_pesq OR name LIKE :valor_pesq OR usuario LIKE :valor_pesq)";
    }

    $query = $conexao->prepare($query);
    $query->bindValue(':admin_id', $admin_id);
    if (!empty($dados_requisicao['search']['value'])) {
        $valor_pesq = "%" . $dados_requisicao['search']['value'] . "%";
        $query->bindValue(':valor_pesq', $valor_pesq);
    }

    $query->execute();
    $result = $query->fetch(PDO::FETCH_ASSOC);

    $inicio = (int)$dados_requisicao['start'];
    $quantidade = (int)$dados_requisicao['length'];

    $list_query = "SELECT clientes.*
                        FROM clientes 
                        WHERE (BINARY clientes.admin_id = :admin_id AND is_trial = 1)";

    if (!empty($dados_requisicao['search']['value'])) {
        $list_query .= " AND (id LIKE :valor_pesq OR clientes.name LIKE :valor_pesq OR clientes.usuario LIKE :valor_pesq)";
    }

    $list_query .= " ORDER BY " . $colunas[$dados_requisicao['order'][0]['column']] . " " . $dados_requisicao['order'][0]['dir'] . " LIMIT :quantidade OFFSET :inicio";

    $list_query = $conexao->prepare($list_query);
    $list_query->bindValue(':admin_id', $admin_id);
    $list_query->bindValue(':inicio', $inicio, PDO::PARAM_INT);
    $list_query->bindValue(':quantidade', $quantidade, PDO::PARAM_INT); 

    if (!empty($dados_requisicao['search']['value'])) {
        $valor_pesq = "%" . $dados_requisicao['search']['value'] . "%";
        $list_query->bindValue(':valor_pesq', $valor_pesq);
    }

    $list_query->execute();

    $dados = [];
    while ($row = $list_query->fetch(PDO::FETCH_ASSOC)) {
        extract($row);

        if (strtotime($Vencimento) < strtotime(date("Y-m-d H:i:s"))) {
            $status = '<span class="badge bg-warning w-100 text-dark"> Expirado </span>';
        } else {
            $status = '<span class="badge bg-success w-100 text-dark"> Ativo </span>';
        }

       $acoes = '<a class="btn btn-sm btn-outline-lightning rounded-0 mr-2" onclick=\'modal_master("api/clientes.php", "info_cliente", "' . $id . '")\'><i class="fa-solid fa-eye"></i></a>';

       $acoes .= '<a class="btn btn-sm btn-outline-lightning rounded-0 mr-2" onclick=\'modal_master("api/clientes.php", "edite_cliente", "' . $id . '")\'><i class="fa fa-edit"></i></a>';

       $acoes .= '<button class="btn" type="button" id="dropdownUser" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="fa-caret-down fa-solid"></i> </button>';

       $dropdown_menu = '<ul class="dropdown-menu dropdown-menu-left" aria-labelledby="dropdownUser" style="">';

       $dropdown_menu .= '<li> <button type="button" class="btn btn-primary dropdown-item" data-toggle="modal" data-placement="top" title="Ativar Teste" onclick=\'modal_master("api/testes.php", "ativar_teste", "' . $id . '", "usuario", "'.$usuario.'")\'>  <i class="fa-solid fa-user-check "></i> Ativar Teste  </button>  </li> ';

       $dropdown_menu .= '<li> <button type="button" class="btn btn-primary dropdown-item"  data-placement="top" title="Apagar" onclick=\'modal_master("api/testes.php", "delete_cliente", "' . $id . '", "usuario", "'.$usuario.'")\'> <i class="far fa-trash-alt text-danger"></i> Apagar  </button></li>';
       $dropdown_menu .= '</ul>';

       $acoes .= $dropdown_menu;

        $registros_com_childs_rows = [
            "id"       => $id,
            "name"      => $name,
            "usuario" => $usuario,
            "indicados" => '',
            "status"      => $status,
            "vencimento"      => date('d-m-y H:i:s', strtotime($Vencimento)),

            "acao"     => $acoes
        ];

        $dados[] = $registros_com_childs_rows;
    }

    $resultado = [
        "draw"            => intval($dados_requisicao['draw']),
        "recordsTotal"    => intval($result['qnt_usuarios']),
        "recordsFiltered" => intval($result['qnt_usuarios']),
        "data"            => $dados
    ];

    echo json_encode($resultado);
}

if (isset($_GET['listar_revendedores'])) {
    header('Content-Type: application/json; charset=utf-8');

    $conexao = conectar_bd();

    $dados_requisicao = $_REQUEST;
    $token = isset($_SESSION['token']) ? $_SESSION['token'] : "0";
    $admin_id = isset($_SESSION['admin_id']) ? $_SESSION['admin_id'] : null;

    $colunas = [
        0 => 'id',
        1 => 'user',
        2 => 'url',
    ];

    $query = "SELECT COUNT(id) AS qnt_servidores FROM admin WHERE (criado_por = :admin_id)";

    if (!empty($dados_requisicao['search']['value'])) {
        $query .= " AND (id LIKE :id OR user LIKE :user)";
    }

    $query = $conexao->prepare($query);
    $query->bindValue(':admin_id', $admin_id);
    if (!empty($dados_requisicao['search']['value'])) {
        $valor_pesq = "%" . $dados_requisicao['search']['value'] . "%";
        $query->bindValue(':id', $valor_pesq);
        $query->bindValue(':user', $valor_pesq);
    }

    $query->execute();
    $result = $query->fetch(PDO::FETCH_ASSOC);

    $inicio = (int)$dados_requisicao['start'];
    $quantidade = (int)$dados_requisicao['length'];

    $list_query = "SELECT a.*, p.nome as tipo_admin
                        FROM admin a
                        LEFT JOIN planos_admin p ON p.id = a.plano  
                        WHERE (BINARY criado_por = :admin_id)";

    if (!empty($dados_requisicao['search']['value'])) {
        $list_query .= " AND (a.id LIKE :id OR user LIKE :user)";
    }

    $list_query .= " ORDER BY " . $colunas[$dados_requisicao['order'][0]['column']] . " " . $dados_requisicao['order'][0]['dir'] . " LIMIT :quantidade OFFSET :inicio";

    $list_query = $conexao->prepare($list_query);
    $list_query->bindValue(':admin_id', $admin_id);
    $list_query->bindValue(':inicio', $inicio, PDO::PARAM_INT);
    $list_query->bindValue(':quantidade', $quantidade, PDO::PARAM_INT); 

    if (!empty($dados_requisicao['search']['value'])) {
        $valor_pesq = "%" . $dados_requisicao['search']['value'] . "%";
        $list_query->bindValue(':id', $valor_pesq);
        $list_query->bindValue(':user', $valor_pesq);
    }

    $list_query->execute();

    $dados = [];
    while ($row = $list_query->fetch(PDO::FETCH_ASSOC)) {
        extract($row);

        $query = "SELECT COUNT(id) AS qnt FROM admin WHERE (criado_por = :id)";
        $query = $conexao->prepare($query);
        $query->bindValue(':id', $id);
        $query->execute();
        $qnt_revendedores = $query->fetch(PDO::FETCH_ASSOC);

        $query1 = "SELECT COUNT(id) AS qnt FROM clientes WHERE (admin_id = :id)";
        $query1 = $conexao->prepare($query1);
        $query1->bindValue(':id', $id);
        $query1->execute();
        $qnt_clientes = $query1->fetch(PDO::FETCH_ASSOC);

       $acoes = '';
       $acoes .= '<a class="btn btn-sm btn-outline-lightning rounded-0 mr-2" onclick=\'modal_master("api/revendedores.php", "adicionar_creditos", "' . $id . '", "usuario", "'.$user.'")\'><i class="fas fa-plus-circle"></i></a>';

       $acoes .= '<a class="btn btn-sm btn-outline-lightning rounded-0 mr-2" onclick=\'modal_master("api/revendedores.php", "edite_revendedor", "' . $id . '")\'><i class="fa fa-edit"></i></a>';

       $acoes .= '<button type="button" class="btn btn-sm btn-outline-lightning rounded-0 mr-2"  data-placement="top" title="Apagar" onclick=\'modal_master("api/revendedores.php", "delete_revendedor", "' . $id . '", "usuario", "'.$user.'")\'> <i class="fa fa-trash"></i></button>';

       $tipo_admin_parcial = strstr($tipo_admin, ':');
       $tipo_admin = trim($tipo_admin_parcial, ': ');

        $registros_com_childs_rows = [
            "id"       => $id,
            "usuario"      => $user,
            "creditos"      => $creditos,
            "tipo"      => $tipo_admin,
            "qnt_revendedores"      => $qnt_revendedores['qnt'],
            "qnt_clientes"      => $qnt_clientes['qnt'],

            "acao"     => $acoes
        ];

        $dados[] = $registros_com_childs_rows;
    }

    $resultado = [
        "draw"            => intval($dados_requisicao['draw']),
        "recordsTotal"    => intval($result['qnt_servidores']),
        "recordsFiltered" => intval($result['qnt_servidores']),
        "data"            => $dados
    ];

    echo json_encode($resultado);
}

if (isset($_GET['listar_categorias'])) {
    header('Content-Type: application/json; charset=utf-8');

    $conexao = conectar_bd();

    $dados_requisicao = $_REQUEST;
    $admin_id = $_SESSION['admin_id'];

    $colunas = [
        0 => 'id',
        1 => 'nome',
        2 => 'type',
        3 => 'is_adult',
        5 => 'bg',
        6 => 'Vencimento',
    ];

    $query = "SELECT COUNT(id) AS qnt_categorias FROM categoria WHERE (admin_id = :admin_id)";

    if (!empty($dados_requisicao['search']['value'])) {
        $query .= " AND (id LIKE :id OR nome LIKE :nome OR type LIKE :type)";
    }

    $query = $conexao->prepare($query);
    $query->bindValue(':admin_id', $admin_id);
    if (!empty($dados_requisicao['search']['value'])) {
        $valor_pesq = "%" . $dados_requisicao['search']['value'] . "%";
        $query->bindValue(':id', $valor_pesq);
        $query->bindValue(':nome', $valor_pesq);
        $query->bindValue(':type', $valor_pesq);
    }

    $query->execute();
    $result = $query->fetch(PDO::FETCH_ASSOC);

    $inicio = (int)$dados_requisicao['start'];
    $quantidade = (int)$dados_requisicao['length'];

    $list_query = "SELECT * FROM categoria WHERE (admin_id = :admin_id)";

    if (!empty($dados_requisicao['search']['value'])) {
        $list_query .= " AND (id LIKE :id OR nome LIKE :nome OR type LIKE :type)";
    }

    $list_query .= " ORDER BY " . $colunas[$dados_requisicao['order'][0]['column']] . " " . $dados_requisicao['order'][0]['dir'] . " LIMIT :quantidade OFFSET :inicio";

    $list_query = $conexao->prepare($list_query);
    $list_query->bindValue(':admin_id', $admin_id);
    $list_query->bindValue(':inicio', $inicio, PDO::PARAM_INT);
    $list_query->bindValue(':quantidade', $quantidade, PDO::PARAM_INT); 

    if (!empty($dados_requisicao['search']['value'])) {
        $valor_pesq = "%" . $dados_requisicao['search']['value'] . "%";
        $list_query->bindValue(':id', $valor_pesq);
        $list_query->bindValue(':nome', $valor_pesq);
        $list_query->bindValue(':type', $valor_pesq);
    }

    $list_query->execute();

    $dados = [];
    while ($row = $list_query->fetch(PDO::FETCH_ASSOC)) {
        extract($row);

        $adulto = 'nao';
        if ($is_adult == 1) {
            $adulto = 'Sim';
        }

        $acoes = '';

        $acoes .= '<a class="btn btn-sm btn-outline-lightning rounded-0 mr-2" onclick=\'modal_master("api/categorias.php", "edite_categorias", "' . $id . '")\'><i class="fa fa-edit"></i></a>';

        $acoes .= '<a class="btn btn-sm btn-outline-lightning rounded-0 mr-2"  data-placement="top" title="Apagar" onclick=\'modal_master("api/categorias.php", "delete_categorias", "' . $id . '", "name", "'.$nome.'")\'> <i class="far fa-trash-alt text-danger"></i></a>';

        $registros_com_childs_rows = [
            "category_id"       => $id,
            "category_name"      => $nome,
            "type" => $type,
            "is_adult" => $adulto,
            "bg" => $bg,

            "acao"     => $acoes
        ];

        $dados[] = $registros_com_childs_rows;
    }

    $resultado = [
        "draw"            => intval($dados_requisicao['draw']),
        "recordsTotal"    => intval($result['qnt_categorias']),
        "recordsFiltered" => intval($result['qnt_categorias']),
        "data"            => $dados
    ];

    echo json_encode($resultado);
}

if (isset($_GET['info_admin'])) {
    $conexao = conectar_bd();

    $token = isset($_SESSION['token']) ? $_SESSION['token'] : "0";
    $admin_id = isset($_SESSION['admin_id']) ? $_SESSION['admin_id'] : null;

    $sql = "SELECT *, p.nome as tipo_admin
            FROM admin a
            LEFT JOIN planos_admin p ON p.id = a.plano  
            WHERE a.id = :admin_id AND a.token = :token";
        $stmt = $conexao->prepare($sql);
        $stmt->bindParam(':admin_id', $admin_id, PDO::PARAM_INT);
        $stmt->bindParam(':token', $token);
        $stmt->execute();
    $resposta = [];
    if ($row = $stmt->fetch()) {
        extract($row);

        if ($admin == 1) {
            $tipo_admin = "Administrador";
            $creditos = '<i class="fa-solid fa-infinity"></i>';

        }else{
            $tipo_admin_parcial = strstr($tipo_admin, ':');
            $tipo_admin = "Nivel: ".trim($tipo_admin_parcial, ': ');
        }

        $resposta['tipo_admin'] = $tipo_admin;
        $resposta['creditos'] = $creditos;
        $resposta['icon'] = 'success';

        echo json_encode($resposta);
    }else {
        return 0;
    }
}
if (isset($_GET['listar_canais'])) {
    header('Content-Type: application/json; charset=utf-8');

    $conexao = conectar_bd();

    $dados_requisicao = $_REQUEST;
    $admin_id = $_SESSION['admin_id'];

    $colunas = [
        0 => 'id',
        1 => 'name',
        5 => 'is_adult',
    ];

    $query = "SELECT COUNT(s.id) AS qnt_usuarios 
              FROM streams s
              LEFT JOIN categoria c ON c.id = s.category_id
              WHERE s.stream_type = :stream_type";

    if (!empty($dados_requisicao['search']['value'])) {
        $query .= " AND (s.name LIKE :name OR c.nome LIKE :name)";
    }

    $query = $conexao->prepare($query);
    $query->bindValue(':stream_type', 'live');
    if (!empty($dados_requisicao['search']['value'])) {
        $valor_pesq = "%" . $dados_requisicao['search']['value'] . "%";
        $query->bindValue(':name', $valor_pesq);
    }

    $query->execute();
    $result = $query->fetch(PDO::FETCH_ASSOC);

    $inicio = (int)$dados_requisicao['start'];
    $quantidade = (int)$dados_requisicao['length'];

    $list_query = "SELECT s.*, c.nome as category_name
                        FROM streams s
                        LEFT JOIN categoria c ON c.id = s.category_id
                        WHERE (BINARY s.stream_type = :stream_type)";

    if (!empty($dados_requisicao['search']['value'])) {
        $list_query .= " AND (s.name LIKE :name OR c.nome LIKE :name)";
    }

    $list_query .= " ORDER BY s." . $colunas[$dados_requisicao['order'][0]['column']] . " " . $dados_requisicao['order'][0]['dir'] . " LIMIT :quantidade OFFSET :inicio";

    $list_query = $conexao->prepare($list_query);
    $list_query->bindValue(':stream_type', 'live', PDO::PARAM_STR);
    $list_query->bindValue(':inicio', $inicio, PDO::PARAM_INT);
    $list_query->bindValue(':quantidade', $quantidade, PDO::PARAM_INT);

    if (!empty($dados_requisicao['search']['value'])) {
        $valor_pesq = "%" . $dados_requisicao['search']['value'] . "%";
        $list_query->bindValue(':name', $valor_pesq, PDO::PARAM_STR);
    }

    $list_query->execute();

    $dados = [];
    while ($row = $list_query->fetch(PDO::FETCH_ASSOC)) {
        extract($row);
        if ($is_adult == 0) {
            $is_adult = '<span class="badge bg-warning w-100 text-dark"> Nao </span>';
        } else {
            $is_adult = '<span class="badge bg-success w-100 text-dark"> sim </span>';
        }

        $acoes = '';

        $acoes .= '<a class="btn btn-sm btn-outline-lightning rounded-0 mr-2" onclick=\'modal_master("api/canais.php", "editar_canal", "' . $id . '")\'><i class="fa fa-edit"></i></a>';

        $acoes .= '<a class="btn btn-sm btn-outline-lightning rounded-0 mr-2"  data-placement="top" title="Apagar" onclick=\'modal_master("api/canais.php", "delete_canal", "' . $id . '", "name", "'.$name.'")\'> <i class="far fa-trash-alt text-danger"></i></a>';

        $registros_com_childs_rows = [
            "id"       => $id,
            "name"      => $name,
            "usuario" => "<div style=\"width: 50px; height: 50px;\" class=\"bg-dark-subtle m-auto mb-1 overflow-hidden rounded-2\"> <img class=\"img-fluid w-100 h-100\" src=\"$stream_icon\" alt=".$name."></div>",
            "indicados" => $category_name,
            "status"      => $stream_type,
            "is_adult"      => $is_adult,

            "acao"     => $acoes
        ];

        $dados[] = $registros_com_childs_rows;
    }

    $resultado = [
        "draw"            => intval($dados_requisicao['draw']),
        "recordsTotal"    => intval($result['qnt_usuarios']),
        "recordsFiltered" => intval($result['qnt_usuarios']),
        "data"            => $dados
    ];

    echo json_encode($resultado);
}
if (isset($_GET['listar_filmes'])) {
    header('Content-Type: application/json; charset=utf-8');

    $conexao = conectar_bd();

    $dados_requisicao = $_REQUEST;
    $admin_id = $_SESSION['admin_id'];

    $colunas = [
        0 => 'id',
        1 => 'name',
        5 => 'is_adult',
    ];

    $query = "SELECT COUNT(s.id) AS qnt_usuarios 
              FROM streams s
              LEFT JOIN categoria c ON c.id = s.category_id
              WHERE s.stream_type = :stream_type";

    if (!empty($dados_requisicao['search']['value'])) {
        $query .= " AND (s.id LIKE :id OR s.name LIKE :name OR c.nome LIKE :name)";
    }

    $query = $conexao->prepare($query);
    $query->bindValue(':stream_type', 'movie');
    if (!empty($dados_requisicao['search']['value'])) {
        $valor_pesq = "%" . $dados_requisicao['search']['value'] . "%";
        $query->bindValue(':id', $valor_pesq);
        $query->bindValue(':name', $valor_pesq);
    }

    $query->execute();
    $result = $query->fetch(PDO::FETCH_ASSOC);

    $inicio = (int)$dados_requisicao['start'];
    $quantidade = (int)$dados_requisicao['length'];

    $list_query = "SELECT s.*, c.nome as category_name
                        FROM streams s
                        LEFT JOIN categoria c ON c.id = s.category_id
                        WHERE (BINARY s.stream_type = :stream_type)";

    if (!empty($dados_requisicao['search']['value'])) {
        $list_query .= " AND (s.id LIKE :id or s.name LIKE :name or c.nome LIKE :name)";
    }

    $list_query .= " ORDER BY s." . $colunas[$dados_requisicao['order'][0]['column']] . " " . $dados_requisicao['order'][0]['dir'] . " LIMIT :quantidade OFFSET :inicio";

    $list_query = $conexao->prepare($list_query);
    $list_query->bindValue(':stream_type', 'movie', PDO::PARAM_STR);
    $list_query->bindValue(':inicio', $inicio, PDO::PARAM_INT);
    $list_query->bindValue(':quantidade', $quantidade, PDO::PARAM_INT);

    if (!empty($dados_requisicao['search']['value'])) {
        $valor_pesq = "%" . $dados_requisicao['search']['value'] . "%";
        $list_query->bindValue(':id', $valor_pesq, PDO::PARAM_STR);
        $list_query->bindValue(':name', $valor_pesq, PDO::PARAM_STR);
    }

    $list_query->execute();

    $dados = [];
    while ($row = $list_query->fetch(PDO::FETCH_ASSOC)) {
        extract($row);
        if ($is_adult == 0) {
            $is_adult = '<span class="badge bg-warning w-100 text-dark"> Nao </span>';
        } else {
            $is_adult = '<span class="badge bg-success w-100 text-dark"> sim </span>';
        }

        $acoes = '';
        $acoes .= '<a class="btn btn-sm btn-outline-lightning rounded-0 mr-2" onclick=\'modal_master("api/filmes.php", "editar_filmes", "' . $id . '")\'><i class="fa fa-edit"></i></a>';

        $acoes .= '<a class="btn btn-sm btn-outline-lightning rounded-0 mr-2"  data-placement="top" title="Apagar" onclick=\'modal_master("api/filmes.php", "delete_filmes", "' . $id . '", "name", "'.$name.'")\'> <i class="far fa-trash-alt text-danger"></i></a>';

        $registros_com_childs_rows = [
            "id"       => $id,
            "name"      => $name,
            "usuario" => "<div class=\"bg-dark-subtle m-auto mb-1 overflow-hidden rounded-2\" style=\"max-width: 70px;\"> <img class=\"img-fluid\" src=\"$stream_icon\" alt=".$name."></div>",
            "indicados" => $category_name,
            "status"      => $stream_type,
            "vencimento"      => $is_adult,

            "acao"     => $acoes
        ];

        $dados[] = $registros_com_childs_rows;
    }

    $resultado = [
        "draw"            => intval($dados_requisicao['draw']),
        "recordsTotal"    => intval($result['qnt_usuarios']),
        "recordsFiltered" => intval($result['qnt_usuarios']),
        "data"            => $dados
    ];

    echo json_encode($resultado);
}
if (isset($_GET['listar_series'])) {
    header('Content-Type: application/json; charset=utf-8');

    $conexao = conectar_bd();

    $dados_requisicao = $_REQUEST;
    $admin_id = $_SESSION['admin_id'];

    $colunas = [
        0 => 'id',
        1 => 'name',
        3 => 'category_id',
        5 => 'is_adult',
    ];

    $query = "SELECT COUNT(s.id) AS qnt_usuarios 
              FROM series s
              LEFT JOIN categoria c ON c.id = s.category_id
              WHERE s.stream_type = :stream_type";

    if (!empty($dados_requisicao['search']['value'])) {
        $query .= " AND (s.name LIKE :name OR c.nome LIKE :name)";
    }

    $query = $conexao->prepare($query);
    $query->bindValue(':stream_type', 'series');
    if (!empty($dados_requisicao['search']['value'])) {
        $valor_pesq = "%" . $dados_requisicao['search']['value'] . "%";
        $query->bindValue(':name', $valor_pesq);
    }

    $query->execute();
    $result = $query->fetch(PDO::FETCH_ASSOC);

    $inicio = (int)$dados_requisicao['start'];
    $quantidade = (int)$dados_requisicao['length'];

    $list_query = "SELECT s.*, c.nome as category_name
                        FROM series s
                        LEFT JOIN categoria c ON c.id = s.category_id
                        WHERE (BINARY s.stream_type = :stream_type)";

    if (!empty($dados_requisicao['search']['value'])) {
        $list_query .= " AND (s.id LIKE :id or s.name LIKE :name or c.nome LIKE :name)";
    }

    $list_query .= " ORDER BY s." . $colunas[$dados_requisicao['order'][0]['column']] . " " . $dados_requisicao['order'][0]['dir'] . " LIMIT :quantidade OFFSET :inicio";

    $list_query = $conexao->prepare($list_query);
    $list_query->bindValue(':stream_type', 'series', PDO::PARAM_STR);
    $list_query->bindValue(':inicio', $inicio, PDO::PARAM_INT);
    $list_query->bindValue(':quantidade', $quantidade, PDO::PARAM_INT);

    if (!empty($dados_requisicao['search']['value'])) {
        $valor_pesq = "%" . $dados_requisicao['search']['value'] . "%";
        $list_query->bindValue(':id', $valor_pesq, PDO::PARAM_STR);
        $list_query->bindValue(':name', $valor_pesq, PDO::PARAM_STR);
    }

    $list_query->execute();

    $dados = [];
    while ($row = $list_query->fetch(PDO::FETCH_ASSOC)) {
        extract($row);
        if ($is_adult == 0) {
            $is_adult = '<span class="badge bg-warning w-100 text-dark"> Nao </span>';
        } else {
            $is_adult = '<span class="badge bg-success w-100 text-dark"> sim </span>';
        }

        $acoes = '';

        $acoes .= '<a class="btn btn-sm btn-outline-lightning rounded-0 mr-2" onclick=\'modal_master("api/series.php", "editar_series", "' . $id . '")\'><i class="fa fa-edit"></i></a>';

        $acoes .= '<a class="btn btn-sm btn-outline-lightning rounded-0 mr-2"  data-placement="top" title="Apagar" onclick=\'modal_master("api/series.php", "delete_series", "' . $id . '", "name", "'.$name.'")\'> <i class="far fa-trash-alt text-danger"></i></a>';

        $registros_com_childs_rows = [
            "id"       => $id,
            "name"      => $name,
            "usuario" => "<div class=\"bg-dark-subtle m-auto mb-1 overflow-hidden rounded-2\" style=\"max-width: 70px;\"> <img class=\"img-fluid\" src=\"$cover\" alt=".$name."></div>",
            "indicados" => $category_name,
            "status"      => $stream_type,
            "vencimento"      => $is_adult,

            "acao"     => $acoes
        ];

        $dados[] = $registros_com_childs_rows;
    }

    $resultado = [
        "draw"            => intval($dados_requisicao['draw']),
        "recordsTotal"    => intval($result['qnt_usuarios']),
        "recordsFiltered" => intval($result['qnt_usuarios']),
        "data"            => $dados
    ];

    echo json_encode($resultado);
}