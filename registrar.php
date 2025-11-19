<?php
session_start();
include_once './config/config.php';
include_once './classes/Usuario.php';

$usuario = new Usuario($db);
$mensagem_erro = '';
$mensagem_sucesso = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['registrar'])) {
    $nome = trim($_POST['nome']);
    $sexo = $_POST['sexo'];
    $fone = trim($_POST['fone']);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $senha = $_POST['senha'];
    $confirmar_senha = $_POST['confirmar_senha'];
    
    // Validações
    if (empty($nome) || empty($sexo) || empty($email) || empty($senha) || empty($confirmar_senha)) {
        $mensagem_erro = "Por favor, preencha todos os campos obrigatórios!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $mensagem_erro = "Por favor, insira um email válido!";
    } elseif (strlen($senha) < 6) {
        $mensagem_erro = "A senha deve ter pelo menos 6 caracteres!";
    } elseif ($senha !== $confirmar_senha) {
        $mensagem_erro = "As senhas não coincidem!";
    } else {
        try {
            $resultado = $usuario->registrar($nome, $sexo, $fone, $email, $senha);
            
            if ($resultado) {
                $mensagem_sucesso = "Cadastro realizado com sucesso! Você pode fazer login agora.";
                // Limpar os campos do formulário
                $nome = $sexo = $fone = $email = '';
            }
        } catch (Exception $e) {
            $mensagem_erro = $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - Portal de Notícias</title>
    <link rel="stylesheet" href="css/registrar.css">
    
</head>
<body>
    <div class="registro-container">
        <div class="registro-header">
            <h2>Cadastro</h2>
            <p>Crie a conta do jornalista credenciado</p>
        </div>
        
        <div class="registro-form">
            <?php if ($mensagem_erro): ?>
                <div class="mensagem-erro">
                    <?php echo htmlspecialchars($mensagem_erro); ?>
                </div>
            <?php endif; ?>

            <?php if ($mensagem_sucesso): ?>
                <div class="mensagem-sucesso">
                    <?php echo htmlspecialchars($mensagem_sucesso); ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label for="nome" class="required">Nome Completo:</label>
                    <input type="text" 
                           id="nome" 
                           name="nome" 
                           value="<?php echo isset($_POST['nome']) ? htmlspecialchars($_POST['nome']) : ''; ?>" 
                           required 
                           placeholder="Seu nome completo">
                </div>
                
                <div class="form-group">
                    <label class="required">Sexo:</label>
                    <div class="radio-group">
                        <div class="radio-option">
                            <input type="radio" id="sexo_m" name="sexo" value="M" 
                                   <?php echo (isset($_POST['sexo']) && $_POST['sexo'] == 'M') ? 'checked' : ''; ?> required>
                            <label for="sexo_m">Masculino</label>
                        </div>
                        <div class="radio-option">
                            <input type="radio" id="sexo_f" name="sexo" value="F"
                                   <?php echo (isset($_POST['sexo']) && $_POST['sexo'] == 'F') ? 'checked' : ''; ?>>
                            <label for="sexo_f">Feminino</label>
                        </div>
                        <div class="radio-option">
                            <input type="radio" id="sexo_o" name="sexo" value="O"
                                   <?php echo (isset($_POST['sexo']) && $_POST['sexo'] == 'O') ? 'checked' : ''; ?>>
                            <label for="sexo_o">Outro</label>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="fone">Telefone:</label>
                    <input type="tel" 
                           id="fone" 
                           name="fone" 
                           value="<?php echo isset($_POST['fone']) ? htmlspecialchars($_POST['fone']) : ''; ?>" 
                           placeholder="(11) 99999-9999">
                </div>
                
                <div class="form-group">
                    <label for="email" class="required">Email:</label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" 
                           required 
                           placeholder="seu@email.com">
                </div>
                
                <div class="form-group">
                    <label for="senha" class="required">Senha:</label>
                    <input type="password" 
                           id="senha" 
                           name="senha" 
                           required 
                           placeholder="Mínimo 6 caracteres">
                    <div class="senha-info">A senha deve ter pelo menos 6 caracteres</div>
                </div>
                
                <div class="form-group">
                    <label for="confirmar_senha" class="required">Confirmar Senha:</label>
                    <input type="password" 
                           id="confirmar_senha" 
                           name="confirmar_senha" 
                           required 
                           placeholder="Digite a senha novamente">
                </div>
                
                <div class="form-buttons">
                    <button type="submit" name="voltar" value="voltar" class="btn-voltar">
                        Voltar
                    </button>
                    <button type="submit" name="registrar" value="registrar" class="btn-registrar">
                        Cadastrar
                    </button>
                </div>
                
            </form>
        </div>
    </div>
</body>
</html>