<?php
function login($username, $password)
{
    $conexao = conectar_bd();
    $username = preg_replace('/[^a-zA-Z0-9#@!%&*]/', '', $username);
    $password = preg_replace('/[^a-zA-Z0-9#@!%&*]/', '', $password);

    if (empty($username) || empty($password)) {
        $resposta = [
            'title' => 'Usuário ou senha inválidos.',
            'icon' => 'error'
        ];
        return $resposta;
    }

    $stmt = $conexao->prepare('SELECT * FROM admin WHERE user = :username AND pass = :password');
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':password', $password);
    $stmt->execute();
    $admin_login = $stmt->fetch();

    if (!$admin_login) {
        $resposta = [
            'title' => 'Usuário ou senha incorretos.',
            'icon' => 'error'
        ];
        return $resposta;
    }

        $token = bin2hex(random_bytes(32));

        session_start();
        $_SESSION['logged_in_fxtream'] = true;
        $_SESSION['token'] = $token;
        $_SESSION['admin_id'] = $admin_login['id'];
        $_SESSION['nivel_admin'] = $admin_login['admin'];
        $_SESSION['plano_admin'] = $admin_login['plano'];
        $_SESSION['username'] = $username;
        $_SESSION['password'] = $password;
        $_SESSION['last_activity'] = time(); 

        $resposta = [
            'title' => 'Login efetuado com sucesso ',
            'url' => 'clientes.php',
            'time' => '100',
            'icon' => 'success'
        ];
        $sql_update = "UPDATE admin SET token = '$token' WHERE user = '$username' and pass = '$password'";
        $conexao->exec($sql_update);

    return $resposta;
}