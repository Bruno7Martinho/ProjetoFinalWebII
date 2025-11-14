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
    <link rel="stylesheet" href="css/login.css">
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h2>Seja Bem vindo ao Ponto Esportivo</h2>
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
                
            </form>
        </div>
    </div>
</body>
</html>