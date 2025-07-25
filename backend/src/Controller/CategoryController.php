<?php

namespace App\Controller;

class CategoryController extends Controller
{
    private $_id;
    private $_name;

    public function __construct($id, $name, $conn)
    {
        $id = $id;
        $name = $name;
    }

    public static function findAll($conn)
    {
        $categories = [];
        $rows = $conn->query('SELECT id FROM categories');

        if ($rows === false) {
            // Check if the query failed
            $error = $conn->error; // Or however your $conn object exposes errors
            error_log("Database error in CategoryModel::findAll: " . $error);
            throw new \RuntimeException("Database error fetching category IDs: " . $error);
        }

        if (!is_array($rows)) {
            // Extra check if it might return something else weird
            error_log("Unexpected return type from DB query in CategoryModel::findAll");
            throw new \RuntimeException("Unexpected data format from database query.");
        }

        foreach ($rows as $row) {
            // Loop through the array of rows
            if (isset($row['id'])) {
                $category = self::findById($row['id'], $conn);
                if ($category) {
                    $categories[] = $category;
                }
            }
        }

        // No $result->close() needed because $conn->query() already fetched everything
        return $categories;
    }

    public static function findById($id, $conn)
    {
        $stmt = $conn->prepare("SELECT * FROM categories WHERE id = ?");
        $stmt->execute([$id]);
        $category = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $category;
    }

    public static function create($data, $conn)
    {
        $sql = "INSERT INTO categories (name) VALUES (?)";
        // Use the new insert method that takes params and returns lastInsertId
        $insert_id = $conn->insert($sql, ['name' => $data['name']]);

        if ($insert_id) {
            return self::findById($insert_id, $conn);
        } else {
            return null;
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
