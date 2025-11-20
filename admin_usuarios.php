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
    header('Location: meu_painel.php');
    exit();
}

$usuario = new Usuario($db);
$mensagem_sucesso = '';
$mensagem_erro = '';

// Buscar todos os usuários
$usuarios = $usuario->ler();

// Processar busca de usuários
$termo_busca = '';
$usuarios_filtrados = $usuarios;

if (isset($_GET['buscar']) && !empty($_GET['buscar'])) {
    $termo_busca = trim($_GET['buscar']);
    $usuarios_filtrados = array_filter($usuarios, function ($user) use ($termo_busca) {
        // Buscar apenas no nome
        return stripos($user['nome'], $termo_busca) !== false;
    });
}

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
    <title>Gerenciar Usuários - Admin - Ponto Esportivo</title>
    <link rel="stylesheet" href="css/admin_usuarios.css">
</head>

<body>
    <header>
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <h1>Ponto Esportivo</h1>
                </div>
                <div class="nav-links">
                    <span class="admin-badge">👑 ADMIN</span>
                    <a href="index.php">Voltar para a página</a>
                    <a href="registrar.php">Cadastrar Usuários</a>
                    <a href="logout.php">Sair</a>
                </div>
            </div>
        </div>
    </header>

    <main class="container">
        <div class="page-header">
            <h2>Gerenciador de Usuários</h2>
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

        
        <div class="users-section">
            <div class="section-header">
                <h3>Lista de Usuários</h3>
                <form method="GET" class="search-box">
                    <input type="text"
                        class="search-input"
                        name="buscar"
                        placeholder="Buscar por nome..."
                        value="<?php echo htmlspecialchars($termo_busca); ?>">
                    <button type="submit" class="btn-search">🔍</button>
                </form>
            </div>

            <?php if (empty($usuarios_filtrados)): ?>
                <div class="empty-state">
                    <h3>
                        <?php if (!empty($termo_busca)): ?>
                            Nenhum usuário encontrado para "<?php echo htmlspecialchars($termo_busca); ?>"
                        <?php else: ?>
                            Nenhum usuário cadastrado
                        <?php endif; ?>
                    </h3>
                    <p>
                        <?php if (!empty($termo_busca)): ?>
                            Nenhum usuário foi encontrado com esse nome.
                        <?php else: ?>
                            Não há usuários no sistema além de você.
                        <?php endif; ?>
                    </p>
                </div>
            <?php else: ?>
                <?php if (!empty($termo_busca)): ?>
                    <div class="search-results-info">
                        <p>Encontrados <strong><?php echo count($usuarios_filtrados); ?></strong> usuário(s) para "<?php echo htmlspecialchars($termo_busca); ?>"</p>
                    </div>
                <?php endif; ?>

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
                        <?php foreach ($usuarios_filtrados as $user): ?>
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
                                    switch ($user['sexo']) {
                                        case 'M':
                                            echo '♂️ Masculino';
                                            break;
                                        case 'F':
                                            echo '♀️ Feminino';
                                            break;
                                        case 'O':
                                            echo '⚤ Outro';
                                            break;
                                        default:
                                            echo 'Não informado';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php echo date('d/m/Y H:i', strtotime($user['data_criacao'])); ?>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="editar_usuario_admin.php?id=<?php echo $user['id']; ?>" class="btn btn-edit">
                                            ✏️ Editar
                                        </a>
                                        <?php if ($user['id'] == 1): ?>
                                            <button class="btn btn-disabled" disabled>
                                                🚫 Excluir
                                            </button>
                                        <?php else: ?>
                                            <a href="admin_usuarios.php?excluir=<?php echo $user['id']; ?><?php echo !empty($termo_busca) ? '&buscar=' . urlencode($termo_busca) : ''; ?>"
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

    </main>

    <footer>
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> Ponto Esportivo - Painel Administrativo. Todos os direitos reservados.</p>
        </div>
    </footer>
</body>

</html>