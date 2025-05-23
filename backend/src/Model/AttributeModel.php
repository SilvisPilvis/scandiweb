<?php

namespace App\Model;

class AttributeModel extends Model
{
    public static function findAll($conn)
    {
        $attributes = [];
        $rows = $conn->query('SELECT id FROM attributes');

        if ($rows === false) {
            $error = $conn->error;
            error_log("Database error in AtributeModel::findAll: " . $error);
            throw new \RuntimeException("Database error fetching attribute IDs: " . $error);
        }

        if (!is_array($rows)) {
            error_log("Unexpected return type from DB query in AtributeModel::findAll");
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
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $attribute = $result->fetch_assoc();
        $stmt->close();
        $attribute['displayValue'] = $attribute['display_value'];
        unset($attribute['display_value']);

        return $attribute;
    }

    public static function create($data, $conn)
    {
        $result = $conn->prepare("INSERT INTO attributes (display_value, value) VALUES (?, ?)");
        $result->bind_param('ss', $data['displayValue'], $data['value']);
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
        $result = $conn->prepare("UPDATE attributes SET display_value = ?, value = ? WHERE id = ?");
        $result->bind_param('ssi', $data['displayValue'], $data['value'], $id);
        $result->execute();
        $result->close();
        return $result;
    }

    public static function delete($id, $conn)
    {
        $result = $conn->prepare("DELETE FROM attributes WHERE id = ?");
        $result->bind_param('i', $id);
        $result->execute();
        $result->close();
        return $result;
    }
}
