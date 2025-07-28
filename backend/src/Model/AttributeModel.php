<?php

namespace App\Model;

class AttributeModel
{
    public static function findAll($conn)
    {
        $attributes = [];
        $rows = $conn->query('SELECT id FROM attributes');
        if ($rows === false) {
            $error = $conn->error;
            error_log("Database error in AttributeModel::findAll: " . $error);
            throw new \RuntimeException("Database error fetching attribute IDs: " . $error);
        }
        if (!is_array($rows)) {
            error_log("Unexpected return type from DB query in AttributeModel::findAll");
            throw new \RuntimeException("Unexpected data format from database query.");
        }
        foreach ($rows as $row) {
            if (isset($row['id'])) {
                $attribute = self::findById($row['id'], $conn);
                if ($attribute) {
                    $attributes[] = $attribute;
                }
            }
        }
        return $attributes;
    }
    public static function findById($id, $conn)
    {
        $stmt = $conn->prepare("SELECT * FROM attributes WHERE id = ?");
        $stmt->execute([$id]);
        $attribute = $stmt->fetch(\PDO::FETCH_ASSOC);
        $attribute['displayValue'] = $attribute['display_value'];
        unset($attribute['display_value']);
        return $attribute;
    }
    public static function create($data, $conn)
    {
        $stmt = $conn->prepare("INSERT INTO attributes (display_value, value) VALUES (?, ?)");
        $stmt->execute([$data['displayValue'], $data['value']]);
        $insert_id = $conn->lastInsertId();
        if ($insert_id) {
            return self::findById($insert_id, $conn);
        }
        return null;
    }
    public static function update($id, $data, $conn)
    {
        $stmt = $conn->prepare("UPDATE attributes SET display_value = ?, value = ? WHERE id = ?");
        $stmt->execute([$data['displayValue'], $data['value'], $id]);
        return $stmt;
    }
    public static function delete($id, $conn)
    {
        $stmt = $conn->prepare("DELETE FROM attributes WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt;
    }
} 