-- -------------------------------------------------------------
-- -------------------------------------------------------------
-- TablePlus 1.2.4
--
-- https://tableplus.com/
--
-- Database: mariadb
-- Generation Time: 2025-06-27 12:31:49.047450
-- -------------------------------------------------------------


DROP TABLE IF EXISTS `if0_39327894_scandiweb`.`attributes`;


CREATE TABLE IF NOT EXISTS `attributes` (
  `id` varchar(255) NOT NULL,
  `display_value` varchar(255) NOT NULL,
  `value` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

DROP TABLE IF EXISTS `if0_39327894_scandiweb`.`categories`;


CREATE TABLE IF NOT EXISTS `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

DROP TABLE IF EXISTS `if0_39327894_scandiweb`.`orders`;


CREATE TABLE IF NOT EXISTS `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `items` varchar(1024) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;


DROP TABLE IF EXISTS `if0_39327894_scandiweb`.`product_attribute_sets`;

CREATE TABLE IF NOT EXISTS `products` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `in_stock` tinyint(1) NOT NULL DEFAULT 1,
  `description` text NOT NULL,
  `category_id` int(11) NOT NULL,
  `brand` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;


DROP TABLE IF EXISTS `if0_39327894_scandiweb`.`prices`;

CREATE TABLE IF NOT EXISTS `prices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` varchar(255) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `currency` varchar(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `prices_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

DROP TABLE IF EXISTS `if0_39327894_scandiweb`.`attribute_sets`;

CREATE TABLE IF NOT EXISTS `attribute_sets` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

DROP TABLE IF EXISTS `if0_39327894_scandiweb`.`attribute_set_items`;

CREATE TABLE IF NOT EXISTS `attribute_set_items` (
  `attribute_set_id` varchar(255) NOT NULL,
  `attribute_id` varchar(255) NOT NULL,
  PRIMARY KEY (`attribute_set_id`,`attribute_id`),
  KEY `attribute_id` (`attribute_id`),
  CONSTRAINT `attribute_set_items_ibfk_1` FOREIGN KEY (`attribute_set_id`) REFERENCES `attribute_sets` (`id`) ON DELETE CASCADE,
  CONSTRAINT `attribute_set_items_ibfk_2` FOREIGN KEY (`attribute_id`) REFERENCES `attributes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

CREATE TABLE IF NOT EXISTS `product_attribute_sets` (
  `product_id` varchar(255) NOT NULL,
  `attribute_set_id` varchar(255) NOT NULL,
  PRIMARY KEY (`product_id`,`attribute_set_id`),
  KEY `attribute_set_id` (`attribute_set_id`),
  CONSTRAINT `product_attribute_sets_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `product_attribute_sets_ibfk_2` FOREIGN KEY (`attribute_set_id`) REFERENCES `attribute_sets` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

DROP TABLE IF EXISTS `if0_39327894_scandiweb`.`product_gallery_images`;

CREATE TABLE IF NOT EXISTS `product_gallery_images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` varchar(255) NOT NULL,
  `image_url` varchar(2048) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `product_gallery_images_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

DROP TABLE IF EXISTS `if0_39327894_scandiweb`.`products`;

INSERT INTO `if0_39327894_scandiweb`.`attribute_set_items` (`attribute_set_id`, `attribute_id`) VALUES 
('Capacity', '1T'),
('Capacity', '256GB'),
('Size', '40'),
('Size', '41'),
('Size', '42'),
('Size', '43'),
('Capacity', '512G'),
('Color', 'Black'),
('Color', 'Blue'),
('Color', 'Cyan'),
('Size', 'Extra Large'),
('Color', 'Green'),
('Size', 'Large'),
('Size', 'Medium'),
('Touch ID in keyboard', 'No'),
('With USB 3 ports', 'No'),
('Size', 'Small'),
('Color', 'White'),
('Touch ID in keyboard', 'Yes'),
('With USB 3 ports', 'Yes');

INSERT INTO `if0_39327894_scandiweb`.`attribute_sets` (`id`, `name`, `type`) VALUES 
('Capacity', 'Capacity', 'text'),
('Color', 'Color', 'swatch'),
('Size', 'Size', 'text'),
('Touch ID in keyboard', 'Touch ID in keyboard', 'text'),
('With USB 3 ports', 'With USB 3 ports', 'text');

INSERT INTO `if0_39327894_scandiweb`.`attributes` (`id`, `display_value`, `value`) VALUES 
('1T', '1T', '1T'),
('256GB', '256GB', '256GB'),
('40', '40', '40'),
('41', '41', '41'),
('42', '42', '42'),
('43', '43', '43'),
('512G', '512G', '512G'),
('Black', 'Black', '#000000'),
('Blue', 'Blue', '#030BFF'),
('Cyan', 'Cyan', '#03FFF7'),
('Extra Large', 'Extra Large', 'XL'),
('Green', 'Green', '#44FF03'),
('Large', 'Large', 'L'),
('Medium', 'Medium', 'M'),
('No', 'No', 'No'),
('Small', 'Small', 'S'),
('White', 'White', '#FFFFFF'),
('Yes', 'Yes', 'Yes');

INSERT INTO `if0_39327894_scandiweb`.`categories` (`id`, `name`) VALUES 
(1, 'all'),
(2, 'clothes'),
(3, 'tech');

INSERT INTO `if0_39327894_scandiweb`.`prices` (`id`, `product_id`, `amount`, `currency`) VALUES 
(1, 'huarache-x-stussy-le', 144.69, 'USD'),
(2, 'jacket-canada-goosee', 518.47, 'USD'),
(3, 'ps-5', 844.02, 'USD'),
(4, 'xbox-series-s', 333.99, 'USD'),
(5, 'apple-imac-2021', 1688.03, 'USD'),
(6, 'apple-iphone-12-pro', 1000.76, 'USD'),
(7, 'apple-airpods-pro', 300.23, 'USD');

INSERT INTO `if0_39327894_scandiweb`.`product_attribute_sets` (`product_id`, `attribute_set_id`) VALUES 
('apple-imac-2021', 'Capacity'),
('apple-iphone-12-pro', 'Capacity'),
('ps-5', 'Capacity'),
('xbox-series-s', 'Capacity'),
('apple-iphone-12-pro', 'Color'),
('ps-5', 'Color'),
('xbox-series-s', 'Color'),
('huarache-x-stussy-le', 'Size'),
('jacket-canada-goosee', 'Size'),
('apple-imac-2021', 'Touch ID in keyboard'),
('apple-imac-2021', 'With USB 3 ports');

INSERT INTO `if0_39327894_scandiweb`.`product_gallery_images` (`id`, `product_id`, `image_url`) VALUES 
(1, 'huarache-x-stussy-le', 'https://cdn.shopify.com/s/files/1/0087/6193/3920/products/DD1381200_DEOA_2_720x.jpg?v=1612816087'),
(2, 'huarache-x-stussy-le', 'https://cdn.shopify.com/s/files/1/0087/6193/3920/products/DD1381200_DEOA_1_720x.jpg?v=1612816087'),
(3, 'huarache-x-stussy-le', 'https://cdn.shopify.com/s/files/1/0087/6193/3920/products/DD1381200_DEOA_3_720x.jpg?v=1612816087'),
(4, 'huarache-x-stussy-le', 'https://cdn.shopify.com/s/files/1/0087/6193/3920/products/DD1381200_DEOA_5_720x.jpg?v=1612816087'),
(5, 'huarache-x-stussy-le', 'https://cdn.shopify.com/s/files/1/0087/6193/3920/products/DD1381200_DEOA_4_720x.jpg?v=1612816087'),
(6, 'jacket-canada-goosee', 'https://images.canadagoose.com/image/upload/w_480,c_scale,f_auto,q_auto:best/v1576016105/product-image/2409L_61.jpg'),
(7, 'jacket-canada-goosee', 'https://images.canadagoose.com/image/upload/w_480,c_scale,f_auto,q_auto:best/v1576016107/product-image/2409L_61_a.jpg'),
(8, 'jacket-canada-goosee', 'https://images.canadagoose.com/image/upload/w_480,c_scale,f_auto,q_auto:best/v1576016108/product-image/2409L_61_b.jpg'),
(9, 'jacket-canada-goosee', 'https://images.canadagoose.com/image/upload/w_480,c_scale,f_auto,q_auto:best/v1576016109/product-image/2409L_61_c.jpg'),
(10, 'jacket-canada-goosee', 'https://images.canadagoose.com/image/upload/w_480,c_scale,f_auto,q_auto:best/v1576016110/product-image/2409L_61_d.jpg'),
(11, 'ps-5', 'https://images-na.ssl-images-amazon.com/images/I/510VSJ9mWDL._SL1262_.jpg'),
(12, 'ps-5', 'https://images-na.ssl-images-amazon.com/images/I/610%2B69ZsKCL._SL1500_.jpg'),
(13, 'ps-5', 'https://images-na.ssl-images-amazon.com/images/I/51iPoFwQT3L._SL1230_.jpg'),
(14, 'ps-5', 'https://images-na.ssl-images-amazon.com/images/I/61qbqFcvoNL._SL1500_.jpg'),
(15, 'ps-5', 'https://images-na.ssl-images-amazon.com/images/I/51HCjA3rqYL._SL1230_.jpg'),
(16, 'xbox-series-s', 'https://images-na.ssl-images-amazon.com/images/I/71vPCX0bS-L._SL1500_.jpg'),
(17, 'xbox-series-s', 'https://images-na.ssl-images-amazon.com/images/I/71q7JTbRTpL._SL1500_.jpg'),
(18, 'xbox-series-s', 'https://images-na.ssl-images-amazon.com/images/I/71iQ4HGHtsL._SL1500_.jpg'),
(19, 'xbox-series-s', 'https://images-na.ssl-images-amazon.com/images/I/61IYrCrBzxL._SL1500_.jpg'),
(20, 'xbox-series-s', 'https://images-na.ssl-images-amazon.com/images/I/61RnXmpAmIL._SL1500_.jpg'),
(21, 'apple-imac-2021', 'https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/imac-24-blue-selection-hero-202104?wid=904&hei=840&fmt=jpeg&qlt=80&.v=1617492405000'),
(22, 'apple-iphone-12-pro', 'https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-12-pro-family-hero?wid=940&amp;hei=1112&amp;fmt=jpeg&amp;qlt=80&amp;.v=1604021663000'),
(23, 'apple-airpods-pro', 'https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/MWP22?wid=572&hei=572&fmt=jpeg&qlt=95&.v=1591634795000');

INSERT INTO `if0_39327894_scandiweb`.`products` (`id`, `name`, `in_stock`, `description`, `category_id`, `brand`) VALUES 
('apple-airpods-pro', 'AirPods Pro', 0, 'Magic like you''ve never heard', 3, 'Apple'),
('apple-imac-2021', 'iMac 2021', 1, 'The new iMac!', 3, 'Apple'),
('apple-iphone-12-pro', 'iPhone 12 Pro', 1, 'This is iPhone 12. Nothing else to say.', 3, 'Apple'),
('huarache-x-stussy-le', 'Nike Air Huarache Le', 1, '<p>Great sneakers for everyday use!</p>', 2, 'Nike x Stussy'),
('jacket-canada-goosee', 'Jacket', 1, '<p>Awesome winter jacket</p>', 2, 'Canada Goose'),
('ps-5', 'PlayStation 5', 1, '<p>A good gaming console. Plays games of PS4! Enjoy if you can buy it mwahahahaha</p>', 3, 'Sony'),
('xbox-series-s', 'Xbox Series S 512GB', 0, '<div><ul><li><span>Hardware-beschleunigtes Raytracing macht dein Spiel noch realistischer</span></li></ul></div>', 3, 'Microsoft');

