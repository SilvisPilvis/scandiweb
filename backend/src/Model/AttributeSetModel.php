<?php

namespace App\Model;

class AttributeSetModel extends Model
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
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $attributeSet = $result->fetch_assoc();
        $stmt->close();

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
        $stmt->bind_param('s', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $items = [];
        while ($row = $result->fetch_assoc()) {
            $items[] = $row;
        }
        $stmt->close();
        return $items;
    }

    public static function create($data, $conn)
    {
        $result = $conn->prepare("INSERT INTO attribute_sets (name, type) VALUES (?, ?)");
        $result->bind_param('ss', $data['name'], $data['type']);
        $result->execute();
        $insert_id = $result->insert_id;
        $result->close();

        if ($insert_id) {
            return self::findById($insert_id, $conn);
        }
        return null;
    }

    public static function update($id, $data, $conn)
    {
        $result = $conn->prepare("UPDATE attribute_sets SET name = ?, type = ? WHERE id = ?");
        $result->bind_param('sss', $data['name'], $data['type'], $id);
        $result->execute();
        $result->close();
        return $result;
    }

    public static function delete($id, $conn)
    {
        $result = $conn->prepare("DELETE FROM attribute_sets WHERE id = ?");
        $result->bind_param('i', $id);
        $result->execute();
        $result->close();
        return $result;
    }
}
