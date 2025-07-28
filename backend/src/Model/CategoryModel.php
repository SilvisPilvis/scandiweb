<?php

namespace App\Model;

class CategoryModel
{
    public $id;
    public $name;

    public function __construct($id, $name)
    {
        $this->id = $id;
        $this->name = $name;
    }

    public static function findAll($conn)
    {
        $categories = [];
        $rows = $conn->query('SELECT id FROM categories');
        if ($rows === false) {
            $error = $conn->error;
            error_log("Database error in CategoryModel::findAll: " . $error);
            throw new \RuntimeException("Database error fetching category IDs: " . $error);
        }
        if (!is_array($rows)) {
            error_log("Unexpected return type from DB query in CategoryModel::findAll");
            throw new \RuntimeException("Unexpected data format from database query.");
        }
        foreach ($rows as $row) {
            if (isset($row['id'])) {
                $category = self::findById($row['id'], $conn);
                if ($category) {
                    $categories[] = $category;
                }
            }
        }
        return $categories;
    }
    
    public static function findById($id, $conn)
    {
        $stmt = $conn->prepare("SELECT * FROM categories WHERE id = ?");
        $stmt->execute([$id]);
        $category = $stmt->fetch(\PDO::FETCH_ASSOC);
        if (!$category) return null;
        return new self($category['id'], $category['name']);
    }

    public static function create($data, $conn)
    {
        $stmt = $conn->prepare("INSERT INTO categories (name) VALUES (?)");
        $stmt->execute([$data['name']]);
        $insert_id = $conn->lastInsertId();
        if ($insert_id) {
            return self::findById($insert_id, $conn);
        }
        return null;
    }

    public static function update($id, $data, $conn)
    {
        $stmt = $conn->prepare("UPDATE categories SET name = ? WHERE id = ?");
        $stmt->execute([$data['name'], $id]);
        return $stmt;
    }

    public static function delete($id, $conn)
    {
        $stmt = $conn->prepare("DELETE FROM categories WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt;
    }
} 