<?php

namespace App\Model;

class ProductModel
{
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
        return $productData;
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