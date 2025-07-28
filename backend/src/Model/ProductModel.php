<?php

namespace App\Model;

class ProductModel
{
    // Instance properties (optional if you mainly use static methods returning arrays)
    public $id;
    public $name;
    public $inStock;
    public $gallery; // Array of strings (URLs)
    public $description;
    public $category; // Category object/array
    public $attributes; // Array of AttributeSet objects/arrays
    public $prices; // Array of Price objects/arrays
    public $brand;

    // Corrected Constructor (if you plan to instantiate ProductModel objects)
    public function __construct($id, $name, $inStock, $gallery, $description, $category, $attributes, $prices, $brand)
    {
        $this->id = $id;
        $this->name = $name;
        $this->inStock = $inStock;
        $this->gallery = $gallery;
        $this->description = $description;
        $this->category = $category;
        $this->attributes = $attributes;
        $this->prices = $prices;
        $this->brand = $brand;
    }

    public static function findAll($conn)
    {
        $products = [];
        $rows = $conn->query('SELECT id FROM products');
        if ($rows === false) {
            $error = $conn->error;
            error_log("Database error in ProductModel::findAll: " . $error);
            throw new \RuntimeException("Database error fetching product IDs: " . $error);
        }
        if (!is_array($rows)) {
            error_log("Unexpected return type from DB query in ProductModel::findAll");
            throw new \RuntimeException("Unexpected data format from database query.");
        }
        foreach ($rows as $row) {
            if (isset($row['id'])) {
                $product = self::findById($row['id'], $conn);
                if ($product) {
                    $products[] = $product;
                }
            }
        }
        return $products;
    }

    public static function findById($id, $conn)
    {
        $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$id]);
        $productData = $stmt->fetch(\PDO::FETCH_ASSOC);
        if (!$productData) return null;
        // You may want to fetch related data for gallery, category, attributes, prices, etc.
        // For now, just pass the DB fields directly
        return new self(
            $productData['id'],
            $productData['name'],
            $productData['in_stock'],
            [], // gallery
            $productData['description'],
            $productData['category_id'],
            [], // attributes
            [], // prices
            $productData['brand']
        );
    }

    public static function create($data, $conn)
    {
        $stmt = $conn->prepare("INSERT INTO products (id, name, in_stock, description, category_id, brand) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$data['id'], $data['name'], $data['in_stock'], $data['description'], $data['category_id'], $data['brand']]);
        $insert_id = $conn->lastInsertId();
        if ($insert_id) {
            return self::findById($insert_id, $conn);
        }
        return null;
    }

    public static function update($id, $data, $conn)
    {
        $stmt = $conn->prepare("UPDATE products SET name = ?, in_stock = ?, description = ?, category_id = ?, brand = ? WHERE id = ?");
        $stmt->execute([$data['name'], $data['in_stock'], $data['description'], $data['category_id'], $data['brand'], $id]);
        return $stmt;
    }
    
    public static function delete($id, $conn)
    {
        $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt;
    }
} 