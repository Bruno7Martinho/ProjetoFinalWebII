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
$mensagem_sucesso = '';
$mensagem_erro = '';

// Buscar todos os usuários
$usuarios = $usuario->ler();

// Processar exclusão de usuário
if (isset($_GET['excluir'])) {
    $usuario_id = $_GET['excluir'];
    
    // Impedir que o admin exclua a si mesmo
    if ($usuario_id == 1) {
        $mensagem_erro = "Você não pode excluir sua própria conta de administrador!";
    } else {
        try {
            if ($usuario->deletar($usuario_id)) {
                $mensagem_sucesso = "Usuário excluído com sucesso!";
                // Recarregar a lista
                header('Location: admin_usuarios.php?sucesso=Usuário excluído com sucesso!');
                exit();
            }
        } catch (Exception $e) {
            $mensagem_erro = $e->getMessage();
        }
    }
}

// Mensagens via GET
$mensagem_sucesso = $_GET['sucesso'] ?? $mensagem_sucesso;
$mensagem_erro = $_GET['erro'] ?? $mensagem_erro;
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Usuários - Admin - SportNews</title>
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
            max-width: 1400px;
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
        
        .admin-badge {
            background: #e63946;
            color: white;
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
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
        
        /* Stats */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 3px 15px rgba(0,0,0,0.1);
        }
        
        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: #e63946;
            margin-bottom: 0.5rem;
        }
        
        .stat-label {
            color: #666;
            font-weight: 600;
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
        
        /* Users Table */
        .users-section {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }
        
        .section-header {
            display: flex;
            justify-content: between;
            align-items: center;
            margin-bottom: 2rem;
            flex-wrap: wrap;
            gap: 1rem;
        }
        
        .section-header h3 {
            color: #1a1a2e;
            font-size: 1.5rem;
        }
        
        .search-box {
            display: flex;
            gap: 0.5rem;
        }
        
        .search-input {
            padding: 0.5rem 1rem;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1rem;
        }
        
        .btn-search {
            background: #e63946;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            cursor: pointer;
        }
        
        .users-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }
        
        .users-table th {
            background: #1a1a2e;
            color: white;
            padding: 1rem;
            text-align: left;
            font-weight: 600;
        }
        
        .users-table td {
            padding: 1rem;
            border-bottom: 1px solid #eee;
        }
        
        .users-table tr:hover {
            background: #f8f9fa;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #e63946, #d62839);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 1.2rem;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .user-details h4 {
            color: #1a1a2e;
            margin-bottom: 0.2rem;
        }
        
        .user-details p {
            color: #666;
            font-size: 0.9rem;
        }
        
        .admin-tag {
            background: #e63946;
            color: white;
            padding: 0.2rem 0.6rem;
            border-radius: 12px;
            font-size: 0.7rem;
            font-weight: 600;
        }
        
        .action-buttons {
            display: flex;
            gap: 0.5rem;
        }
        
        .btn {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 6px;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
        }
        
        .btn-edit {
            background: #ffc107;
            color: #212529;
        }
        
        .btn-edit:hover {
            background: #e0a800;
        }
        
        .btn-delete {
            background: #dc3545;
            color: white;
        }
        
        .btn-delete:hover {
            background: #c82333;
        }
        
        .btn-disabled {
            background: #6c757d;
            color: white;
            cursor: not-allowed;
            opacity: 0.6;
        }
        
        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 3rem;
            color: #666;
        }
        
        .empty-state h3 {
            margin-bottom: 1rem;
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
            
            .users-table {
                font-size: 0.9rem;
            }
            
            .action-buttons {
                flex-direction: column;
            }
            
            .user-info {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.5rem;
            }
            
            .section-header {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .search-box {
                width: 100%;
            }
            
            .search-input {
                flex: 1;
            }
        }
        
        @media (max-width: 480px) {
            .users-table {
                display: block;
                overflow-x: auto;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <h1>⚽ SportNews Admin</h1>
                </div>
                <div class="nav-links">
                    <span class="admin-badge">👑 ADMIN</span>
                    <a href="index.php">📰 Site</a>
                    <a href="portal.php">📊 Painel</a>
                    <a href="admin_usuarios.php">👥 Usuários</a>
                    <a href="logout.php">🚪 Sair</a>
                </div>
            </div>
        </div>
    </header>

    <main class="container">
        <div class="page-header">
            <h2>👥 Gerenciar Usuários</h2>
            <p>Painel administrativo - Gerencie todos os usuários do sistema</p>
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

        <!-- Estatísticas -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?php echo count($usuarios); ?></div>
                <div class="stat-label">Total de Usuários</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">1</div>
                <div class="stat-label">Administradores</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo count($usuarios) - 1; ?></div>
                <div class="stat-label">Usuários Comuns</div>
            </div>
        </div>

        <!-- Lista de Usuários -->
        <div class="users-section">
            <div class="section-header">
                <h3>📋 Lista de Usuários</h3>
                <div class="search-box">
                    <input type="text" class="search-input" placeholder="Buscar usuário...">
                    <button class="btn-search">🔍</button>
                </div>
            </div>

            <?php if (empty($usuarios)): ?>
                <div class="empty-state">
                    <h3>📭 Nenhum usuário cadastrado</h3>
                    <p>Não há usuários no sistema além de você.</p>
                </div>
            <?php else: ?>
                <table class="users-table">
                    <thead>
                        <tr>
                            <th>Usuário</th>
                            <th>Contato</th>
                            <th>Sexo</th>
                            <th>Data de Cadastro</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($usuarios as $user): ?>
                        <tr>
                            <td>
                                <div class="user-info">
                                    <div class="user-avatar">
                                        <?php echo strtoupper(substr($user['nome'], 0, 1)); ?>
                                    </div>
                                    <div class="user-details">
                                        <h4>
                                            <?php echo htmlspecialchars($user['nome']); ?>
                                            <?php if ($user['id'] == 1): ?>
                                                <span class="admin-tag">ADMIN</span>
                                            <?php endif; ?>
                                        </h4>
                                        <p>ID: <?php echo $user['id']; ?></p>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?><br>
                                <strong>Telefone:</strong> <?php echo $user['fone'] ? htmlspecialchars($user['fone']) : 'Não informado'; ?>
                            </td>
                            <td>
                                <?php 
                                switch($user['sexo']) {
                                    case 'M': echo '♂️ Masculino'; break;
                                    case 'F': echo '♀️ Feminino'; break;
                                    case 'O': echo '⚤ Outro'; break;
                                    default: echo 'Não informado';
                                }
                                ?>
                            </td>
                            <td>
                                <?php echo date('d/m/Y H:i', strtotime($user['data_criacao'])); ?>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="editar_perfil.php?id=<?php echo $user['id']; ?>" class="btn btn-edit">
                                        ✏️ Editar
                                    </a>
                                    <?php if ($user['id'] == 1): ?>
                                        <button class="btn btn-disabled" disabled>
                                            🚫 Excluir
                                        </button>
                                    <?php else: ?>
                                        <a href="admin_usuarios.php?excluir=<?php echo $user['id']; ?>" 
                                           class="btn btn-delete"
                                           onclick="return confirm('Tem certeza que deseja excluir o usuário <?php echo htmlspecialchars($user['nome']); ?>? Esta ação não pode ser desfeita!')">
                                            🗑️ Excluir
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <!-- Ações Rápidas -->
        <div class="users-section">
            <h3 style="color: #1a1a2e; margin-bottom: 1.5rem;">⚡ Ações Rápidas</h3>
            <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                <a href="portal.php" class="btn" style="background: #6c757d; color: white; text-decoration: none;">
                    📊 Voltar ao Painel
                </a>
                <a href="index.php" class="btn" style="background: #28a745; color: white; text-decoration: none;">
                    📰 Ver Site Público
                </a>
                <a href="nova_noticia.php" class="btn" style="background: #007bff; color: white; text-decoration: none;">
                    📝 Nova Notícia
                </a>
            </div>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> SportNews - Painel Administrativo. Todos os direitos reservados.</p>
        </div>
    </footer>

    <script>
        // Busca em tempo real
        document.querySelector('.search-input').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const rows = document.querySelectorAll('.users-table tbody tr');
            
            rows.forEach(row => {
                const userName = row.querySelector('.user-details h4').textContent.toLowerCase();
                const userEmail = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
                
                if (userName.includes(searchTerm) || userEmail.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html>