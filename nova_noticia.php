<?php
session_start();
include_once './config/config.php';
include_once './classes/Noticia.php';
include_once './classes/Usuario.php';

// Verificar se usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}

$noticia = new Noticia($db);
$usuario = new Usuario($db);

$mensagem_erro = '';
$mensagem_sucesso = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['criar_noticia'])) {
    $titulo = trim($_POST['titulo']);
    $conteudo = trim($_POST['conteudo']);
    $autor_id = $_SESSION['usuario_id'];
    $imagem = null;
    
    // Validações
    if (empty($titulo) || empty($conteudo)) {
        $mensagem_erro = "Por favor, preencha todos os campos obrigatórios!";
    } elseif (strlen($titulo) < 5) {
        $mensagem_erro = "O título deve ter pelo menos 5 caracteres!";
    } elseif (strlen($conteudo) < 50) {
        $mensagem_erro = "O conteúdo da notícia deve ter pelo menos 50 caracteres!";
    } else {
        try {
            // Processar upload de imagem se existir
            if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
                $imagem = uploadImagemNoticia($_FILES['imagem']);
            }
            
            $resultado = $noticia->criar($titulo, $conteudo, $autor_id, $imagem);
            
            if ($resultado) {
                $mensagem_sucesso = "Notícia publicada com sucesso!";
                // Redirecionar para o portal
                header('Location: portal.php?sucesso=Notícia publicada com sucesso!');
                exit();
            }
        } catch (Exception $e) {
            $mensagem_erro = $e->getMessage();
        }
    }
}

// Função para upload de imagem
function uploadImagemNoticia($imagem) {
    $pasta = 'imagens/noticias/';
    
    // Criar pasta se não existir
    if (!is_dir($pasta)) {
        mkdir($pasta, 0777, true);
    }
    
    // Verificar se é uma imagem válida
    $tiposPermitidos = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    if (!in_array($imagem['type'], $tiposPermitidos)) {
        throw new Exception('Tipo de arquivo não permitido. Use apenas JPEG, PNG, GIF ou WebP.');
    }
    
    // Verificar tamanho (máximo 5MB)
    if ($imagem['size'] > 5242880) {
        throw new Exception('A imagem deve ter no máximo 5MB.');
    }
    
    // Gerar nome único para o arquivo
    $extensao = pathinfo($imagem['name'], PATHINFO_EXTENSION);
    $nomeArquivo = uniqid() . '_noticia.' . $extensao;
    $caminhoCompleto = $pasta . $nomeArquivo;
    
    if (move_uploaded_file($imagem['tmp_name'], $caminhoCompleto)) {
        return $caminhoCompleto;
    }
    
    throw new Exception('Erro ao fazer upload da imagem.');
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nova Notícia - Ponto Esportivo</title>
    <link rel="stylesheet" href="css/nova_noticia.css">
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
                    <a href="meu_painel.php">Meu Painel</a>
                    <a href="nova_noticia.php">+ Nova Notícia</a>
                    <a href="logout.php">Sair</a>
                </div>
            </div>
        </div>
    </header>

    <main class="container">
        <div class="form-container">
            <div class="form-header">
                <h2>Criar Nova Notícia</h2>
                <p>Compartilhe suas notícias com o mundo</p>
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

            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="titulo" class="form-label required">Título da Notícia</label>
                    <input type="text" 
                           class="form-control" 
                           id="titulo" 
                           name="titulo" 
                           value="<?php echo isset($_POST['titulo']) ? htmlspecialchars($_POST['titulo']) : ''; ?>" 
                           required 
                           placeholder="Digite um título atraente para sua notícia"
                           maxlength="255">
                    <div class="char-info">Máximo 255 caracteres | Mínimo 5 caracteres</div>
                </div>
                
                <div class="form-group">
                    <label for="conteudo" class="form-label required">Conteúdo da Notícia</label>
                    <textarea class="form-control" 
                              id="conteudo" 
                              name="conteudo" 
                              required 
                              placeholder="Escreva o conteúdo completo da sua notícia aqui..."
                              rows="15"><?php echo isset($_POST['conteudo']) ? htmlspecialchars($_POST['conteudo']) : ''; ?></textarea>
                    <div class="char-info">Mínimo 50 caracteres</div>
                </div>
                
                <div class="form-group">
                    <label for="imagem" class="form-label">Imagem da Notícia</label>
                    <input type="file" 
                           class="form-control file-input" 
                           id="imagem" 
                           name="imagem" 
                           accept="image/*">
                    <div class="form-text">
                        Formatos aceitos: JPEG, PNG, GIF, WebP | Tamanho máximo: 5MB
                    </div>
                </div>
                
                <div class="form-actions">
                    <a href="meu_painel.php" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" name="criar_noticia" value="criar" class="btn btn-primary">
                        Publicar Notícia
                    </button>
                </div>
            </form>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> Ponto Esportivo. Todos os direitos reservados.</p>
        </div>
    </footer>
</body>
</html>