-- Création de la base de données si elle n'existe pas déjà
CREATE DATABASE IF NOT EXISTS `omnes_immobilier`;

-- Utilisation de la base de données
USE `omnes_immobilier`;

-- Création de la table pour les agents immobiliers
CREATE TABLE IF NOT EXISTS `agents_immobiliers` (
    `agent_id` INT(11) NOT NULL AUTO_INCREMENT,
    `nom` VARCHAR(50) DEFAULT NULL,
    `prenom` VARCHAR(50) DEFAULT NULL,
    `telephone` VARCHAR(15) DEFAULT NULL,
    `email` VARCHAR(100) DEFAULT NULL,
    `specialite` VARCHAR(100) DEFAULT NULL,
    `jours_travail` VARCHAR(100) DEFAULT NULL,
    `mot_de_passe` VARCHAR(255) NOT NULL,
    `photo_id` INT(11) DEFAULT NULL,
    PRIMARY KEY (`agent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertion de 20 agents dans la table
INSERT INTO `agents_immobiliers` (`nom`, `prenom`, `telephone`, `email`, `specialite`, `jours_travail`,`mot_de_passe`, `photo_id`) VALUES
('Rembry', 'Demian', '0678123456', 'rembry.demian@omnesimmobilier.com', 'Vente', 'Lundi, Mardi, Mercredi, Jeudi', '123456', 1),
('Sellito', 'Juliette', '0612345678', 'juliette.sellito@omnesimmobilier.com', 'Location', 'Mardi, Mercredi, Jeudi, Vendredi', 'motdepasse', 2),
('Didenot', 'Jules', '0678456231', 'jules.didenot@omnesimmobilier.com', 'Investissement', 'Lundi, Mercredi, Jeudi, Vendredi', 'mdp', 3),
('Khalifa', 'Mia', '0612314567', 'mia.khalifa@omnesimmobilier.com', 'Commercial', 'Lundi, Mardi, Jeudi, Vendredi', 'ph_tu_coco', 4),
('Thomas', 'Isabelle', '0678965432', 'isabelle.thomas@omnesimmobilier.com', 'Vente', 'Lundi, Mardi, Mercredi, Vendredi', 'azertyuiop', 5),
('Robert', 'Catherine', '0612347890', 'catherine.robert@omnesimmobilier.com', 'Location', 'Lundi, Mardi, Jeudi, Vendredi', 'qsdfghjklm', 6),
('Richard', 'Nicolas', '0678234012', 'nicolas.richard@omnesimmobilier.com', 'Investissement', 'Mardi, Mercredi, Jeudi, Vendredi', 'wxcvbn', 7),
('Petit', 'Julie', '0612789456', 'julie.petit@omnesimmobilier.com', 'Commercial', 'Lundi, Mercredi, Jeudi, Vendredi', 'aqwzsxedc', 8),
('Durand', 'Philippe', '0678456123', 'philippe.durand@omnesimmobilier.com', 'Vente', 'Lundi, Mardi, Mercredi, Jeudi', 'rfvtgbyhn', 9),
('Leroy', 'Caroline', '0612345678', 'caroline.leroy@omnesimmobilier.com', 'Location', 'Mardi, Mercredi, Jeudi, Vendredi', 'ujikolpm', 10),
('Moreau', 'Luc', '0678123456', 'luc.moreau@omnesimmobilier.com', 'Investissement', 'Lundi, Mardi, Mercredi, Vendredi', '741852963', 11),
('Simon', 'Virginie', '0612345678', 'virginie.simon@omnesimmobilier.com', 'Commercial', 'Lundi, Mercredi, Jeudi, Vendredi', 'motdp', 12),
('Leroux', 'Michel', '0678123456', 'michel.leroux@omnesimmobilier.com', 'Vente', 'Lundi, Mardi, Jeudi, Vendredi', 'jesaispas', 13),
('Laurent', 'Christine', '0612345678', 'christine.laurent@omnesimmobilier.com', 'Location', 'Mardi, Mercredi, Jeudi, Vendredi', 'mettezmoiunebonnenote', 14),
('Lefort', 'Paul', '0678123456', 'paul.lefort@omnesimmobilier.com', 'Investissement', 'Lundi, Mardi, Mercredi, Jeudi', 'svp', 15),
('Garcia', 'Sylvie', '0612345678', 'sylvie.garcia@omnesimmobilier.com', 'Commercial', 'Mardi, Mercredi, Jeudi, Vendredi', 'jpslemeilleur', 16),
('David', 'Laurent', '0678123456', 'laurent.david@omnesimmobilier.com', 'Vente', 'Lundi, Mardi, Jeudi, Vendredi', 'louismahllebest', 17),
('Bertrand', 'Martine', '0612345678', 'martine.bertrand@omnesimmobilier.com', 'Location', 'Lundi, Mercredi, Jeudi, Vendredi', 'cedricdoumbe', 18),
('Roux', 'David', '0678123456', 'david.roux@omnesimmobilier.com', 'Investissement', 'Lundi, Mardi, Mercredi, Jeudi', 'jaiplusdinspi', 19),
('Morel', 'Séverin', '0612345678', 'severin.morel@omnesimmobilier.com', 'Commercial','Lundi, Mardi, Mercredi, Vendredi', 'ahahahahaha', 20);

CREATE TABLE IF NOT EXISTS `clients` (
    `client_id` INT(11) NOT NULL AUTO_INCREMENT,
    `nom` VARCHAR(100) NOT NULL,
    `prenom` VARCHAR(100) NOT NULL,
    `adresse` VARCHAR(255) NOT NULL,
    `email` VARCHAR(100) NOT NULL UNIQUE,
    `telephone` VARCHAR(20) NOT NULL,
    `mot_de_passe` VARCHAR(255) NOT NULL,
    `carte_type` VARCHAR(50) DEFAULT NULL,
    `carte_numero` VARCHAR(50) DEFAULT NULL,
    `carte_nom` VARCHAR(100) DEFAULT NULL,
    `carte_expiration` VARCHAR(7) DEFAULT NULL,
    `carte_code` VARCHAR(4) DEFAULT NULL,
    PRIMARY KEY (`client_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


INSERT INTO `clients` (`nom`, `prenom`, `adresse`, `email`, `telephone`, `mot_de_passe`, `carte_type`, `carte_numero`, `carte_nom`, `carte_expiration`, `carte_code`) VALUES
('Alacache', 'Axel', '123 Rue de Paris, 75001 Paris', 'axel.alacache@gmail.com', '0612345678', 'ParkourAndRun1', 'Visa', '4111111111111111', 'Axel Alacache', '12/24', '123'),
('Marcus', 'Celian', '456 Avenue de Lyon, 69002 Lyon', 'celian.marcus@gmail.com', '0678123456', '123456', 'MasterCard', '5500000000000004', 'Celian Marcus', '11/23', '321'),
('Jami', 'Raphael', '789 Rue de Bordeaux, 33000 Bordeaux', 'raph.jami@gmail.com', '0678912345', 'AZEQSDWXC', 'American Express', '340000000000009', 'Raphael Jami', '10/25', '2314');



-- Création de la table pour les biens à vendre
CREATE TABLE IF NOT EXISTS `biens_a_vendre` (
    `bien_id` INT(11) NOT NULL AUTO_INCREMENT,
    `agent_id` INT(11) NOT NULL,
    `lieu` VARCHAR(255) NOT NULL,
    `type` VARCHAR(50) NOT NULL,
    `prix` VARCHAR(255) NOT NULL,
    `superficie` VARCHAR(50) NOT NULL,
    `photo_id` INT(11) DEFAULT NULL,
    `description` TEXT NOT NULL,
    `bien_code` VARCHAR(6) NOT NULL UNIQUE,
    PRIMARY KEY (`bien_id`),
    FOREIGN KEY (`agent_id`) REFERENCES `agents_immobiliers`(`agent_id`)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertion de 20 biens dans la table
INSERT INTO `biens_a_vendre` (`agent_id`, `lieu`, `type`, `photo_id`, `description`, `prix`, `superficie`, `bien_code`) VALUES
(1, '789 Rue de Nice, 06000 Nice', 'Maison', 21, 'Belle maison de 5 pièces avec 3 chambres, étage: Rez-de-chaussée, balcon: Oui. Proche des commodités.', '500,000€', '120m²', 'B1N21C'),
(2, '123 Avenue de la Californie, 06000 Nice', 'Maison', 22, 'Maison confortable avec 4 pièces, 2 chambres, étage: 1er étage, balcon: Oui.', '450,000€', '95m²', 'B1N22C'),
(3, '45 Boulevard Gambetta, 06000 Nice', 'Maison', 23, 'Grande maison familiale avec 6 pièces, 4 chambres, étage: 2e étage, balcon: Oui.', '600,000€', '150m²', 'B2N23G'),
(4, '67 Rue de France, 06000 Nice', 'Maison', 24, 'Petite maison charmante avec 3 pièces, 2 chambres, étage: Rez-de-chaussée, balcon: Non.', '400,000€', '85m²', 'B2N24F'),
(5, '1010 Boulevard de Marseille, 13008 Marseille', 'Maison', 25, 'Maison spacieuse avec 7 pièces, 5 chambres, étage: 3e étage, balcon: Oui.', '750,000€', '200m²', 'B3M25B'),
(6, '123 Rue Paradis, 13008 Marseille', 'Appartement', 26, 'Appartement moderne avec 4 pièces, 2 chambres, étage: 5e étage, balcon: Oui.', '350,000€', '110m²', 'B3M26P'),
(7, '456 Avenue du Prado, 13008 Marseille', 'Appartement', 27, 'Appartement cosy avec 3 pièces, 1 chambre, étage: 2e étage, balcon: Non.', '250,000€', '70m²', 'B4M27D'),
(8, '789 Rue de la République, 13008 Marseille', 'Appartement', 28, 'Appartement de standing avecy. 5 pièces, 3 chambres, étage: 4e étage, balcon: Oui.', '500,000€', '130m²', 'B4M28R'),
(9, '15 Rue de Bordeaux, 33000 Bordeaux', 'Appartement', 29, 'Appartement lumineux avec 4 pièces, 2 chambres, étage: 1er étage, balcon: Oui.', '300,000€', '100m²', 'B5B29T'),
(10, '123 Avenue Thiers, 33000 Bordeaux', 'Appartement', 30, 'Appartement bien situé avec 3 pièces, 1 chambre, étage: 3e étage, balcon: Non.', '200,000€', '85m²', 'B5B30T'),
(11, '45 Boulevard du Président Wilson, 33000 Bordeaux', 'Terrain', 31, 'Grand terrain constructible de 500m² dans un quartier résidentiel.', '100,000€', '500m²', 'B6B31W'),
(12, '67 Rue Sainte-Catherine, 33000 Bordeaux', 'Terrain', 32, 'Terrain spacieux de 750m², idéal pour un projet immobilier.', '150,000€', '750m²', 'B6B32C'),
(13, '123 Avenue de Lille, 59000 Lille', 'Terrain', 33, 'Terrain viabilisé de 600m², prêt à construire.', '120,000€', '600m²', 'B7L33A'),
(14, '456 Rue Nationale, 59000 Lille', 'Terrain', 34, 'Terrain à bâtir de 400m², proche des écoles et commerces.', '90,000€', '400m²', 'B7L34N'),
(15, '789 Boulevard de la Liberté, 59000 Lille', 'Terrain', 35, 'Terrain idéal pour une maison familiale de 550m².', '110,000€', '550m²', 'B8L35L'),
(16, '101 Rue du Molinel, 59000 Lille', 'Commerce', 36, 'Local commercial spacieux avec une grande vitrine.', '300,000€', '200m²', 'B8L36M'),
(17, '456 Rue de Nantes, 44000 Nantes', 'Commerce', 37, 'Local commercial bien situé avec un parking privé.', '280,000€', '180m²', 'B9N37T'),
(18, '123 Boulevard des Anglais, 44000 Nantes', 'Commerce', 38, 'Local commercial moderne, idéal pour bureaux ou boutiques.', '260,000€', '150m²', 'B9N38A'),
(19, '789 Rue de Strasbourg, 44000 Nantes', 'Commerce', 39, 'Local commercial avec un grand espace de stockage.', '220,000€', '120m²', 'B10N39S'),
(20, '101 Rue de la Bastille, 44000 Nantes', 'Commerce', 40, 'Grand local commercial sur deux niveaux, idéal pour showroom.', '400,000€', '220m²', 'B10N40B');


-- Création de la table pour les rendez-vous
CREATE TABLE IF NOT EXISTS `appointments` (
    `appointment_id` INT(11) NOT NULL AUTO_INCREMENT,
    `agent_id` INT(11) NOT NULL,
    `client_id` INT(11) NOT NULL,
    `bien_id` INT(11) NOT NULL,
    `day_of_week` VARCHAR(20) NOT NULL,
    `time` TIME NOT NULL,
    `day_of_month` INT(2) NOT NULL,
    `month` INT(2) NOT NULL,
    `year` INT(4) NOT NULL,
    PRIMARY KEY (`appointment_id`),
    FOREIGN KEY (`agent_id`) REFERENCES `agents_immobiliers`(`agent_id`),
    FOREIGN KEY (`client_id`) REFERENCES `clients`(`client_id`),
    FOREIGN KEY (`bien_id`) REFERENCES `biens_a_vendre`(`bien_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Création de la table pour les administrateurs
CREATE TABLE IF NOT EXISTS `administrateurs` (
    `admin_id` INT(11) NOT NULL AUTO_INCREMENT,
    `nom` VARCHAR(100) NOT NULL,
    `prenom` VARCHAR(100) NOT NULL,
    `email` VARCHAR(100) NOT NULL UNIQUE,
    `telephone` VARCHAR(20) NOT NULL,
    `adresse` VARCHAR(255) NOT NULL,
    `mot_de_passe` VARCHAR(255) NOT NULL,
    PRIMARY KEY (`admin_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertion d'un administrateur
INSERT INTO `administrateurs` (`nom`, `prenom`, `email`, `telephone`, `adresse`, `mot_de_passe`) VALUES
('Mahl Le Best', 'Louis', 'louis.mahl@admin.com', '0612345678', '123 Rue de l administration 75001 Paris', 'lebest');


CREATE TABLE IF NOT EXISTS `chats` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `agent_id` INT(11) NOT NULL,
    `client_id` INT(11) NOT NULL,
    `message` TEXT NOT NULL,
    `sender` ENUM('client', 'agent') NOT NULL,
    `timestamp` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`agent_id`) REFERENCES `agents_immobiliers`(`agent_id`),
    FOREIGN KEY (`client_id`) REFERENCES `clients`(`client_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
