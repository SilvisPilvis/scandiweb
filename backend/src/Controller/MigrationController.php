<?php

namespace App\Controller;

class MigrationController
{
    public static function migrate($conn)
    {
        $conn->prepare(
            '
            CREATE TABLE categories (
                id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL
            );


            CREATE TABLE attributes (
                id VARCHAR(255) NOT NULL PRIMARY KEY,
                display_value VARCHAR(255) NOT NULL,
                value VARCHAR(255) NOT NULL
            );


            CREATE TABLE attribute_sets (
                id VARCHAR(255) NOT NULL PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                type VARCHAR(255) NOT NULL
            );


            CREATE TABLE products (
                id VARCHAR(255) NOT NULL PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                in_stock BOOLEAN NOT NULL DEFAULT TRUE,
                description TEXT NOT NULL,
                category_id VARCHAR(255) NOT NULL,
                brand VARCHAR(255) NOT NULL,
                FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE RESTRICT
            );


            CREATE TABLE prices (
                id VARCHAR(255) NOT NULL PRIMARY KEY,
                product_id VARCHAR(255) NOT NULL,
                amount DECIMAL(10, 2) NOT NULL,
                currency VARCHAR(10) NOT NULL,
                FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
            );


            CREATE TABLE attribute_set_items (
                attribute_set_id VARCHAR(255) NOT NULL,
                attribute_id VARCHAR(255) NOT NULL,
                PRIMARY KEY (attribute_set_id, attribute_id),
                FOREIGN KEY (attribute_set_id) REFERENCES attribute_sets(id) ON DELETE CASCADE,
                FOREIGN KEY (attribute_id) REFERENCES attributes(id) ON DELETE CASCADE
            );


            CREATE TABLE product_gallery_images (
                id INT AUTO_INCREMENT PRIMARY KEY,
                product_id VARCHAR(255) NOT NULL,
                image_url VARCHAR(2048) NOT NULL,
                FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
            );


            CREATE TABLE product_attribute_sets (
                product_id VARCHAR(255) NOT NULL,
                attribute_set_id VARCHAR(255) NOT NULL,
                PRIMARY KEY (product_id, attribute_set_id),
                FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
                FOREIGN KEY (attribute_set_id) REFERENCES attribute_sets(id) ON DELETE CASCADE
            );
            '
        );
        $conn->execute();
        $insert_id = $conn->insert_id;
        $conn->close();
        return $insert_id;
    }
}
