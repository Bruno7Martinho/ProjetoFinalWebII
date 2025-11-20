<?php
session_start();
include_once './config/config.php';
include_once './classes/Usuario.php';
include_once './classes/Noticia.php';

// Verificar se usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}

$usuario = new Usuario($db);
$noticia = new Noticia($db);

// Buscar dados do usuário logado
$dados_usuario = $usuario->lerPorId($_SESSION['usuario_id']);

// Buscar notícias do usuário
$noticias_usuario = $noticia->lerPorAutor($_SESSION['usuario_id']);

// Processar exclusão de notícia
if (isset($_GET['excluir'])) {
    $noticia_id = $_GET['excluir'];
    
    // Verificar se a notícia pertence ao usuário
    if ($noticia->isAutor($noticia_id, $_SESSION['usuario_id'])) {
        if ($noticia->deletar($noticia_id)) {
            $mensagem_sucesso = "Notícia excluída com sucesso!";
        } else {
            $mensagem_erro = "Erro ao excluir notícia!";
        }
        // Recarregar a página
        header('Location: index.php');
        exit();
    } else {
        $mensagem_erro = "Você não tem permissão para excluir esta notícia!";
    }
}

// Mensagens
$mensagem_sucesso = $_GET['sucesso'] ?? '';
$mensagem_erro = $_GET['erro'] ?? '';

// Verificar se é admin
$is_admin = ($_SESSION['usuario_id'] == 1);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal - Meu Painel</title>
    <link rel="stylesheet" href="css/meu_painel.css">
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
                    
                    <?php if ($is_admin): ?>
                        <a href="nova_noticia.php">+ Nova Notícia</a>
                    <?php endif; ?>
                
                    
                    <?php if ($is_admin): ?>
                        <a href="admin_usuarios.php">Gerenciar Usuários</a>
                    <?php endif; ?>

                     <span class="user-welcome">👋 Olá, <?php echo htmlspecialchars($dados_usuario['nome']); ?></span>
                    <a href="logout.php">Sair</a>
                </div>
            </div>
        </div>
    </header>

    <main class="container">
        <div class="dashboard-header">
            <h2>Meu Perfil</h2>
            <p>Gerencie seu perfil</p>
        </div>
        

        <section class="section">
            <div class="quick-actions">
                <a href="editar_perfil.php" class="action-card">
                    <div style="font-size: 2rem; margin-bottom: 0.5rem;">👤</div>
                    <strong>Editar Meu Perfil</strong>
                </a>
            </div>
        </section>


    </main>

    <footer>
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> Ponto Esportivo. Todos os direitos reservados.</p>
        </div>
    </footer>
</body>
</html>