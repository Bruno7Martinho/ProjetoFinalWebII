<?php
session_start();
include_once './config/config.php';
include_once './classes/Noticia.php';
include_once './classes/Usuario.php';

$noticia = new Noticia($db);
$usuario = new Usuario($db);

// Buscar todas as notícias públicas (ordenadas por data, mais recentes primeiro)
$noticias = $noticia->ler();
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal de Notícias - Página Inicial</title>
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
                    <a href="index.php">Início</a>
                    <?php if (isset($_SESSION['usuario_id'])): ?>
                        <a href="meu_painel.php">Meu Perfil</a>
                        <a href="logout.php">Sair</a>
                    <?php else: ?>
                        <a href="login.php" class="btn-login">Fazer Login</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>

    <section class="hero">
        <div class="container">
            <h2>Bem-vindo ao Ponto Esportivo</h2>
            <p>Fique por dentro das últimas notícias e atualizações</p>
        </div>
    </section>

    <main class="container">
        <div class="section-title">
            <h2>Últimas Notícias</h2>

        </div>

        
            
        
            <div class="news-grid">
                <?php foreach ($noticias as $noticia_item): ?>
                    <article class="news-card">
                        <?php if ($noticia_item['imagem']): ?>
                            <div class="news-image">
                                <img src="<?php echo $noticia_item['imagem']; ?>" alt="<?php echo htmlspecialchars($noticia_item['titulo']); ?>">
                            </div>
                        <?php endif; ?>

                        <div class="news-content">
                            <h3 class="news-title"><?php echo htmlspecialchars($noticia_item['titulo']); ?></h3>

                            <p class="news-excerpt">
                                <?php
                                $resumo = strip_tags($noticia_item['noticia']);
                                if (strlen($resumo) > 150) {
                                    echo substr($resumo, 0, 150) . '...';
                                } else {
                                    echo $resumo;
                                }
                                ?>
                            </p>

                            <a href="noticia.php?id=<?php echo $noticia_item['id']; ?>" class="btn-read-more">
                                Ler Notícia Completa
                            </a>

                            <div class="news-meta">
                                <span class="news-author">Por: <?php echo htmlspecialchars($noticia_item['autor_nome']); ?></span>
                                <span class="news-date"><?php echo date('d/m/Y H:i', strtotime($noticia_item['data'])); ?></span>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <


    </main>

    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h4>Entre em Contato</h4>
                    <p>Email: contato@pontoesportivo.com<br>
                        Telefone: (51) 99999-9999<br>
                        Endereço: Sapucaia do Sul, RS</p>
                </div>
            </div>

            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> Ponto Esportivo. Todos os direitos reservados.</p>
            </div>
        </div>
    </footer>
</body>

</html>