<?php
session_start();
include_once './config/config.php';
include_once './classes/Usuario.php';

// Verificar se usuário já está logado
if (isset($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit();
}

$usuario = new Usuario($db);
$mensagem_erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $senha = $_POST['senha'];
    
    if (empty($email) || empty($senha)) {
        $mensagem_erro = "Por favor, preencha todos os campos!";
    } else {
        try {
            $dados_usuario = $usuario->login($email, $senha);
            if ($dados_usuario) {
                $_SESSION['usuario_id'] = $dados_usuario['id'];
                $_SESSION['usuario_nome'] = $dados_usuario['nome'];
                $_SESSION['usuario_email'] = $dados_usuario['email'];
                
                header('Location: index.php');
                exit();
            } else {
                $mensagem_erro = "Email ou senha incorretos!";
            }
        } catch (Exception $e) {
            $mensagem_erro = "Erro ao fazer login: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Portal de Notícias</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        .login-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 400px;
            overflow: hidden;
        }
        .login-header {
            background: #667eea;
            color: white;
            padding: 20px;
            text-align: center;
        }
        .login-header h2 {
            margin-bottom: 5px;
        }
        .login-form {
            padding: 30px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #333;
        }
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        input[type="email"]:focus,
        input[type="password"]:focus {
            border-color: #667eea;
            outline: none;
        }
        .btn-login {
            width: 100%;
            background: #667eea;
            color: white;
            border: none;
            padding: 15px;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s;
        }
        .btn-login:hover {
            background: #5a6fd8;
        }
        .mensagem-erro {
            background: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            border: 1px solid #f5c6cb;
        }
        .links {
            text-align: center;
            margin-top: 20px;
        }
        .links a {
            color: #667eea;
            text-decoration: none;
            display: block;
            margin: 5px 0;
        }
        .links a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h2>🔐 Login</h2>
            <p>Acesse sua conta</p>
        </div>
        
        <div class="login-form">
            <?php if ($mensagem_erro): ?>
                <div class="mensagem-erro">
                    <?php echo htmlspecialchars($mensagem_erro); ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" 
                           required 
                           placeholder="seu@email.com">
                </div>
                
                <div class="form-group">
                    <label for="senha">Senha:</label>
                    <input type="password" 
                           id="senha" 
                           name="senha" 
                           required 
                           placeholder="Sua senha">
                </div>
                
                <button type="submit" name="login" value="login" class="btn-login">
                    Entrar
                </button>
                
                <div class="links">
                    <p>Não tem uma conta? <a href="./registrar.php">Cadastre-se aqui</a></p>
                </div>
            </form>
        </div>
    </div>
</body>
</html>