<?php
session_start();
include_once './config/config.php';
include_once './classes/Noticia.php';
include_once './classes/Usuario.php';

// Verifica se foi passado um ID na URL
if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit();
}

$noticia = new Noticia($db);
$usuario = new Usuario($db);

// Pega o ID da notícia da URL
$noticia_id = $_GET['id'];

// Buscar dados da notícia
$noticia_dados = $noticia->lerPorId($noticia_id);

// Verifica se a notícia existe
if (!$noticia_dados) {
    header('Location: index.php');
    exit();
}

// Processar exclusão se for solicitado
if (isset($_GET['excluir']) && $_GET['excluir'] == 'confirmar') {
    // Verificar se o usuário está logado e é o autor
    if (isset($_SESSION['usuario_id']) && $_SESSION['usuario_id'] == $noticia_dados['autor']) {
        if ($noticia->deletar($noticia_id)) {
            header('Location: index.php?sucesso=Notícia excluída com sucesso');
            exit();
        } else {
            $mensagem_erro = "Erro ao excluir notícia!";
        }
    } else {
        $mensagem_erro = "Você não tem permissão para excluir esta notícia!";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($noticia_dados['titulo']); ?> - SportNews</title>
    <link rel="stylesheet" href="css/noticia.css">
</head>
<body>
    <header>
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <h1>Ponto Esportivo</h1>
                </div>
                <div class="nav-links">
                    <a href="index.php">Voltar para Notícias</a>
                    <?php if (isset($_SESSION['usuario_id'])): ?>
                        <a href="meu_painel.php">Meu Painel</a>
                    <?php else: ?>
                        <a href="login.php">Fazer Login</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>

    <main class="container">
        <article class="noticia-container">
            <h1 class="noticia-titulo"><?php echo htmlspecialchars($noticia_dados['titulo']); ?></h1>
            
            <div class="noticia-meta">
                <strong>📝 Autor:</strong> <?php echo htmlspecialchars($noticia_dados['autor_nome']); ?> | 
                <strong>📅 Publicado em:</strong> <?php echo date('d/m/Y H:i', strtotime($noticia_dados['data'])); ?>
            </div>

            <?php if ($noticia_dados['imagem']): ?>
                <img src="<?php echo $noticia_dados['imagem']; ?>" alt="<?php echo htmlspecialchars($noticia_dados['titulo']); ?>" class="noticia-imagem">
            <?php endif; ?>

            <div class="noticia-conteudo">
                <?php echo nl2br(htmlspecialchars($noticia_dados['noticia'])); ?>
            </div>

            <a href="index.php" class="btn-voltar">Voltar para Notícias</a>

            <?php if (isset($_SESSION['usuario_id']) && $_SESSION['usuario_id'] == $noticia_dados['autor']): ?>
                <div style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid #eee;">
                    <h3 style="color: #1a1a2e; margin-bottom: 1rem;">Ações do Autor</h3>
                    <a href="editar_noticia.php?id=<?php echo $noticia_dados['id']; ?>" style="background: #ffc107; color: #212529; padding: 0.7rem 1.5rem; border-radius: 8px; text-decoration: none; margin-right: 1rem;">✏️ Editar</a>
                    <a href="noticia.php?id=<?php echo $noticia_dados['id']; ?>&excluir=confirmar" 
                       style="background: #dc3545; color: white; padding: 0.7rem 1.5rem; border-radius: 8px; text-decoration: none;" 
                       onclick="return confirm('Tem certeza que deseja excluir esta notícia?')">🗑️ Excluir</a>
                </div>
            <?php endif; ?>

            <?php if (isset($mensagem_erro)): ?>
                <div style="background: #f8d7da; color: #721c24; padding: 1rem; border-radius: 8px; margin-top: 1rem; border-left: 4px solid #dc3545;">
                    ❌ <?php echo htmlspecialchars($mensagem_erro); ?>
                </div>
            <?php endif; ?>
        </article>
    </main>

    <footer>
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> SportNews - Portal de Notícias Esportivas. Todos os direitos reservados.</p>
        </div>
    </footer>
</body>
</html>