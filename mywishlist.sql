DROP TABLE IF EXISTS `lists_messages`;
DROP TABLE IF EXISTS `items_reservations`;
DROP TABLE IF EXISTS `founding_pots_participations`;
DROP TABLE IF EXISTS `founding_pots`;
DROP TABLE IF EXISTS `items`;
DROP TABLE IF EXISTS `lists`;
DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `firstname` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `lastname` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `lists` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `expiration` date DEFAULT NULL,
  `token` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `is_public` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_lists_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `list_id` int(11) NOT NULL,
  `name` text NOT NULL,
  `description` text DEFAULT NULL,
  `image` text DEFAULT NULL,
  `url` text DEFAULT NULL,
  `price` decimal(5,2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_items_lists` FOREIGN KEY (`list_id`) REFERENCES `lists` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `founding_pots` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) NOT NULL,
  `amount` decimal(5,2) NOT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_founding_pots_items` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `founding_pots_participations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `amount` decimal(5,2) NOT NULL,
  `founding_pot_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_founding_pots_participations` FOREIGN KEY (`founding_pot_id`) REFERENCES `founding_pots` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_founding_pots_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `items_reservations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_items_reservations_items` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_items_reservations_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `lists_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
	`list_id` INT(11) NOT NULL,
	`user_id` INT(11) NOT NULL,
	`message` VARCHAR(1024) NOT NULL,
	PRIMARY KEY (`id`),
	CONSTRAINT `fk_lists_messages_lists` FOREIGN KEY (`list_id`) REFERENCES `lists` (`id`) ON DELETE CASCADE,
	CONSTRAINT `fk_lists_messages_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `users` (`id`, `firstname`, `lastname`, `email`, `password`) VALUES
(1, 'Jean', 'Dupont', 'a@a.com', '$2y$12$WD8JZg.SitaDw.n6pFkxuuPLLWSKRSPZ8lspQ1n4KdnSbrjZsBOd.'),
(2, 'Jeanne', 'Maria', 'b@b.com', '$2y$12$WD8JZg.SitaDw.n6pFkxuuPLLWSKRSPZ8lspQ1n4KdnSbrjZsBOd.'),
(3, 'Jeanne2', 'Maria2', 'c@c.com', '$2y$12$WD8JZg.SitaDw.n6pFkxuuPLLWSKRSPZ8lspQ1n4KdnSbrjZsBOd.');

INSERT INTO `lists` (`id`, `user_id`, `title`, `description`, `expiration`, `token`, `is_public`) VALUES
(1, 1, 'Pour fêter le bac !', 'Pour un week-end à Nancy qui nous fera oublier les épreuves. ', '2018-06-27', 'nosecure1', true),
(2, 2, 'lists de mariage d\'Alice et Bob', 'Nous souhaitons passer un week-end royal à Nancy pour notre lune de miel :)', '2018-06-30', 'nosecure2', false),
(3, 3, 'C\'est l\'anniversaire de Charlie', 'Pour lui préparer une fête dont il se souviendra :)', '2017-12-12', 'nosecure3', false);

INSERT INTO `items` (`id`, `list_id`, `name`, `description`, `image`, `url`, `price`) VALUES
(1, 2, 'Champagne', 'Bouteille de champagne + flutes + jeux à gratter', 'champagne.jpg', '', 20.00),
(2, 2, 'Musique', 'Partitions de piano à 4 mains', 'musique.jpg', '', 25.00),
(3, 2, 'Exposition', 'Visite guidée de l’exposition ‘REGARDER’ à la galerie Poirel', 'poirelregarder.jpg', '', 14.00),
(4, 3, 'Goûter', 'Goûter au FIFNL', 'gouter.jpg', '', 20.00),
(5, 3, 'Projection', 'Projection courts-métrages au FIFNL', 'film.jpg', '', 10.00),
(6, 2, 'Bouquet', 'Bouquet de roses et Mots de Marion Renaud', 'rose.jpg', '', 16.00),
(7, 2, 'Diner Stanislas', 'Diner à La Table du Bon Roi Stanislas (Apéritif /Entrée / Plat / Vin / Dessert / Café / Digestif)', 'bonroi.jpg', '', 60.00),
(8, 3, 'Origami', 'Baguettes magiques en Origami en buvant un thé', 'origami.jpg', '', 12.00),
(9, 3, 'Livres', 'Livre bricolage avec petits-enfants + Roman', 'bricolage.jpg', '', 24.00),
(10, 2, 'Diner  Grand Rue ', 'Diner au Grand’Ru(e) (Apéritif / Entrée / Plat / Vin / Dessert / Café)', 'grandrue.jpg', '', 59.00),
(11, 3, 'Visite guidée', 'Visite guidée personnalisée de Saint-Epvre jusqu’à Stanislas', 'place.jpg', '', 11.00),
(12, 2, 'Bijoux', 'Bijoux de manteau + Sous-verre pochette de disque + Lait après-soleil', 'bijoux.jpg', '', 29.00),
(13, 3, 'Jeu contacts', 'Jeu pour échange de contacts', 'contact.png', '', 5.00),
(14, 3, 'Concert', 'Un concert à Nancy', 'concert.jpg', '', 17.00),
(15, 1, 'Appart Hotel', 'Appart’hôtel Coeur de Ville, en plein centre-ville', 'apparthotel.jpg', '', 56.00),
(16, 2, 'Hôtel d\'Haussonville', 'Hôtel d\'Haussonville, au coeur de la Vieille ville à deux pas de la place Stanislas', 'hotel_haussonville_logo.jpg', '', 169.00),
(17, 1, 'Boite de nuit', 'Discothèque, Boîte tendance avec des soirées à thème & DJ invités', 'boitedenuit.jpg', '', 32.00),
(18, 1, 'Planètes Laser', 'Laser game : Gilet électronique et pistolet laser comme matériel, vous voilà équipé.', 'laser.jpg', '', 15.00),
(19, 1, 'Fort Aventure', 'Découvrez Fort Aventure à Bainville-sur-Madon, un site Accropierre unique en Lorraine ! Des Parcours Acrobatiques pour petits et grands, Jeu Mission Aventure, Crypte de Crapahute, Tyrolienne, Saut à l\'élastique inversé, Toboggan géant... et bien plus encore.', 'fort.jpg', '', 25.00);

INSERT INTO `founding_pots` (`id`, `item_id`, `amount`) VALUES
(1, 2, 25.00),
(2, 7, 60.00),
(3, 16, 100.00);

INSERT INTO `items_reservations` (`id`, `item_id`, `user_id`, `message`) VALUES
(1, 1, 1, 'Je t\'offre cette bouteille de champagne, déguste la bien.');
