<?php

namespace App\Model;

class CategoryModel
{
    public $id;
    public $name;
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function findAll()
    {
        $categories = [];
        $rows = $this->conn->query('SELECT id FROM categories');
        if ($rows === false) {
            $error = $this->conn->error;
            error_log("Database error in CategoryModel::findAll: " . $error);
            throw new \RuntimeException("Database error fetching category IDs: " . $error);
        }
        if (!is_array($rows)) {
            error_log("Unexpected return type from DB query in CategoryModel::findAll");
            throw new \RuntimeException("Unexpected data format from database query.");
        }
        foreach ($rows as $row) {
            if (isset($row['id'])) {
                $category = $this->findById($row['id']);
                if ($category) {
                    $categories[] = $category;
                }
            }
        }
        return $categories;
    }
    
    public function findById($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM categories WHERE id = ?");
        $stmt->execute([$id]);
        $stmt->setFetchMode(\PDO::FETCH_CLASS, CategoryModel::class, [$this->conn]);
        $category = $stmt->fetch();
        if (!$category) return null;
        if (!isset($category->name) || $category->name === null || trim($category->name) === '') {
            throw new \RuntimeException("Category with id $id has a null or empty name, which is not allowed for GraphQL non-nullable field 'Category.name'.");
        }

        return $category;
    }

    public function create($data)
    {
        $stmt = $this->conn->prepare("INSERT INTO categories (name) VALUES (?)");
        $stmt->execute([$data['name']]);
        $insert_id = $this->conn->lastInsertId();
        if ($insert_id) {
            return $this->findById($insert_id);
        }
        return null;
    }

    public function update($id, $data)
    {
        $stmt = $this->conn->prepare("UPDATE categories SET name = ? WHERE id = ?");
        $stmt->execute([$data['name'], $id]);
        return $stmt;
    }

    public function delete($id)
    {
        $stmt = $this->conn->prepare("DELETE FROM categories WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt;
    }

    public function toArray()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
        ];
    }
} 