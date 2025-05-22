<?php

namespace App\Model;

class AtributeSetModel extends Model
{
    public static function findAll($conn)
    {
        $result = $conn->query('SELECT * FROM attributeSets');
        return $result;
    }

    public static function findById($id, $conn)
    {
        $result = $conn->prepare("SELECT * FROM attributeSets WHERE id = ?");
        $result->bind_param('i', $id);
        $result->get_result()->fetch_assoc();
        $result->close();
        return $result;
    }

    public static function findItemsBySetId($id, $conn)
    {
        $result = $conn->prepare("SELECT * FROM attributeSets WHERE id = ?");
        $result->bind_param('i', $id);
        $result->get_result()->fetch_assoc();
        $result->close();
        return $result;
    }

    public static function create($data, $conn)
    {
        $result = $conn->prepare("INSERT INTO attributeSets (items, name, type) VALUES (?, ?, ?)");
        $result->bind_param('sss', $data['items'], $data['name'], $data['type']);
        $result->execute();
        $insert_id = $result->insert_id;
        $result->close();
        return $insert_id;
    }

    public static function update($id, $data, $conn)
    {
        $result = $conn->prepare("UPDATE attributeSets SET items = ?, name = ?, type = ? WHERE id = ?");
        $result->bind_param('sssi', $data['items'], $data['name'], $data['type'], $id);
        $result->execute();
        $result->close();
        return $result;
    }

    public static function delete($id, $conn)
    {
        $result = $conn->prepare("DELETE FROM attributeSets WHERE id = ?");
        $result->bind_param('i', $id);
        $result->execute();
        $result->close();
        return $result;
    }
}
