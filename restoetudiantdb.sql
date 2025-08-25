-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jul 14, 2025 at 03:57 PM
-- Server version: 9.1.0
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `restoetudiantdb`
--

CREATE DATABASE IF NOT EXISTS restoetudiantdb DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;
USE restoetudiantdb;


-- --------------------------------------------------------

--
-- Table structure for table `utilisateur`
--

CREATE TABLE IF NOT EXISTS utilisateur (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    telephone VARCHAR(20) DEFAULT NULL,
    motdepasse VARCHAR(255) NOT NULL,
    role ENUM('Etudiant','Restaurateur') NOT NULL DEFAULT 'Etudiant',
    date_creation TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- -----------------
-- AJOUT 2 NOUVELLES TABLES ---
-- creer une nouvelle table pour enregistrer les etudiants
-- code_etudiant sert a stocker le code unique des etudiants
CREATE TABLE IF NOT EXISTS etudiants (
    utilisateur_id INT PRIMARY KEY,
    universite VARCHAR(150),
    annee_academique VARCHAR(20),
    numeroEtudiant VARCHAR(100) NOT NULL UNIQUE,
    code_etudiant VARCHAR(50) NOT NULL UNIQUE,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateur(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


-- creer une nouvelle table pour les restaurateurs 
-- code_restaurateur sert a stocker le code unique des restaurateurs
CREATE TABLE IF NOT EXISTS restaurateurs (
    utilisateur_id INT PRIMARY KEY,
    adresse VARCHAR(255) NOT NULL,
    cuisine VARCHAR(100) NOT NULL,
    code_restaurateur VARCHAR(50) NOT NULL UNIQUE,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateur(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `utilisateur`
--

INSERT INTO `utilisateur` (`id`, `nom`, `email`, `telephone`, `motdepasse`, `role`, `date_creation`) VALUES
(1, 'Beda Eric', 'eric@gmail.com', '5147894562', '$2y$10$IJ/bdsL9h3gjYwsTRm3SrOzAuX.DG37RMu.KsVCoAw7pUMlGF7HNC', 'Etudiant', '2025-07-14 01:58:31'),
(2, 'Sehboub Mourad', 'mouradmaths17@gmail.com', '5148987654', '$2y$10$fuq3rqPuTziLisgYQAOx.eFSSoSBeCg7HYpERysoF04jqefjFw9xq', 'Restaurateur', '2025-07-14 01:39:51');

-- Insertion pour la table etudiants (pour l'utilisateur id=6)
INSERT INTO `etudiants` (`utilisateur_id`, `universite`, `annee_academique`, `numeroEtudiant`, `code_etudiant`) VALUES
(1, 'College Lasalle', '2024-2025', '249985', 'EtuCls123456789');

-- Insertion pour la table restaurateurs (pour l'utilisateur id=5)
INSERT INTO `restaurateurs` (`utilisateur_id`, `cuisine`, `code_restaurateur`) VALUES
(2, 'Cuisine Algerienne', 'ResAlg001');


/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- --------------------------------------------------------

--
-- Table structure for table `formule`
--

CREATE TABLE IF NOT EXISTS formules (
    id INT AUTO_INCREMENT PRIMARY KEY,
    utilisateur_id INT NOT NULL,
    nom VARCHAR(100) NOT NULL,
    description TEXT,
    prix DECIMAL(6,2) NOT NULL,
    duree ENUM('1 semaine','2 semaines','1 mois') NOT NULL,
    image VARCHAR(255) DEFAULT NULL,
    date_ajout TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateur(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
-- --------------------------------------------------------
-- Table structure for table `plat`

CREATE TABLE IF NOT EXISTS plats (
    id INT NOT NULL AUTO_INCREMENT,
    utilisateur_id INT NOT NULL,
    nom VARCHAR(100) NOT NULL,
    description TEXT,
    prix DECIMAL(6,2) NOT NULL,
    image VARCHAR(255) DEFAULT NULL,
    date_ajout TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    disponible TINYINT(1) DEFAULT 1,
    PRIMARY KEY (id),
    KEY utilisateur_id (utilisateur_id),
    CONSTRAINT fk_plat_utilisateur FOREIGN KEY (utilisateur_id) REFERENCES utilisateur(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


----------------------------------------------------------

-- Table structure for table `formule_plat`

CREATE TABLE IF NOT EXISTS formules_plats (
    formule_id INT NOT NULL,
    plat_id INT NOT NULL,
    quantite INT DEFAULT 1,
    PRIMARY KEY (formule_id, plat_id),
    FOREIGN KEY (formule_id) REFERENCES formule(id),
    FOREIGN KEY (plat_id) REFERENCES plat(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Table structure for table `commandes`

CREATE TABLE IF NOT EXISTS commandes (
    id INT NOT NULL AUTO_INCREMENT,
    utilisateur_id INT NOT NULL,
    date_commande TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    statut ENUM('en attente','validée','livrée','annulée') DEFAULT 'en attente',
    PRIMARY KEY (id),
    KEY utilisateur_id (utilisateur_id),
    CONSTRAINT fk_commande_utilisateur FOREIGN KEY (utilisateur_id) REFERENCES utilisateur(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


-- --------------------------------------------------------
-- Table structure for table `commande_formule`
--

CREATE TABLE IF NOT EXISTS commandes_formules (
    commande_id INT NOT NULL,
    formule_id INT NOT NULL,
    quantite INT DEFAULT '1',
    PRIMARY KEY (commande_id, formule_id),
    KEY formule_id (formule_id),
    CONSTRAINT fk_commande_formule_commande FOREIGN KEY (commande_id) REFERENCES commande(id),
    CONSTRAINT fk_commande_formule_formule FOREIGN KEY (formule_id) REFERENCES formule(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Table structure for table `commandes_plats`
--

CREATE TABLE IF NOT EXISTS commandes_plats (
    commande_id INT NOT NULL,
    plat_id INT NOT NULL,
    quantite INT DEFAULT '1',
    PRIMARY KEY (commande_id, plat_id),
    KEY plat_id (plat_id),
    CONSTRAINT fk_commande_plat_commande FOREIGN KEY (commande_id) REFERENCES commande(id),
    CONSTRAINT fk_commande_plat_plat FOREIGN KEY (plat_id) REFERENCES plat(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
