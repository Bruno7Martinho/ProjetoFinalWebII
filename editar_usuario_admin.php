<?php
session_start();
include_once './config/config.php';
include_once './classes/Usuario.php';

// Verificar se usuário está logado e é admin
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}

// Verificar se é admin (ID 1 é o admin)
if ($_SESSION['usuario_id'] != 1) {
    header('Location: portal.php');
    exit();
}

$usuario = new Usuario($db);
$mensagem_erro = '';
$mensagem_sucesso = '';

// Verificar se foi passado um ID na URL
if (!isset($_GET['id'])) {
    header('Location: admin_usuarios.php');
    exit();
}

$usuario_id = $_GET['id'];

// Buscar dados do usuário a ser editado
$usuario_editar = $usuario->lerPorId($usuario_id);

if (!$usuario_editar) {
    header('Location: admin_usuarios.php?erro=Usuário não encontrado');
    exit();
}

// Processar atualização do usuário
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['atualizar_usuario'])) {
    $nome = trim($_POST['nome']);
    $sexo = $_POST['sexo'];
    $fone = trim($_POST['fone']);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $nova_senha = $_POST['nova_senha'];
    
    // Validações
    if (empty($nome) || empty($sexo) || empty($email)) {
        $mensagem_erro = "Por favor, preencha todos os campos obrigatórios!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $mensagem_erro = "Por favor, insira um email válido!";
    } elseif ($nova_senha && strlen($nova_senha) < 6) {
        $mensagem_erro = "A nova senha deve ter pelo menos 6 caracteres!";
    } else {
        try {
            // Se foi informada nova senha, atualiza com senha, senão mantém a atual
            if (!empty($nova_senha)) {
                $resultado = $usuario->atualizar($usuario_id, $nome, $sexo, $fone, $email, $nova_senha);
            } else {
                $resultado = $usuario->atualizar($usuario_id, $nome, $sexo, $fone, $email);
            }
            
            if ($resultado) {
                $mensagem_sucesso = "Usuário atualizado com sucesso!";
                // Recarregar dados do usuário
                $usuario_editar = $usuario->lerPorId($usuario_id);
            }
        } catch (Exception $e) {
            $mensagem_erro = $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuário - Admin - Ponto Esportivo</title>
    <link rel="stylesheet" href="css/editar_perfil.css">
    <style>
        .admin-header {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            margin-bottom: 2rem;
        }
        .user-id-badge {
            background: #34495e;
            color: white;
            padding: 0.3rem 0.8rem;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 600;
            margin-left: 1rem;
        }
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1rem;
            color: #3498db;
            text-decoration: none;
            font-weight: 600;
        }
        .back-link:hover {
            color: #2980b9;
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <h1>Ponto Esportivo 👑</h1>
                </div>
                <div class="nav-links">
                    <a href="admin_usuarios.php">← Voltar para Usuários</a>
                    <a href="portal.php">Meu Painel</a>
                    <a href="logout.php">Sair</a>
                </div>
            </div>
        </div>
    </header>

    <main class="container">
        <div class="page-header admin-header">
            <h2>👥 Editar Usuário</h2>
            <p>Editando usuário: <strong><?php echo htmlspecialchars($usuario_editar['nome']); ?></strong>
               <span class="user-id-badge">ID: <?php echo $usuario_editar['id']; ?></span>
            </p>
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
                <div class="user-avatar" style="background: linear-gradient(135deg, #e74c3c, #c0392b);">
                    <?php echo strtoupper(substr($usuario_editar['nome'], 0, 1)); ?>
                </div>
                <h3><?php echo htmlspecialchars($usuario_editar['nome']); ?></h3>
                <p style="color: #666; margin-bottom: 2rem;">Membro desde <?php echo date('d/m/Y', strtotime($usuario_editar['data_criacao'])); ?></p>
                
                <div class="user-details">
                    <div class="user-detail">
                        <strong>🆔 ID do Usuário</strong>
                        <span><?php echo $usuario_editar['id']; ?></span>
                    </div>
                    <div class="user-detail">
                        <strong>📧 Email</strong>
                        <span><?php echo htmlspecialchars($usuario_editar['email']); ?></span>
                    </div>
                    <div class="user-detail">
                        <strong>📞 Telefone</strong>
                        <span><?php echo $usuario_editar['fone'] ? htmlspecialchars($usuario_editar['fone']) : 'Não informado'; ?></span>
                    </div>
                    <div class="user-detail">
                        <strong>⚤ Sexo</strong>
                        <span>
                            <?php 
                            switch($usuario_editar['sexo']) {
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
                <h3>✏️ Editar Informações do Usuário</h3>
                <form method="POST">
                    <div class="form-group">
                        <label for="nome" class="form-label required">Nome Completo</label>
                        <input type="text" 
                               class="form-control" 
                               id="nome" 
                               name="nome" 
                               value="<?php echo htmlspecialchars($usuario_editar['nome']); ?>" 
                               required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label required">Sexo</label>
                        <div class="radio-group">
                            <div class="radio-option">
                                <input type="radio" id="sexo_m" name="sexo" value="M" 
                                       <?php echo ($usuario_editar['sexo'] == 'M') ? 'checked' : ''; ?> required>
                                <label for="sexo_m">Masculino</label>
                            </div>
                            <div class="radio-option">
                                <input type="radio" id="sexo_f" name="sexo" value="F"
                                       <?php echo ($usuario_editar['sexo'] == 'F') ? 'checked' : ''; ?>>
                                <label for="sexo_f">Feminino</label>
                            </div>
                            <div class="radio-option">
                                <input type="radio" id="sexo_o" name="sexo" value="O"
                                       <?php echo ($usuario_editar['sexo'] == 'O') ? 'checked' : ''; ?>>
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
                               value="<?php echo $usuario_editar['fone'] ? htmlspecialchars($usuario_editar['fone']) : ''; ?>" 
                               placeholder="(11) 99999-9999">
                    </div>
                    
                    <div class="form-group">
                        <label for="email" class="form-label required">Email</label>
                        <input type="email" 
                               class="form-control" 
                               id="email" 
                               name="email" 
                               value="<?php echo htmlspecialchars($usuario_editar['email']); ?>" 
                               required>
                    </div>
                    
                    <div class="form-group">
                        <label for="nova_senha" class="form-label">Nova Senha</label>
                        <input type="password" 
                               class="form-control" 
                               id="nova_senha" 
                               name="nova_senha" 
                               placeholder="Deixe em branco para manter a senha atual">
                        <div class="form-text">Mínimo 6 caracteres (opcional)</div>
                    </div>
                    
                    <div class="form-actions">
                        <a href="admin_usuarios.php" class="btn btn-secondary">Cancelar</a>
                        <button type="submit" name="atualizar_usuario" class="btn btn-primary">
                            Atualizar Usuário
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> Ponto Esportivo - Painel Administrativo. Todos os direitos reservados.</p>
        </div>
    </footer>
</body>
</html>