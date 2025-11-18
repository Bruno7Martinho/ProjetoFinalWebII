<?php
session_start();
include_once './config/config.php';
include_once './classes/Noticia.php';

// DEBUG
error_log("=== INICIANDO EDIÇÃO DE NOTÍCIA ===");

// Verificar login
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}

$noticia = new Noticia($db);
$mensagem_erro = '';
$mensagem_sucesso = '';
$noticia_editar = null;

// Verificar ID
if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit();
}

$noticia_id = $_GET['id'];

if (!is_numeric($noticia_id)) {
    $mensagem_erro = "ID inválido!";
} else {
    $noticia_editar = $noticia->lerPorId($noticia_id);

    if ($noticia_editar) {
        if ($noticia_editar['autor'] != $_SESSION['usuario_id']) {
            $mensagem_erro = "Você não tem permissão para editar esta notícia!";
            $noticia_editar = null;
        }
    } else {
        $mensagem_erro = "Notícia não encontrada!";
    }
}

// PROCESSAR UPDATE
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['atualizar_noticia'])) {

    $titulo = trim($_POST['titulo']);
    $conteudo = trim($_POST['conteudo']);
    $imagem = trim($_POST['imagem']);

    if (empty($titulo) || empty($conteudo)) {
        $mensagem_erro = "Por favor, preencha todos os campos obrigatórios!";
    } else {
        try {
            $resultado = $noticia->atualizar($noticia_id, $titulo, $conteudo, $imagem);

            if ($resultado) {

                // 🔥 MENSAGEM DE SUCESSO
                $mensagem_sucesso = "Notícia atualizada com sucesso! Redirecionando...";

                // 🔥 REDIRECIONAR PARA INDEX EM 3 SEGUNDOS
                header("Refresh: 3; url=index.php");

                // Atualiza dados na tela
                $noticia_editar = $noticia->lerPorId($noticia_id);

            } else {
                $mensagem_erro = "Erro ao atualizar a notícia!";
            }

        } catch (Exception $e) {
            $mensagem_erro = $e->getMessage();
        }
    }
}

error_log("=== FINALIZANDO EDIÇÃO DE NOTÍCIA ===");
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Notícia - Ponto Esportivo</title>
    <link rel="stylesheet" href="css/editar_noticia.css">
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
                <a href="meu_painel.php">Meu Painel</a>
            </div>
        </div>
    </div>
</header>

<main class="container">

    <div class="page-header">
        <h2>✏️ Editar Notícia</h2>
        <p>Atualize os dados da sua notícia</p>
    </div>

    <!-- 🔥 MENSAGENS DE STATUS -->
    <?php if (!empty($mensagem_sucesso)): ?>
        <div class="alert alert-success">
            ✅ <?php echo $mensagem_sucesso; ?><br>
            <small>Você será redirecionado para a página inicial em alguns segundos...</small>
        </div>
    <?php endif; ?>

    <?php if (!empty($mensagem_erro)): ?>
        <div class="alert alert-error">
            ❌ <?php echo $mensagem_erro; ?>
        </div>
    <?php endif; ?>

    <!-- FORMULÁRIO -->
    <?php if ($noticia_editar): ?>
    <div class="form-container">
        <h2>Editar: <?php echo htmlspecialchars($noticia_editar['titulo']); ?></h2>

        <form method="POST">

            <div class="form-group">
                <label class="form-label required">Título da Notícia</label>
                <input type="text" name="titulo" class="form-control"
                       value="<?php echo htmlspecialchars($noticia_editar['titulo']); ?>" required>
            </div>

            <div class="form-group">
                <label class="form-label required">Conteúdo da Notícia</label>
                <textarea name="conteudo" class="form-control" required><?php echo htmlspecialchars_decode($noticia_editar['noticia']); ?></textarea>
            </div>

            <div class="form-group">
                <label class="form-label">URL da Imagem</label>
                <input type="text" name="imagem" class="form-control"
                       value="<?php echo htmlspecialchars($noticia_editar['imagem']); ?>">
                <div class="form-text">Opcional - Cole a URL da imagem da notícia</div>

                <?php if ($noticia_editar['imagem']): ?>
                <div class="image-preview">
                    <p><strong>Preview atual:</strong></p>
                    <img src="<?php echo htmlspecialchars($noticia_editar['imagem']); ?>" 
                         alt="Preview"
                         onerror="this.style.display='none'">
                </div>
                <?php endif; ?>
            </div>

            <div class="form-actions">
                <a href="noticia.php?id=<?php echo $noticia_editar['id']; ?>" class="btn btn-secondary">← Cancelar</a>
                <button type="submit" name="atualizar_noticia" class="btn btn-primary">💾 Atualizar Notícia</button>
            </div>

        </form>
    </div>

    <?php endif; ?>
</main>

<footer>
    <div class="container">
        <p>&copy; <?php echo date('Y'); ?> Ponto Esportivo. Todos os direitos reservados.</p>
    </div>
</footer>

</body>
</html>
