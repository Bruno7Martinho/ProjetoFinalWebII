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
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f8f9fa;
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
            background: white;
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
            background: #667eea;
            color: white;
        }
        
        .btn-login {
            background: #667eea;
            color: white;
            padding: 0.7rem 1.5rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-login:hover {
            background: #5a6fd8;
            transform: translateY(-2px);
        }
        
        /* Hero Section */
        .hero {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 4rem 0;
            text-align: center;
            margin-bottom: 3rem;
        }
        
        .hero h2 {
            font-size: 3rem;
            margin-bottom: 1rem;
            font-weight: 700;
        }
        
        .hero p {
            font-size: 1.2rem;
            opacity: 0.9;
            max-width: 600px;
            margin: 0 auto;
        }
        
        /* Main Content */
        main {
            padding: 2rem 0;
        }
        
        .section-title {
            text-align: center;
            margin-bottom: 3rem;
        }
        
        .section-title h2 {
            color: #2c3e50;
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
        }
        
        .section-title p {
            color: #666;
            font-size: 1.1rem;
        }
        
        /* News Grid */
        .news-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }
        
        .news-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }
        
        .news-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.2);
        }
        
        .news-image {
            height: 200px;
            overflow: hidden;
        }
        
        .news-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }
        
        .news-card:hover .news-image img {
            transform: scale(1.1);
        }
        
        .news-content {
            padding: 1.5rem;
        }
        
        .news-title {
            color: #2c3e50;
            font-size: 1.3rem;
            margin-bottom: 1rem;
            line-height: 1.4;
        }
        
        .news-excerpt {
            color: #666;
            margin-bottom: 1.5rem;
            line-height: 1.6;
        }
        
        .news-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 1rem;
            border-top: 1px solid #eee;
        }
        
        .news-author {
            color: #667eea;
            font-weight: 600;
            font-size: 0.9rem;
        }
        
        .news-date {
            color: #999;
            font-size: 0.9rem;
        }
        
        .btn-read-more {
            background: #667eea;
            color: white;
            padding: 0.7rem 1.5rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-block;
        }
        
        .btn-read-more:hover {
            background: #5a6fd8;
            transform: translateY(-2px);
        }
        
        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        
        .empty-state h3 {
            color: #666;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }
        
        .empty-state p {
            color: #999;
            margin-bottom: 2rem;
        }
        
        /* Call to Action */
        .cta-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 3rem;
            border-radius: 15px;
            text-align: center;
            margin: 3rem 0;
        }
        
        .cta-section h3 {
            font-size: 2rem;
            margin-bottom: 1rem;
        }
        
        .cta-section p {
            font-size: 1.1rem;
            margin-bottom: 2rem;
            opacity: 0.9;
        }
        
        .btn-cta {
            background: white;
            color: #667eea;
            padding: 1rem 2rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 700;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            display: inline-block;
        }
        
        .btn-cta:hover {
            background: #f8f9fa;
            transform: translateY(-2px);
        }
        
        /* Footer */
        footer {
            background: #2c3e50;
            color: white;
            padding: 3rem 0;
            margin-top: 4rem;
        }
        
        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }
        
        .footer-section h4 {
            color: #667eea;
            margin-bottom: 1rem;
            font-size: 1.2rem;
        }
        
        .footer-section p {
            color: #ccc;
            line-height: 1.6;
        }
        
        .footer-links {
            list-style: none;
        }
        
        .footer-links li {
            margin-bottom: 0.5rem;
        }
        
        .footer-links a {
            color: #ccc;
            text-decoration: none;
            transition: color 0.3s ease;
        }
        
        .footer-links a:hover {
            color: #667eea;
        }
        
        .footer-bottom {
            text-align: center;
            padding-top: 2rem;
            border-top: 1px solid #444;
            color: #999;
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
            
            .hero h2 {
                font-size: 2.2rem;
            }
            
            .news-grid {
                grid-template-columns: 1fr;
            }
            
            .section-title h2 {
                font-size: 2rem;
            }
        }
        
        @media (max-width: 480px) {
            .hero {
                padding: 3rem 0;
            }
            
            .hero h2 {
                font-size: 1.8rem;
            }
            
            .cta-section {
                padding: 2rem 1rem;
            }
            
            .cta-section h3 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <h1>Portal de Notícias Esportivas</h1>
                </div>
                <div class="nav-links">
                    <a href="index.php">Início</a>
                    <?php if (isset($_SESSION['usuario_id'])): ?>
                        <a href="meu_painel.php">Meu Painel</a>
                        <a href="nova_noticia.php">+ Nova Notícia</a>
                        <a href="logout.php" >Sair</a>
                    <?php else: ?>
                        <a href="registrar.php">Cadastrar</a>
                        <a href="login.php" class="btn-login">Fazer Login</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>

    <section class="hero">
        <div class="container">
            <h2>Bem-vindo ao Portal de Notícias</h2>
            <p>Fique por dentro das últimas notícias e atualizações. Conteúdo fresco todos os dias!</p>
        </div>
    </section>

    <main class="container">
        <div class="section-title">
            <h2>Últimas Notícias</h2>
            <p>Confira as notícias mais recentes publicadas em nosso portal</p>
        </div>

        <?php if (empty($noticias)): ?>
            <div class="empty-state">
                <h3>📭 Nenhuma notícia publicada ainda</h3>
                <p>Seja o primeiro a compartilhar uma notícia!</p>
                <?php if (isset($_SESSION['usuario_id'])): ?>
                    <a href="nova_noticia.php" class="btn-read-more">📝 Publicar Primeira Notícia</a>
                <?php else: ?>
                    <a href="registrar.php" class="btn-read-more">📝 Cadastrar e Publicar</a>
                <?php endif; ?>
            </div>
        <?php else: ?>
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
        <?php endif; ?>

        
    </main>

    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h4>Portal de Notícias</h4>
                    <p>Seu portal confiável para as melhores notícias e atualizações. Conteúdo fresco e relevante todos os dias.</p>
                </div>
                
                
                <div class="footer-section">
                    <h4>📞 Contato</h4>
                    <p>Email: contato@portalnoticias.com<br>
                    Telefone: (11) 99999-9999<br>
                    Endereço: São Paulo, SP</p>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> Portal de Notícias. Todos os direitos reservados.</p>
            </div>
        </div>
    </footer>
</body>
</html>