-- phpMyAdmin SQL Dump
-- version 5.1.2
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost:3306
-- Généré le : mer. 07 juin 2023 à 21:43
-- Version du serveur : 5.7.24
-- Version de PHP : 8.0.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `cryptoinc`
--

-- --------------------------------------------------------

--
-- Structure de la table `compatibility_table`
--

CREATE TABLE `compatibility_table` (
  `id` bigint(20) NOT NULL,
  `id_struct_1` bigint(20) NOT NULL,
  `type_struct_1` varchar(20) NOT NULL,
  `id_struct_2` bigint(20) NOT NULL,
  `type_struct_2` varchar(20) NOT NULL,
  `compatibility_1` varchar(20) NOT NULL,
  `compatibility_2` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `contrat_ex`
--

CREATE TABLE `contrat_ex` (
  `id` bigint(20) NOT NULL,
  `exchange_id` int(8) NOT NULL,
  `token_id` bigint(15) NOT NULL,
  `type` varchar(30) NOT NULL,
  `max_supply` bigint(20) NOT NULL,
  `prix` double NOT NULL,
  `fees` double NOT NULL,
  `supply` double NOT NULL,
  `leverage_max` int(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `contrat_ex`
--

INSERT INTO `contrat_ex` (`id`, `exchange_id`, `token_id`, `type`, `max_supply`, `prix`, `fees`, `supply`, `leverage_max`) VALUES
(1, 1, 1, 'spot', 100000, 29000, 2, 10007.8, 0),
(2, 2, 2, 'spot', 1500, 1800, 0, 740, 0),
(3, 1, 1, 'derivee', 10000, 29000, 5, 4751.6, 100);

-- --------------------------------------------------------

--
-- Structure de la table `game_blockchain`
--

CREATE TABLE `game_blockchain` (
  `id` int(6) NOT NULL,
  `name` varchar(30) NOT NULL,
  `owner_wallet_id` bigint(20) NOT NULL,
  `creator_id` bigint(10) NOT NULL,
  `date_creation` date NOT NULL,
  `version` varchar(15) NOT NULL,
  `native_token_id` bigint(15) NOT NULL,
  `blockchain_token_id` varchar(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `game_exchange`
--

CREATE TABLE `game_exchange` (
  `id` int(8) NOT NULL,
  `owner_id` bigint(10) NOT NULL,
  `blockchain_id` int(6) NOT NULL,
  `name` varchar(20) NOT NULL,
  `fonds` double NOT NULL,
  `project_token_id` bigint(15) NOT NULL,
  `date_creation` date NOT NULL,
  `version` varchar(12) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `game_exchange`
--

INSERT INTO `game_exchange` (`id`, `owner_id`, `blockchain_id`, `name`, `fonds`, `project_token_id`, `date_creation`, `version`) VALUES
(1, 1, 1, 'Binance', 1000000, 2, '2017-07-14', '1.0.0'),
(2, 1, 1, 'Bybit', 20000, 1, '2018-03-23', '1.0.0');

-- --------------------------------------------------------

--
-- Structure de la table `game_token`
--

CREATE TABLE `game_token` (
  `id` bigint(15) NOT NULL,
  `name` varchar(25) NOT NULL,
  `sigle` varchar(8) NOT NULL COMMENT '(sera limité a 4 au debut) peut comprendre majuscule, minuscule, nb, et caractère speciaux.',
  `price` double NOT NULL,
  `blockchain_id` int(6) NOT NULL,
  `max_supply` bigint(20) NOT NULL,
  `supply` double NOT NULL,
  `date_creation` date NOT NULL,
  `ico_price` double NOT NULL,
  `id_creator` bigint(10) NOT NULL,
  `id_wallet_creator` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `game_token`
--

INSERT INTO `game_token` (`id`, `name`, `sigle`, `price`, `blockchain_id`, `max_supply`, `supply`, `date_creation`, `ico_price`, `id_creator`, `id_wallet_creator`) VALUES
(1, 'Bitcoin', 'Btc', 29000, 1, 21000000, 11000000, '2009-01-03', 0.001, 1, 1),
(2, 'Ethereum', 'Eth', 1800, 1, 1500000, 150000, '2015-07-30', 0.31, 1, 1);

-- --------------------------------------------------------

--
-- Structure de la table `smart_contract`
--

CREATE TABLE `smart_contract` (
  `id` double NOT NULL,
  `name` varchar(50) NOT NULL,
  `code` text NOT NULL,
  `id_wallet_creator` bigint(20) NOT NULL,
  `type` varchar(10) NOT NULL,
  `id_user_creator` bigint(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `smart_contract`
--

INSERT INTO `smart_contract` (`id`, `name`, `code`, `id_wallet_creator`, `type`, `id_user_creator`) VALUES
(1, 'test', 'ON ERC-20;\nSEND Btc NUMBER 10 FROM 242D8BE54A TO 584AA4DF34;', 1, 'public', 1);

-- --------------------------------------------------------

--
-- Structure de la table `trades_ex`
--

CREATE TABLE `trades_ex` (
  `id` double NOT NULL,
  `id_contrat` bigint(20) NOT NULL,
  `id_user` bigint(10) NOT NULL,
  `prix_achat` double NOT NULL,
  `quantite` double NOT NULL,
  `etat` varchar(16) NOT NULL,
  `liquidation` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `trades_ex`
--

INSERT INTO `trades_ex` (`id`, `id_contrat`, `id_user`, `prix_achat`, `quantite`, `etat`, `liquidation`) VALUES
(1, 3, 1, 27000, 0.5, 'end', 0),
(2, 1, 1, 29000, 0.5, 'end', 0),
(3, 1, 1, 14502, 0.5, 'end', 0),
(4, 1, 1, 14502, 0.2, 'end', 0),
(5, 1, 1, 43502, 0.2, 'test', 0),
(6, 1, 1, 14496, 0.5, 'test', 0),
(7, 3, 1, 10, 1, 'end', 0),
(8, 3, 1, 15000, 0.5, 'end', 0),
(9, 3, 1, 29000, 0.5, 'end', 0),
(12, 1, 1, 14498, 0.5, 'test', 0),
(13, 1, 1, 14500, 0.5, 'test', 0),
(14, 1, 1, 14500, 0.5, 'test', 0),
(15, 1, 1, 2902, 0.1, 'test', 0),
(16, 1, 1, 2902, 0.1, 'test', 0),
(17, 1, 1, 2902, 0.1, 'test', 0),
(18, 1, 1, 2902, 0.1, 'test', 0),
(19, 1, 1, 14502, 0.5, 'test', 0),
(20, 3, 1, 29005, 100, 'end', 0),
(21, 3, 1, 6050, 10, 'end', 0),
(22, 3, 1, 29000, 0.5, 'end', 1),
(23, 3, 1, 29000, 0.5, 'end', 2900),
(24, 3, 1, 29000, 0.5, 'end', 2900),
(25, 3, 1, 29000, 0.5, 'end', 2900),
(26, 3, 1, 29000, 5, 'end', 29000),
(27, 3, 1, 29000, 5, 'end', 29000),
(28, 3, 1, 29000, 2, 'end', 29000),
(29, 3, 1, 29000, 0.5, 'end', 29000),
(30, 3, 1, 29000, 0, 'end', 29000),
(31, 3, 1, 29000, 0, 'end', 29000),
(32, 3, 1, 29000, 0, 'end', 29000),
(33, 3, 1, 29000, 0.5, 'end', 29000),
(34, 3, 1, 29000, 0.5, 'end', 29000),
(35, 3, 1, 29000, 0.5, 'end', 29000),
(36, 3, 1, 25000, 2, 'end', 29000),
(37, 3, 1, 29000, 0.7, 'end', 0),
(38, 3, 1, 25000, 1, 'end', 26100),
(39, 3, 1, 2000000, 2, 'end', 0),
(40, 3, 1, 25000, 5, 'end', 31900),
(41, 3, 1, 25000, 0.5, 'end', 0),
(42, 3, 1, 25000, 0.5, 'end', 58000),
(43, 3, 1, 25000, 10, 'end', 26100),
(44, 3, 1, 25000, 10, 'end', 26100),
(45, 3, 1, 25000, 10, 'end', 31900);

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` bigint(10) NOT NULL,
  `username` varchar(16) NOT NULL,
  `hash` varchar(100) NOT NULL,
  `salt` varchar(50) NOT NULL,
  `argent` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `username`, `hash`, `salt`, `argent`) VALUES
(1, 'test_000', '$5$rounds=5000$steamedhamstest_$Ut2hsoznke2zpsrL0irPy9vTL8YdlTQHdldJc6AfqR2', '$5$rounds=5000$steamedhamstest_000$', 4356500);

-- --------------------------------------------------------

--
-- Structure de la table `users_messages`
--

CREATE TABLE `users_messages` (
  `id` bigint(20) NOT NULL,
  `id_joueur_send` bigint(10) NOT NULL,
  `id_joueur_receiv` bigint(10) NOT NULL,
  `date_envoi` date NOT NULL,
  `contenu` varchar(300) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `wallet_account`
--

CREATE TABLE `wallet_account` (
  `id` bigint(20) NOT NULL,
  `id_creator` bigint(10) NOT NULL,
  `hash` varchar(100) NOT NULL,
  `salt` varchar(50) NOT NULL,
  `recovery_phrase` varchar(50) NOT NULL,
  `cle_publique` varchar(20) NOT NULL,
  `blockchain_id` int(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `compatibility_table`
--
ALTER TABLE `compatibility_table`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_struct_1` (`id_struct_1`),
  ADD KEY `id_struct_2` (`id_struct_2`);

--
-- Index pour la table `contrat_ex`
--
ALTER TABLE `contrat_ex`
  ADD PRIMARY KEY (`id`),
  ADD KEY `exchange_id` (`exchange_id`),
  ADD KEY `token` (`token_id`);

--
-- Index pour la table `game_blockchain`
--
ALTER TABLE `game_blockchain`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `blockchain_token_id` (`blockchain_token_id`),
  ADD KEY `owner_wallet_id` (`owner_wallet_id`),
  ADD KEY `creator_id` (`creator_id`),
  ADD KEY `native_token_id` (`native_token_id`);

--
-- Index pour la table `game_exchange`
--
ALTER TABLE `game_exchange`
  ADD PRIMARY KEY (`id`),
  ADD KEY `owner_id` (`owner_id`),
  ADD KEY `blockchain_id` (`blockchain_id`),
  ADD KEY `project_token_id` (`project_token_id`);

--
-- Index pour la table `game_token`
--
ALTER TABLE `game_token`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sigle` (`sigle`),
  ADD KEY `id_creator` (`id_creator`),
  ADD KEY `id_wallet_creator` (`id_wallet_creator`),
  ADD KEY `blockchain_id` (`blockchain_id`);

--
-- Index pour la table `smart_contract`
--
ALTER TABLE `smart_contract`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD KEY `id_wallet_creator` (`id_wallet_creator`);

--
-- Index pour la table `trades_ex`
--
ALTER TABLE `trades_ex`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_contrat` (`id_contrat`),
  ADD KEY `id_user` (`id_user`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Index pour la table `users_messages`
--
ALTER TABLE `users_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_joueur_receiv` (`id_joueur_receiv`),
  ADD KEY `id_joueur_send` (`id_joueur_send`);

--
-- Index pour la table `wallet_account`
--
ALTER TABLE `wallet_account`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `recovery_phrase` (`recovery_phrase`),
  ADD UNIQUE KEY `cle_publique` (`cle_publique`),
  ADD KEY `id_creator` (`id_creator`),
  ADD KEY `blockchain_id` (`blockchain_id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `compatibility_table`
--
ALTER TABLE `compatibility_table`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `contrat_ex`
--
ALTER TABLE `contrat_ex`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `game_blockchain`
--
ALTER TABLE `game_blockchain`
  MODIFY `id` int(6) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `game_exchange`
--
ALTER TABLE `game_exchange`
  MODIFY `id` int(8) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `game_token`
--
ALTER TABLE `game_token`
  MODIFY `id` bigint(15) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `smart_contract`
--
ALTER TABLE `smart_contract`
  MODIFY `id` double NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `trades_ex`
--
ALTER TABLE `trades_ex`
  MODIFY `id` double NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `users_messages`
--
ALTER TABLE `users_messages`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `wallet_account`
--
ALTER TABLE `wallet_account`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
