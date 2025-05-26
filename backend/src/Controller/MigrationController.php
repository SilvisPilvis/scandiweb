<?php

namespace App\Controller;

use \App\Database\Database;

class MigrationController
{
    public static function migrate()
    {
        $db = new Database();

        // Split the migrations into separate statements
        $migrations = [
            'CREATE TABLE categories (
                id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL
            )',
            'CREATE TABLE attributes (
                id VARCHAR(255) NOT NULL PRIMARY KEY,
                display_value VARCHAR(255) NOT NULL,
                value VARCHAR(255) NOT NULL
            )',
            'CREATE TABLE attribute_sets (
                id VARCHAR(255) NOT NULL PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                type VARCHAR(255) NOT NULL
            )',
            'CREATE TABLE products (
                id VARCHAR(255) NOT NULL PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                in_stock BOOLEAN NOT NULL DEFAULT TRUE,
                description TEXT NOT NULL,
                category_id INT NOT NULL,
                brand VARCHAR(255) NOT NULL,
                FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE RESTRICT
            )',
            'CREATE TABLE prices (
                id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                product_id VARCHAR(255) NOT NULL,
                amount DECIMAL(10, 2) NOT NULL,
                currency VARCHAR(10) NOT NULL,
                FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
            )',
            'CREATE TABLE attribute_set_items (
                attribute_set_id VARCHAR(255) NOT NULL,
                attribute_id VARCHAR(255) NOT NULL,
                PRIMARY KEY (attribute_set_id, attribute_id),
                FOREIGN KEY (attribute_set_id) REFERENCES attribute_sets(id) ON DELETE CASCADE,
                FOREIGN KEY (attribute_id) REFERENCES attributes(id) ON DELETE CASCADE
            )',
            'CREATE TABLE product_gallery_images (
                id INT AUTO_INCREMENT PRIMARY KEY,
                product_id VARCHAR(255) NOT NULL,
                image_url VARCHAR(2048) NOT NULL,
                FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
            )',
            'CREATE TABLE product_attribute_sets (
                product_id VARCHAR(255) NOT NULL,
                attribute_set_id VARCHAR(255) NOT NULL,
                PRIMARY KEY (product_id, attribute_set_id),
                FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
                FOREIGN KEY (attribute_set_id) REFERENCES attribute_sets(id) ON DELETE CASCADE
            )'
        ];

        // Execute each migration statement
        foreach ($migrations as $migration) {
            $db->query($migration);
        }

        // Insert initial categories
        $categories = ['all', 'clothes', 'tech'];
        foreach ($categories as $category) {
            $db->query("INSERT INTO categories (name) VALUES (?)", [$category]);
        }

        echo "Database migrated successfully";
        return true;
    }

    public static function seed_db()
    {
        $db = new Database();

        // Clear existing data in the correct order (due to foreign key constraints)
        $db->query("SET FOREIGN_KEY_CHECKS = 0");
        
        $tables = [
            'product_gallery_images',
            'product_attribute_sets',
            'attribute_set_items',
            'prices',
            'products',
            'categories',
            'attributes',
            'attribute_sets'
        ];
        
        foreach ($tables as $table) {
            $db->query("TRUNCATE TABLE $table");
        }
        
        $db->query("SET FOREIGN_KEY_CHECKS = 1");

        // Insert categories first
        $categories = ['all', 'clothes', 'tech'];
        foreach ($categories as $category) {
            $db->query("INSERT INTO categories (name) VALUES (?)", [$category]);
        }

        // Insert attribute sets and their values
        // Size attribute set
        $db->query("INSERT INTO attribute_sets (id, name, type) VALUES ('Size', 'Size', 'text')");

        $size_attributes = [
            ["40", "40", "40"],
            ["41", "41", "41"],
            ["42", "42", "42"],
            ["43", "43", "43"],
            ["Small", "S", "Small"],
            ["Medium", "M", "Medium"],
            ["Large", "L", "Large"],
            ["Extra Large", "XL", "Extra Large"]
        ];

        foreach ($size_attributes as $attr) {
            $db->query("INSERT INTO attributes (id, value, display_value) VALUES (?, ?, ?)", $attr);
            $db->query("INSERT INTO attribute_set_items (attribute_set_id, attribute_id) VALUES ('Size', ?)", [$attr[0]]);
        }

        // Color attribute set
        $db->query("INSERT INTO attribute_sets (id, name, type) VALUES ('Color', 'Color', 'swatch')");

        $color_attributes = [
            ["Green", "Green", "#44FF03"],
            ["Cyan", "Cyan", "#03FFF7"],
            ["Blue", "Blue", "#030BFF"],
            ["Black", "Black", "#000000"],
            ["White", "White", "#FFFFFF"]
        ];

        foreach ($color_attributes as $attr) {
            $db->query("INSERT INTO attributes (id, display_value, value) VALUES (?, ?, ?)", $attr);
            $db->query("INSERT INTO attribute_set_items (attribute_set_id, attribute_id) VALUES ('Color', ?)", [$attr[0]]);
        }

        // Capacity attribute set
        $db->query("INSERT INTO attribute_sets (id, name, type) VALUES ('Capacity', 'Capacity', 'text')");

        $capacity_attributes = [
            ["512G", "512G", "512G"],
            ["1T", "1T", "1T"],
            ["256GB", "256GB", "256GB"]
        ];

        foreach ($capacity_attributes as $attr) {
            $db->query("INSERT INTO attributes (id, display_value, value) VALUES (?, ?, ?)", $attr);
            $db->query("INSERT INTO attribute_set_items (attribute_set_id, attribute_id) VALUES ('Capacity', ?)", [$attr[0]]);
        }

        // USB Ports attribute set
        $db->query("INSERT INTO attribute_sets (id, name, type) VALUES ('With USB 3 ports', 'With USB 3 ports', 'text')");

        $usb_attributes = [
            ["Yes", "Yes", "Yes"],
            ["No", "No", "No"]
        ];

        foreach ($usb_attributes as $attr) {
            $db->query("INSERT INTO attributes (id, display_value, value) VALUES (?, ?, ?)", $attr);
            $db->query("INSERT INTO attribute_set_items (attribute_set_id, attribute_id) VALUES ('With USB 3 ports', ?)", [$attr[0]]);
        }

        // Touch ID attribute set
        $db->query("INSERT INTO attribute_sets (id, name, type) VALUES ('Touch ID in keyboard', 'Touch ID in keyboard', 'text')");

        foreach ($usb_attributes as $attr) {
            $db->query("INSERT INTO attribute_set_items (attribute_set_id, attribute_id) VALUES ('Touch ID in keyboard', ?)", [$attr[0]]);
        }

        // Insert products
        $products = [
            [
                'id' => 'huarache-x-stussy-le',
                'name' => 'Nike Air Huarache Le',
                'in_stock' => true,
                'description' => '<p>Great sneakers for everyday use!</p>',
                'category' => 'clothes',
                'brand' => 'Nike x Stussy',
                'price' => 144.69
            ],
            [
                'id' => 'jacket-canada-goosee',
                'name' => 'Jacket',
                'in_stock' => true,
                'description' => '<p>Awesome winter jacket</p>',
                'category' => 'clothes',
                'brand' => 'Canada Goose',
                'price' => 518.47
            ],
            [
                'id' => 'ps-5',
                'name' => 'PlayStation 5',
                'in_stock' => true,
                'description' => '<p>A good gaming console. Plays games of PS4! Enjoy if you can buy it mwahahahaha</p>',
                'category' => 'tech',
                'brand' => 'Sony',
                'price' => 844.02
            ],
            [
                'id' => 'xbox-series-s',
                'name' => 'Xbox Series S 512GB',
                'in_stock' => false,
                'description' => '<div><ul><li><span>Hardware-beschleunigtes Raytracing macht dein Spiel noch realistischer</span></li></ul></div>',
                'category' => 'tech',
                'brand' => 'Microsoft',
                'price' => 333.99
            ],
            [
                'id' => 'apple-imac-2021',
                'name' => 'iMac 2021',
                'in_stock' => true,
                'description' => 'The new iMac!',
                'category' => 'tech',
                'brand' => 'Apple',
                'price' => 1688.03
            ],
            [
                'id' => 'apple-iphone-12-pro',
                'name' => 'iPhone 12 Pro',
                'in_stock' => true,
                'description' => 'This is iPhone 12. Nothing else to say.',
                'category' => 'tech',
                'brand' => 'Apple',
                'price' => 1000.76
            ],
            [
                'id' => 'apple-airpods-pro',
                'name' => 'AirPods Pro',
                'in_stock' => false,
                'description' => 'Magic like you\'ve never heard',
                'category' => 'tech',
                'brand' => 'Apple',
                'price' => 300.23
            ]
        ];

        foreach ($products as $product) {
            // Get category ID
            $result = $db->query("SELECT id FROM categories WHERE name = ?", [$product['category']]);
            $category_id = $result[0]['id'];

            // Insert product
            $db->query(
                "INSERT INTO products (id, name, in_stock, description, category_id, brand) VALUES (?, ?, ?, ?, ?, ?)",
                [
                    $product['id'],
                    $product['name'],
                    $product['in_stock'],
                    $product['description'],
                    $category_id,
                    $product['brand']
                ]
            );

            // Insert price
            $db->query(
                "INSERT INTO prices (product_id, amount, currency) VALUES (?, ?, 'USD')",
                [$product['id'], $product['price']]
            );
        }

        // Product attribute sets mapping
        $product_attributes = [
            'huarache-x-stussy-le' => ['Size'],
            'jacket-canada-goosee' => ['Size'],
            'ps-5' => ['Color', 'Capacity'],
            'xbox-series-s' => ['Color', 'Capacity'],
            'apple-imac-2021' => ['Capacity', 'With USB 3 ports', 'Touch ID in keyboard'],
            'apple-iphone-12-pro' => ['Capacity', 'Color'],
            'apple-airpods-pro' => []
        ];

        foreach ($product_attributes as $product_id => $attribute_sets) {
            foreach ($attribute_sets as $attribute_set) {
                $db->query(
                    "INSERT INTO product_attribute_sets (product_id, attribute_set_id) VALUES (?, ?)",
                    [$product_id, $attribute_set]
                );
            }
        }

        // Insert product gallery images
        $gallery_images = [
            'huarache-x-stussy-le' => [
                'https://cdn.shopify.com/s/files/1/0087/6193/3920/products/DD1381200_DEOA_2_720x.jpg?v=1612816087',
                'https://cdn.shopify.com/s/files/1/0087/6193/3920/products/DD1381200_DEOA_1_720x.jpg?v=1612816087',
                'https://cdn.shopify.com/s/files/1/0087/6193/3920/products/DD1381200_DEOA_3_720x.jpg?v=1612816087',
                'https://cdn.shopify.com/s/files/1/0087/6193/3920/products/DD1381200_DEOA_5_720x.jpg?v=1612816087',
                'https://cdn.shopify.com/s/files/1/0087/6193/3920/products/DD1381200_DEOA_4_720x.jpg?v=1612816087'
            ],
            'jacket-canada-goosee' => [
                'https://images.canadagoose.com/image/upload/w_480,c_scale,f_auto,q_auto:best/v1576016105/product-image/2409L_61.jpg',
                'https://images.canadagoose.com/image/upload/w_480,c_scale,f_auto,q_auto:best/v1576016107/product-image/2409L_61_a.jpg',
                'https://images.canadagoose.com/image/upload/w_480,c_scale,f_auto,q_auto:best/v1576016108/product-image/2409L_61_b.jpg',
                'https://images.canadagoose.com/image/upload/w_480,c_scale,f_auto,q_auto:best/v1576016109/product-image/2409L_61_c.jpg',
                'https://images.canadagoose.com/image/upload/w_480,c_scale,f_auto,q_auto:best/v1576016110/product-image/2409L_61_d.jpg'
            ],
            'ps-5' => [
                'https://images-na.ssl-images-amazon.com/images/I/510VSJ9mWDL._SL1262_.jpg',
                'https://images-na.ssl-images-amazon.com/images/I/610%2B69ZsKCL._SL1500_.jpg',
                'https://images-na.ssl-images-amazon.com/images/I/51iPoFwQT3L._SL1230_.jpg',
                'https://images-na.ssl-images-amazon.com/images/I/61qbqFcvoNL._SL1500_.jpg',
                'https://images-na.ssl-images-amazon.com/images/I/51HCjA3rqYL._SL1230_.jpg'
            ],
            'xbox-series-s' => [
                'https://images-na.ssl-images-amazon.com/images/I/71vPCX0bS-L._SL1500_.jpg',
                'https://images-na.ssl-images-amazon.com/images/I/71q7JTbRTpL._SL1500_.jpg',
                'https://images-na.ssl-images-amazon.com/images/I/71iQ4HGHtsL._SL1500_.jpg',
                'https://images-na.ssl-images-amazon.com/images/I/61IYrCrBzxL._SL1500_.jpg',
                'https://images-na.ssl-images-amazon.com/images/I/61RnXmpAmIL._SL1500_.jpg'
            ],
            'apple-imac-2021' => [
                'https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/imac-24-blue-selection-hero-202104?wid=904&hei=840&fmt=jpeg&qlt=80&.v=1617492405000'
            ],
            'apple-iphone-12-pro' => [
                'https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-12-pro-family-hero?wid=940&amp;hei=1112&amp;fmt=jpeg&amp;qlt=80&amp;.v=1604021663000'
            ],
            'apple-airpods-pro' => [
                'https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/MWP22?wid=572&hei=572&fmt=jpeg&qlt=95&.v=1591634795000'
            ]
        ];

        foreach ($gallery_images as $product_id => $images) {
            foreach ($images as $image_url) {
                $db->query(
                    "INSERT INTO product_gallery_images (product_id, image_url) VALUES (?, ?)",
                    [$product_id, $image_url]
                );
            }
        }

        echo "Database seeded successfully";
        return true;
    }
}
