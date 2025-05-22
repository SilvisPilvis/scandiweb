<?php

namespace App\Model;

class AtributeModel extends Model
{
    public static function findAll($conn)
    {
        $result = $conn->query('SELECT * FROM attributes');
        return $result;
    }

    public static function findById($id, $conn)
    {
        $result = $conn->prepare("SELECT * FROM attributes WHERE id = ?");
        $result->bind_param('i', $id);
        $result->get_result()->fetch_assoc();
        $result->close();
        return $result;
    }

    public static function create($data, $conn)
    {
        $result = $conn->prepare("INSERT INTO attributes (displayValue, value) VALUES (?, ?)");
        $result->bind_param('ss', $data['displayValue'], $data['value']);
        $result->execute();
        $insert_id = $result->insert_id;
        $result->close();
        return $insert_id;
    }

    public static function update($id, $data, $conn)
    {
        $result = $conn->prepare("UPDATE attributes SET displayValue = ?, value = ? WHERE id = ?");
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
