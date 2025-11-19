<?php
session_start();
include_once './config/config.php';
include_once './classes/Noticia.php';
include_once './classes/Usuario.php';

$noticia = new Noticia($db);
$usuario = new Usuario($db);

// Buscar todas as notícias usando o método ler()
$noticias = $noticia->ler();
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SportNews - Portal de Notícias Esportivas</title>
    <link rel="stylesheet" href="css/index.css">
</head>

<body>
    <header>
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <h1>Ponto Esportivo</h1>
                </div>
                <div class="nav-links">
                    <?php if (isset($_SESSION['usuario_id'])): ?>
                        <a href="meu_painel.php">Meu Painel</a>
                        <a href="logout.php">Sair</a>
                    <?php else: ?>
                        <a href="login.php">Fazer Login</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>

    <main class="container">
        <div class="page-header">
            <h1>Últimas Notícias Esportivas</h1>
            <p>Fique por dentro de todas as novidades do mundo esportivo</p>
        </div>

        <div class="noticias-grid">
            <?php if ($noticias && count($noticias) > 0): ?>
                <?php foreach ($noticias as $noticia_item): ?>
                    <div class="noticia-card">
                        <?php if ($noticia_item['imagem']): ?>
                            <img src="<?php echo $noticia_item['imagem']; ?>" alt="<?php echo htmlspecialchars($noticia_item['titulo']); ?>" class="noticia-imagem">
                        <?php endif; ?>

                        <div class="noticia-content">
                            <h2 class="noticia-titulo"><?php echo htmlspecialchars($noticia_item['titulo']); ?></h2>

                            <div class="noticia-meta">
                                <span class="autor">Por <?php echo htmlspecialchars($noticia_item['autor_nome']); ?></span>
                                <span class="data">em <?php echo date('d/m/Y H:i', strtotime($noticia_item['data'])); ?></span>
                            </div>

                            <div class="noticia-resumo">
                                <?php
                                // Limitar o texto para mostrar um resumo
                                $texto = $noticia_item['noticia'];
                                if (strlen($texto) > 150) {
                                    echo htmlspecialchars(substr($texto, 0, 150)) . '...';
                                } else {
                                    echo htmlspecialchars($texto);
                                }
                                ?>
                            </div>

                            <div class="noticia-actions">
                                <a href="noticias.php?id=<?php echo $noticia_item['id']; ?>" class="btn-lermais">
                                    Ler Notícia Completa
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="sem-noticias">
                    <p>Nenhuma notícia encontrada.</p>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> Ponto Esportivo - Portal de Notícias Esportivas. Todos os direitos reservados.</p>
        </div>
    </footer>
</body>

</html>