

CREATE TABLE `casos_legais` (
  `id` int(11) NOT NULL,
  `corpo_id` int(11) NOT NULL,
  `num_processo` varchar(50) NOT NULL,
  `status_caso` enum('Em Investigação','Arquivado','Aguardando Liberação') NOT NULL,
  `investigador_responsavel` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


CREATE TABLE `corpos` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `data_nascimento` date DEFAULT NULL,
  `data_obito` date DEFAULT NULL,
  `causa_morte` varchar(150) DEFAULT NULL,
  `data_entrada` datetime NOT NULL,
  `localizacao_gaveta` varchar(50) DEFAULT NULL,
  `observacoes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

