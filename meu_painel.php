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
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal - Meu Painel</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            color: #333;
            line-height: 1.6;
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
            color: #667eea;
            font-size: 1.8rem;
            font-weight: 700;
        }
        
        .nav-links {
            display: flex;
            align-items: center;
            gap: 2rem;
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
            background: #667eea;
            color: white;
            transform: translateY(-2px);
        }
        
        .user-welcome {
            color: black;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-weight: 600;
        }
        
        /* Main Content */
        main {
            padding: 2rem 0;
        }
        
        .dashboard-header {
            text-align: center;
            margin-bottom: 3rem;
        }
        
        .dashboard-header h2 {
            color: white;
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        
        .dashboard-header p {
            color: rgba(255,255,255,0.8);
            font-size: 1.1rem;
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
        
        /* Sections */
        .section {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        
        .section:hover {
            transform: translateY(-5px);
        }
        
        .section h3 {
            color: #667eea;
            margin-bottom: 1.5rem;
            font-size: 1.5rem;
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 0.5rem;
        }
        
        /* Table */
        .news-table {
            width: 100%;
            border-collapse: collapse;
            margin: 1rem 0;
        }
        
        .news-table th {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 1rem;
            text-align: left;
            font-weight: 600;
        }
        
        .news-table td {
            padding: 1rem;
            border-bottom: 1px solid #eee;
        }
        
        .news-table tr:hover {
            background: #f8f9fa;
        }
        
        .action-links {
            display: flex;
            gap: 1rem;
        }
        
        .action-links a {
            color: #667eea;
            text-decoration: none;
            padding: 0.3rem 0.8rem;
            border: 1px solid #667eea;
            border-radius: 5px;
            transition: all 0.3s ease;
            font-size: 0.9rem;
        }
        
        .action-links a:hover {
            background: #667eea;
            color: white;
        }
        
        /* Quick Actions */
        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
        }
        
        .action-card {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 1.5rem;
            border-radius: 10px;
            text-decoration: none;
            transition: all 0.3s ease;
            text-align: center;
        }
        
        .action-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        }
        
        /* Stats */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }
        
        .stat-item {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 10px;
            text-align: center;
            border-left: 4px solid #667eea;
        }
        
        .stat-item strong {
            display: block;
            font-size: 1.2rem;
            color: #667eea;
            margin-bottom: 0.5rem;
        }
        
        /* Footer */
        footer {
            background: rgba(255, 255, 255, 0.9);
            padding: 2rem 0;
            margin-top: 3rem;
            text-align: center;
            color: #666;
        }
        
        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 3rem;
            color: #666;
        }
        
        .empty-state a {
            display: inline-block;
            background: #667eea;
            color: white;
            padding: 0.8rem 1.5rem;
            border-radius: 8px;
            text-decoration: none;
            margin-top: 1rem;
            transition: all 0.3s ease;
        }
        
        .empty-state a:hover {
            background: #5a6fd8;
            transform: translateY(-2px);
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
            
            .news-table {
                font-size: 0.9rem;
            }
            
            .action-links {
                flex-direction: column;
                gap: 0.5rem;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <h1>Portal de Notícias</h1>
                </div>
                <div class="nav-links">
                    <a href="index.php">Página Inicial</a>
                    <a href="nova_noticia.php">+ Nova Notícia</a>
                    <span class="user-welcome">👋 Olá, <?php echo htmlspecialchars($dados_usuario['nome']); ?></span>
                    <a href="logout.php">Sair</a>
                    <?php if ($_SESSION['usuario_id'] == 1): ?>
        <a href="admin_usuarios.php">👑 Gerenciar Usuários</a>
    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>

    <main class="container">
        <div class="dashboard-header">
            <h2>Meu Painel</h2>
            <p>Gerencie suas notícias e acompanhe suas estatísticas</p>
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

        <section class="section">
            <h3>Minhas Notícias</h3>
            
            <?php if (empty($noticias_usuario)): ?>
                <div class="empty-state">
                    <p>📭 Você ainda não publicou nenhuma notícia.</p>
                    <a href="nova_noticia.php">➕ Criar Primeira Notícia</a>
                </div>
            <?php else: ?>
                <table class="news-table">
                    <thead>
                        <tr>
                            <th>Título</th>
                            <th>Data de Publicação</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($noticias_usuario as $noticia_item): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($noticia_item['titulo']); ?></strong></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($noticia_item['data'])); ?></td>
                            <td>
                                <div class="action-links">
                                    <a href="noticia.php?id=<?php echo $noticia_item['id']; ?>">Ver</a>
                                    <a href="editar_noticia.php?id=<?php echo $noticia_item['id']; ?>">Editar</a>
                                    <a href="meu_painel.php?excluir=<?php echo $noticia_id; ?>" ...>Excluir</a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                
                <div style="text-align: center; margin-top: 1rem;">
                    <strong>Total: <?php echo count($noticias_usuario); ?> notícia(s)</strong>
                </div>
            <?php endif; ?>
        </section>

        <section class="section">
            <h3>Funcionalidades</h3>
            <div class="quick-actions">
                <a href="nova_noticia.php" class="action-card">
                    <div style="font-size: 2rem; margin-bottom: 0.5rem;">📝</div>
                    <strong>Publicar Nova Notícia</strong>
                </a>
                
                <a href="editar_perfil.php" class="action-card">
                    <div style="font-size: 2rem; margin-bottom: 0.5rem;">👤</div>
                    <strong>Editar Meu Perfil</strong>
                </a>
            </div>
        </section>

    </main>

    <footer>
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> Portal de Notícias. Todos os direitos reservados.</p>
        </div>
    </footer>
</body>
</html>