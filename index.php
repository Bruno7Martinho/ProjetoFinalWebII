<?php
session_start();
include_once './config/config.php';
include_once './classes/Noticia.php';
include_once './classes/Usuario.php';

$noticia = new Noticia($db);
$usuario = new Usuario($db);

// Buscar todas as notícias usando o método ler()
$noticias = $noticia->ler();

// Função para buscar dados do clima
function buscarClima() {
    try {
        // Coordenadas de Sapucaia do Sul/RS
        $latitude = -29.8381;
        $longitude = -51.1444;
        
        $url = "https://api.open-meteo.com/v1/forecast?latitude={$latitude}&longitude={$longitude}&current=temperature_2m,relative_humidity_2m,weather_code,wind_speed_10m&timezone=America/Sao_Paulo";
        
        $json = file_get_contents($url);
        $dados = json_decode($json, true);
        
        if (!$dados || !isset($dados['current'])) {
            throw new Exception('Dados não encontrados');
        }
        
        $current = $dados['current'];
        
        // Função para obter ícone do clima
        function obterIconeClima($codigoClima) {
            $icones = [
                0 => '☀️',   1 => '🌤️',   2 => '⛅',   3 => '☁️',
                45 => '🌫️',  48 => '🌫️',  51 => '🌦️',  53 => '🌦️',
                55 => '🌦️',  61 => '🌧️',  63 => '🌧️',  65 => '🌧️',
                80 => '🌦️',  81 => '🌦️',  82 => '🌦️',  95 => '⛈️',
                96 => '⛈️',  99 => '⛈️'
            ];
            return $icones[$codigoClima] ?? '🌤️';
        }
        
        // Função para obter descrição do clima
        function obterDescricaoClima($codigoClima) {
            $descricoes = [
                0 => 'Céu limpo',          1 => 'Principalmente limpo',
                2 => 'Parcialmente nublado', 3 => 'Nublado',
                45 => 'Nevoeiro',          48 => 'Nevoeiro com geada',
                51 => 'Chuvisco leve',     53 => 'Chuvisco moderado',
                55 => 'Chuvisco denso',    61 => 'Chuva leve',
                63 => 'Chuva moderada',    65 => 'Chuva forte',
                80 => 'Aguaceiros leves',  81 => 'Aguaceiros moderados',
                82 => 'Aguaceiros fortes', 95 => 'Tempestade',
                96 => 'Tempestade com granizo', 99 => 'Tempestade forte com granizo'
            ];
            return $descricoes[$codigoClima] ?? 'Condições meteorológicas';
        }
        
        $icone = obterIconeClima($current['weather_code']);
        $descricao = obterDescricaoClima($current['weather_code']);
        $temperatura = round($current['temperature_2m']);
        $umidade = $current['relative_humidity_2m'];
        $vento = $current['wind_speed_10m'];
        
        // Retornar os dados formatados
        return "
        <div class='clima-content'>
            <div class='clima-icon'>{$icone}</div>
            <div class='clima-temp'>{$temperatura}°C</div>
            <div class='clima-details'>
                <div class='clima-city'>Sapucaia do Sul, RS</div>
                <div class='clima-desc'>{$descricao}</div>
                <div class='clima-extra'>
                    <span>💧 {$umidade}%</span>
                    <span>💨 {$vento} km/h</span>
                </div>
            </div>
        </div>";
        
    } catch (Exception $e) {
        return "<div class='clima-error'>Erro ao carregar dados do clima</div>";
    }
}

// Buscar dados do clima
$climaHTML = buscarClima();
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ponto Esportivo- Portal de Notícias Esportivas</title>
    <link rel="stylesheet" href="css/index.css">
    <style>
/* Widget de Clima */
.clima-widget {
    background: linear-gradient(135deg, #2E6F40, #54582F);
    color: white;
    border-radius: 15px;
    padding: 1.5rem;
    margin: 2rem auto;
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    border: 1px solid #e8e8e8;
    max-width: 1200px;
}

.clima-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.clima-title {
    font-size: 1.3rem;
    font-weight: 600;
}

.clima-refresh {
    background: rgba(255,255,255,0.2);
    border: none;
    color: white;
    padding: 0.5rem;
    border-radius: 50%;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 1.2rem;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.clima-refresh:hover {
    background: rgba(255,255,255,0.3);
    transform: rotate(180deg);
}

.clima-content {
    display: flex;
    align-items: center;
    gap: 1.5rem;
}

.clima-icon {
    font-size: 3rem;
    text-align: center;
    min-width: 60px;
}

.clima-temp {
    font-size: 2.5rem;
    font-weight: 700;
    min-width: 100px;
}

.clima-details {
    flex-grow: 1;
}

.clima-city {
    font-size: 1.2rem;
    margin-bottom: 0.5rem;
    font-weight: 600;
}

.clima-desc {
    color: rgba(255,255,255,0.8);
    margin-bottom: 0.5rem;
}

.clima-extra {
    display: flex;
    gap: 1rem;
    font-size: 0.9rem;
    color: rgba(255,255,255,0.8);
}

.clima-loading, .clima-error {
    text-align: center;
    padding: 1rem;
    font-style: italic;
}

.clima-error {
    color: #ff6b6b;
}

/* RESPONSIVIDADE DO CLIMA */
@media (max-width: 768px) {
    .clima-content {
        flex-direction: column;
        text-align: center;
        gap: 1rem;
    }

    .clima-extra {
        justify-content: center;
    }

    .clima-icon {
        font-size: 2.5rem;
    }

    .clima-temp {
        font-size: 2rem;
    }
}

@media (max-width: 480px) {
    .clima-widget {
        padding: 1rem;
        margin: 1rem 10px 2rem 10px;
    }

    .clima-title {
        font-size: 1.1rem;
    }

    .clima-extra {
        flex-direction: column;
        gap: 0.5rem;
    }
}
</style>
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

    
    <div class="container">
        <div class="clima-widget">
            <div class="clima-header">
                <h2 class="clima-title">Previsão do Tempo</h2>
            </div>
            <div id="clima-content">
                <?php echo $climaHTML; ?>
            </div>
        </div>
    </div>

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