<?php
session_start();
include_once './config/config.php';
include_once './classes/Usuario.php';

// Verificar se usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}

$usuario = new Usuario($db);
$mensagem_erro = '';
$mensagem_sucesso = '';

// Buscar dados do usuário logado
$dados_usuario = $usuario->lerPorId($_SESSION['usuario_id']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['atualizar_perfil'])) {
        $nome = trim($_POST['nome']);
        $sexo = $_POST['sexo'];
        $fone = trim($_POST['fone']);
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        
        // Validações
        if (empty($nome) || empty($sexo) || empty($email)) {
            $mensagem_erro = "Por favor, preencha todos os campos obrigatórios!";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $mensagem_erro = "Por favor, insira um email válido!";
        } else {
            try {
                $resultado = $usuario->atualizar($_SESSION['usuario_id'], $nome, $sexo, $fone, $email);
                
                if ($resultado) {
                    $mensagem_sucesso = "Perfil atualizado com sucesso!";
                    // Atualizar dados na sessão
                    $_SESSION['usuario_nome'] = $nome;
                    $_SESSION['usuario_email'] = $email;
                    // Recarregar dados do usuário
                    $dados_usuario = $usuario->lerPorId($_SESSION['usuario_id']);
                }
            } catch (Exception $e) {
                $mensagem_erro = $e->getMessage();
            }
        }
    }
    
    if (isset($_POST['alterar_senha'])) {
        $senha_atual = $_POST['senha_atual'];
        $nova_senha = $_POST['nova_senha'];
        $confirmar_senha = $_POST['confirmar_senha'];
        
        // Validações
        if (empty($senha_atual) || empty($nova_senha) || empty($confirmar_senha)) {
            $mensagem_erro = "Por favor, preencha todos os campos de senha!";
        } elseif (strlen($nova_senha) < 6) {
            $mensagem_erro = "A nova senha deve ter pelo menos 6 caracteres!";
        } elseif ($nova_senha !== $confirmar_senha) {
            $mensagem_erro = "As novas senhas não coincidem!";
        } elseif (!$usuario->verificarCredenciais($dados_usuario['email'], $senha_atual)) {
            $mensagem_erro = "Senha atual incorreta!";
        } else {
            try {
                $resultado = $usuario->alterarSenha($_SESSION['usuario_id'], $nova_senha);
                
                if ($resultado) {
                    $mensagem_sucesso = "Senha alterada com sucesso!";
                    // Limpar campos de senha
                    $senha_atual = $nova_senha = $confirmar_senha = '';
                }
            } catch (Exception $e) {
                $mensagem_erro = $e->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Perfil - SportNews</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            min-height: 100vh;
            color: #333;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        /* Header */
        header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 20px rgba(0,0,0,0.1);
            padding: 1rem 0;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo h1 {
            color: #e63946;
            font-size: 1.8rem;
            font-weight: 700;
        }
        
        .nav-links {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }
        
        .nav-links a {
            color: #555;
            text-decoration: none;
            font-weight: 500;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        
        .nav-links a:hover {
            background: #e63946;
            color: white;
        }
        
        /* Main Content */
        main {
            padding: 2rem 0;
        }
        
        .page-header {
            text-align: center;
            margin-bottom: 3rem;
        }
        
        .page-header h2 {
            color: white;
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
        }
        
        .page-header p {
            color: rgba(255,255,255,0.8);
            font-size: 1.1rem;
        }
        
        /* Profile Container */
        .profile-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            margin-bottom: 2rem;
        }
        
        @media (max-width: 768px) {
            .profile-container {
                grid-template-columns: 1fr;
            }
        }
        
        .form-section {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        
        .form-section h3 {
            color: #1a1a2e;
            margin-bottom: 1.5rem;
            font-size: 1.5rem;
            border-bottom: 2px solid #e63946;
            padding-bottom: 0.5rem;
        }
        
        /* Messages */
        .alert {
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 2rem;
            border: none;
            font-weight: 500;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border-left: 4px solid #28a745;
        }
        
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border-left: 4px solid #dc3545;
        }
        
        /* Form */
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #1a1a2e;
        }
        
        .required::after {
            content: " *";
            color: #e63946;
        }
        
        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }
        
        .form-control:focus {
            border-color: #e63946;
            outline: none;
            background: white;
            box-shadow: 0 0 0 3px rgba(230, 57, 70, 0.1);
        }
        
        .radio-group {
            display: flex;
            gap: 1.5rem;
            margin-top: 0.5rem;
        }
        
        .radio-option {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .radio-option input[type="radio"] {
            margin: 0;
            transform: scale(1.2);
        }
        
        .radio-option label {
            margin: 0;
            font-weight: normal;
            cursor: pointer;
        }
        
        .form-text {
            color: #666;
            font-size: 0.9rem;
            margin-top: 0.5rem;
        }
        
        /* Buttons */
        .form-actions {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }
        
        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .btn-primary {
            background: #e63946;
            color: white;
        }
        
        .btn-primary:hover {
            background: #d62839;
            transform: translateY(-2px);
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #5a6268;
        }
        
        /* User Info */
        .user-info {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .user-avatar {
            width: 120px;
            height: 120px;
            background: linear-gradient(135deg, #e63946, #d62839);
            border-radius: 50%;
            margin: 0 auto 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            color: white;
            font-weight: bold;
        }
        
        .user-details {
            text-align: left;
        }
        
        .user-detail {
            margin-bottom: 1rem;
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 8px;
        }
        
        .user-detail strong {
            color: #1a1a2e;
            display: block;
            margin-bottom: 0.3rem;
        }
        
        .user-detail span {
            color: #666;
        }
        
        /* Footer */
        footer {
            background: rgba(255, 255, 255, 0.9);
            padding: 2rem 0;
            margin-top: 3rem;
            text-align: center;
            color: #666;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                gap: 1rem;
            }
            
            .nav-links {
                flex-wrap: wrap;
                justify-content: center;
            }
            
            .page-header h2 {
                font-size: 2rem;
            }
            
            .form-actions {
                flex-direction: column;
            }
            
            .btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <h1>⚽ SportNews</h1>
                </div>
                <div class="nav-links">
                    <a href="index.php">Página Inicial</a>
                    <a href="portal.php">Meu Painel</a>
                    <a href="nova_noticia.php">+ Nova Notícia</a>
                    <a href="logout.php">Sair</a>
                </div>
            </div>
        </div>
    </header>

    <main class="container">
        <div class="page-header">
            <h2>👤 Editar Perfil</h2>
            <p>Gerencie suas informações pessoais</p>
        </div>
        
        <?php if ($mensagem_sucesso): ?>
            <div class="alert alert-success">
                ✅ <?php echo htmlspecialchars($mensagem_sucesso); ?>
            </div>
        <?php endif; ?>

        <?php if ($mensagem_erro): ?>
            <div class="alert alert-error">
                ❌ <?php echo htmlspecialchars($mensagem_erro); ?>
            </div>
        <?php endif; ?>

        <div class="profile-container">
            <!-- Informações do Usuário -->
            <div class="user-info">
                <div class="user-avatar">
                    <?php echo strtoupper(substr($dados_usuario['nome'], 0, 1)); ?>
                </div>
                <h3><?php echo htmlspecialchars($dados_usuario['nome']); ?></h3>
                <p style="color: #666; margin-bottom: 2rem;">Membro desde <?php echo date('d/m/Y', strtotime($dados_usuario['data_criacao'])); ?></p>
                
                <div class="user-details">
                    <div class="user-detail">
                        <strong>📧 Email</strong>
                        <span><?php echo htmlspecialchars($dados_usuario['email']); ?></span>
                    </div>
                    <div class="user-detail">
                        <strong>📞 Telefone</strong>
                        <span><?php echo $dados_usuario['fone'] ? htmlspecialchars($dados_usuario['fone']) : 'Não informado'; ?></span>
                    </div>
                    <div class="user-detail">
                        <strong>⚤ Sexo</strong>
                        <span>
                            <?php 
                            switch($dados_usuario['sexo']) {
                                case 'M': echo 'Masculino'; break;
                                case 'F': echo 'Feminino'; break;
                                case 'O': echo 'Outro'; break;
                                default: echo 'Não informado';
                            }
                            ?>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Formulário de Edição -->
            <div class="form-section">
                <h3>Editar Informações</h3>
                <form method="POST">
                    <div class="form-group">
                        <label for="nome" class="form-label required">Nome Completo</label>
                        <input type="text" 
                               class="form-control" 
                               id="nome" 
                               name="nome" 
                               value="<?php echo htmlspecialchars($dados_usuario['nome']); ?>" 
                               required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label required">Sexo</label>
                        <div class="radio-group">
                            <div class="radio-option">
                                <input type="radio" id="sexo_m" name="sexo" value="M" 
                                       <?php echo ($dados_usuario['sexo'] == 'M') ? 'checked' : ''; ?> required>
                                <label for="sexo_m">Masculino</label>
                            </div>
                            <div class="radio-option">
                                <input type="radio" id="sexo_f" name="sexo" value="F"
                                       <?php echo ($dados_usuario['sexo'] == 'F') ? 'checked' : ''; ?>>
                                <label for="sexo_f">Feminino</label>
                            </div>
                            <div class="radio-option">
                                <input type="radio" id="sexo_o" name="sexo" value="O"
                                       <?php echo ($dados_usuario['sexo'] == 'O') ? 'checked' : ''; ?>>
                                <label for="sexo_o">Outro</label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="fone" class="form-label">Telefone</label>
                        <input type="tel" 
                               class="form-control" 
                               id="fone" 
                               name="fone" 
                               value="<?php echo $dados_usuario['fone'] ? htmlspecialchars($dados_usuario['fone']) : ''; ?>" 
                               placeholder="(11) 99999-9999">
                    </div>
                    
                    <div class="form-group">
                        <label for="email" class="form-label required">Email</label>
                        <input type="email" 
                               class="form-control" 
                               id="email" 
                               name="email" 
                               value="<?php echo htmlspecialchars($dados_usuario['email']); ?>" 
                               required>
                    </div>
                    
                    <div class="form-actions">
                        <a href="meu_painel.php" class="btn btn-secondary">Cancelar</a>
                        <button type="submit" name="atualizar_perfil" class="btn btn-primary">
                            Salvar Alterações
                        </button>
                    </div>
                </form>
            </div>

            <!-- Alterar Senha -->
            <div class="form-section">
                <h3>Alterar Senha</h3>
                <form method="POST">
                    <div class="form-group">
                        <label for="senha_atual" class="form-label required">Senha Atual</label>
                        <input type="password" 
                               class="form-control" 
                               id="senha_atual" 
                               name="senha_atual" 
                               required>
                    </div>
                    
                    <div class="form-group">
                        <label for="nova_senha" class="form-label required">Nova Senha</label>
                        <input type="password" 
                               class="form-control" 
                               id="nova_senha" 
                               name="nova_senha" 
                               required>
                        <div class="form-text">Mínimo 6 caracteres</div>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirmar_senha" class="form-label required">Confirmar Nova Senha</label>
                        <input type="password" 
                               class="form-control" 
                               id="confirmar_senha" 
                               name="confirmar_senha" 
                               required>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" name="alterar_senha" class="btn btn-primary">
                            Alterar Senha
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> SportNews - Portal de Notícias Esportivas. Todos os direitos reservados.</p>
        </div>
    </footer>
</body>
</html>