<?php

namespace App\Model;

class CategoryModel extends Model
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
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $category = $result->fetch_assoc(); // fetch the data, fam
        $stmt->close();

        // Or return the whole row if you want everything
        return $category;
        // return null; // Or handle not found case as you like
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
        $result = $conn->prepare("UPDATE categories SET name = ? WHERE id = ?");
        $result->bind_param('si', $data['name'], $id);
        $result->execute();
        $result->close();
        return $result;
    }

    public static function delete($id, $conn)
    {
        $result = $conn->prepare("DELETE FROM categories WHERE id = ?");
        $result->bind_param('i', $id);
        $result->execute();
        $result->close();
        return $result;
    }
}
