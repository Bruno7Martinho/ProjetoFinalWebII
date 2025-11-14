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
    <link rel="stylesheet" href="css/editar_perfil.css">
</head>
<body>
    <header>
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <h1>Ponto Esportivo</h1>
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