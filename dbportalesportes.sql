-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 18/11/2025 às 23:51
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `dbportalesportes`
--
CREATE DATABASE IF NOT EXISTS `dbportalesportes` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `dbportalesportes`;

-- --------------------------------------------------------

--
-- Estrutura para tabela `noticias`
--

CREATE TABLE `noticias` (
  `id` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `noticia` text NOT NULL,
  `data` datetime DEFAULT current_timestamp(),
  `autor` int(11) NOT NULL,
  `imagem` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `noticias`
--

INSERT INTO `noticias` (`id`, `titulo`, `noticia`, `data`, `autor`, `imagem`) VALUES
(1, 'Neymar marca hat-trick e Al-Hilal vence clássico', 'Em partida eletrizante pelo campeonato saudita, Neymar brilhou e marcou três gols na vitória do Al-Hilal por 4x2. O brasileiro mostrou toda sua classe e foi decisivo para a equipe, que se mantém na liderança do campeonato.', '2025-11-13 19:44:12', 1, NULL),
(2, 'Brasil goleia Argentina em clássico das Américas', 'A seleção brasileira aplicou uma goleada histórica sobre a Argentina por 4x0. Richarlison, Vini Jr, Rodrygo e Neymar marcaram os gols da vitória que coloca o Brasil na liderança das eliminatórias. A torcida lotou o Maracanã e celebrou a grande atuação da equipe.', '2025-11-13 19:44:12', 1, NULL),
(3, 'Palmeiras conquista o Brasileirão 2024', 'Com uma rodada de antecedência, o Palmeiras garantiu o título do Campeonato Brasileiro 2024. Foi o 12º título nacional do clube alviverde, que dominou a competição do início ao fim. Abel Ferreira comemora o bicampeonato.', '2025-11-13 19:44:12', 1, NULL),
(4, 'Flamengo anuncia contratação do técnico português', 'O Flamengo oficializou a contratação do técnico português João Silva. Com passagem pelo Porto e experiência na Premier League, o treinador chega para comandar o time rubro-negro na próxima temporada. A diretoria promete reforços para 2025.', '2025-11-13 19:44:12', 1, NULL),
(5, 'São Paulo vence Libertadores em final dramática', 'O São Paulo é campeão da Libertadores 2024! Em final emocionante contra o River Plate, o tricolor venceu nos pênaltis após empate em 2x2 no tempo normal. Luciano foi o herói da noite ao marcar os dois gols e converter o pênalti decisivo.', '2025-11-13 19:44:12', 1, NULL),
(6, 'Corinthians supera crise e avança na Copa do Brasil', 'Em noite de superação, o Corinthians eliminou o rival Santos da Copa do Brasil. Com gol de Yuri Alberto nos acréscimos, o time alvinegra garantiu vaga nas quartas de final da competição. A Fiel comemorou muito a classificação.', '2025-11-13 19:44:12', 1, NULL),
(7, 'Seleção feminina é campeã da Copa do Mundo', 'Pela primeira vez na história, o Brasil é campeão mundial de futebol feminino! A equipe comandada por Pia Sundhage venceu os EUA na final por 2x1, com gols de Debinha e Kerolin. Marta finalmente conquista o título que faltava.', '2025-11-13 19:44:12', 1, NULL),
(8, 'Vasco sobe para a Série A do Brasileirão', 'O Vasco da Gama garantiu o acesso para a Série A do Campeonato Brasileiro 2025. Com vitória sobre o CRB por 3x0, o clube cruz-maltino retorna à elite do futebol nacional. A torcida comemora nas ruas do Rio de Janeiro.', '2025-11-13 19:44:12', 1, NULL),
(11, 'Neymar trai esposa com Virginia  Fonseca', 'dfgdsgfgdsgfdsgfhgdhsgfhgdshgfhdshfhgdhsgfhgsdhfgdsgfhdsgfhghdsgfgdshgfhgsdfsdcsadasdghfasgfsaghfsagfvbsdbnvbdbnfvdshfbnmsdbghbfdjkdbjkdshjkdhsjkhtgjkd', '2025-11-17 22:22:09', 1, 'imagens/noticias/691bca41d79f8_noticia.jpg');

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `sexo` enum('M','F','O') NOT NULL,
  `fone` varchar(20) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `data_criacao` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `nome`, `sexo`, `fone`, `email`, `senha`, `data_criacao`) VALUES
(1, 'Ponto Esportivo', 'M', '(11) 98888-8888', 'admin@portal.com', '$2y$10$hwZSAiIizPD.91l7t.iOwe3TuKUQbs0AlLK.a7GnS7VEQya.AtYVO', '2025-11-13 19:36:17'),
(2, 'Carlos Silva', 'M', '(11) 99999-1111', 'carlos.silva@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-11-13 19:43:42'),
(3, 'Ana Oliveira', 'F', '(11) 99999-2222', 'ana.oliveira@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-11-13 19:43:42'),
(4, 'Roberto Santos', 'M', '(11) 99999-3333', 'roberto.santos@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-11-13 19:43:42'),
(5, 'Juliana Costa', 'F', '(11) 99999-4444', 'juliana.costa@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-11-13 19:43:42'),
(6, 'Marcos Lima', 'M', '(11) 99999-5555', 'marcos.lima@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-11-13 19:43:42'),
(7, 'Fernanda Rocha', 'F', '(11) 99999-6666', 'fernanda.rocha@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-11-13 19:43:42'),
(8, 'Ricardo Alves', 'M', '(11) 99999-7777', 'ricardo.alves@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-11-13 19:43:42'),
(9, 'Patrícia Martins', 'F', '(11) 99999-8888', 'patricia.martins@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-11-13 19:43:42'),
(10, 'Bruno Ferreira', 'M', '(11) 99999-9999', 'bruno.ferreira@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-11-13 19:43:42');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `noticias`
--
ALTER TABLE `noticias`
  ADD PRIMARY KEY (`id`),
  ADD KEY `autor` (`autor`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `noticias`
--
ALTER TABLE `noticias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `noticias`
--
ALTER TABLE `noticias`
  ADD CONSTRAINT `noticias_ibfk_1` FOREIGN KEY (`autor`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
