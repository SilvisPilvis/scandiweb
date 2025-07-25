<?php

namespace App\Controller;

class AttributeSetController extends Controller
{
    public static function findAll($conn)
    {
        $attributeSets = [];
        $rows = $conn->query('SELECT id FROM attribute_sets');

        if ($rows === false) {
            $error = $conn->error;
            error_log("Database error in AtributeSetModel::findAll: " . $error);
            throw new \RuntimeException("Database error fetching attribute set IDs: " . $error);
        }

        if (!is_array($rows)) {
            error_log("Unexpected return type from DB query in AtributeSetModel::findAll");
            throw new \RuntimeException("Unexpected data format from database query.");
        }

        foreach ($rows as $row) {
            if (isset($row['id'])) {
                $attributeSet = self::findById($row['id'], $conn);
                if ($attributeSet) {
                    $attributeSets[] = $attributeSet;
                }
            }
        }
        return $attributeSets;
    }

    public static function findById($id, $conn)
    {
        $stmt = $conn->prepare("SELECT * FROM attribute_sets WHERE id = ?");
        $stmt->execute([$id]);
        $attributeSet = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $attributeSet;
    }

    public static function findItemsBySetId($id, $conn)
    {
        $stmt = $conn->prepare(
            "SELECT a.* 
            FROM attributes a
            JOIN attribute_set_items asi ON a.id = asi.attribute_id
            WHERE asi.attribute_set_id = ?"
        );
        $stmt->execute([$id]);
        $items = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $row['displayValue'] = $row['display_value'];
            unset($row['display_value']);
            $items[] = $row;
        }
        return $items;
    }

    public static function create($data, $conn)
    {
        $stmt = $conn->prepare("INSERT INTO attribute_sets (name, type) VALUES (?, ?)");
        $stmt->execute([$data['name'], $data['type']]);
        $insert_id = $conn->lastInsertId();
        if ($insert_id) {
            return self::findById($insert_id, $conn);
        }
        return null;
    }

    public static function update($id, $data, $conn)
    {
        $stmt = $conn->prepare("UPDATE attribute_sets SET name = ?, type = ? WHERE id = ?");
        $stmt->execute([$data['name'], $data['type'], $id]);
        return $stmt;
    }

    public static function delete($id, $conn)
    {
        $stmt = $conn->prepare("DELETE FROM attribute_sets WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt;
    }
}
