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

// AQUI É ONDE VOCÊ PÕE ESSA LINHA ↓
$noticia_dados = $noticia->lerPorId($noticia_id);

// Verifica se a notícia existe
if (!$noticia_dados) {
    header('Location: index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($noticia_dados['titulo']); ?> - SportNews</title>
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
            max-width: 800px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        /* Header */
        header {
            background: #1a1a2e;
            box-shadow: 0 2px 20px rgba(0,0,0,0.3);
            padding: 1rem 0;
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
        
        .nav-links a {
            color: #f1faee;
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
        
        /* Notícia */
        .noticia-container {
            background: white;
            border-radius: 15px;
            padding: 3rem;
            margin: 2rem 0;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        
        .noticia-titulo {
            color: #1a1a2e;
            font-size: 2.5rem;
            margin-bottom: 1.5rem;
            line-height: 1.3;
        }
        
        .noticia-meta {
            color: #666;
            margin-bottom: 2rem;
            padding-bottom: 1.5rem;
            border-bottom: 2px solid #e63946;
            font-size: 1.1rem;
        }
        
        .noticia-imagem {
            width: 100%;
            max-height: 400px;
            object-fit: cover;
            border-radius: 10px;
            margin: 2rem 0;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        
        .noticia-conteudo {
            font-size: 1.1rem;
            line-height: 1.8;
            color: #444;
        }
        
        .noticia-conteudo p {
            margin-bottom: 1.5rem;
            text-align: justify;
        }
        
        .btn-voltar {
            display: inline-block;
            background: #e63946;
            color: white;
            padding: 1rem 2rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            margin-top: 2rem;
        }
        
        .btn-voltar:hover {
            background: #d62839;
            transform: translateY(-2px);
        }
        
        /* Footer */
        footer {
            background: #1a1a2e;
            color: white;
            padding: 2rem 0;
            margin-top: 3rem;
            text-align: center;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                gap: 1rem;
            }
            
            .noticia-container {
                padding: 2rem 1.5rem;
            }
            
            .noticia-titulo {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <h1>⚽ SportNews</h1>
                </div>
                <div class="nav-links">
                    <a href="index.php">📰 Voltar para Notícias</a>
                    <?php if (isset($_SESSION['usuario_id'])): ?>
                        <a href="portal.php">📊 Meu Painel</a>
                    <?php else: ?>
                        <a href="login.php">🔐 Fazer Login</a>
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
                    <a href="portal.php?excluir=<?php echo $noticia_dados['id']; ?>" style="background: #dc3545; color: white; padding: 0.7rem 1.5rem; border-radius: 8px; text-decoration: none;" onclick="return confirm('Tem certeza que deseja excluir esta notícia?')">🗑️ Excluir</a>
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