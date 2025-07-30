<?php

namespace App\Model;

class AttributeSetModel
{
    public $id;
    public $name;
    public $type;
    private $conn;

    public function __construct($conn, $id = null, $name = null, $type = null)
    {
        $this->conn = $conn;
    }

    public function findAll()
    {
        $attributeSets = [];
        $rows = $this->conn->query('SELECT id FROM attribute_sets');
        if ($rows === false) {
            $error = $this->conn->error;
            error_log("Database error in AttributeSetModel::findAll: " . $error);
            throw new \RuntimeException("Database error fetching attribute set IDs: " . $error);
        }
        if (!is_array($rows)) {
            error_log("Unexpected return type from DB query in AttributeSetModel::findAll");
            throw new \RuntimeException("Unexpected data format from database query.");
        }
        foreach ($rows as $row) {
            if (isset($row['id'])) {
                $attributeSet = $this->findById($row['id']);
                if ($attributeSet) {
                    $attributeSets[] = $attributeSet;
                }
            }
        }
        return $attributeSets;
    }

    public function findById($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM attribute_sets WHERE id = ?");
        $stmt->execute([$id]);
        $stmt->setFetchMode(\PDO::FETCH_CLASS, AttributeSetModel::class, [$this->conn]);
        $attributeSet = $stmt->fetch();
        if (!$attributeSet) return null;
        return $attributeSet;;
    }

    public function findItemsBySetId($id)
    {
        $stmt = $this->conn->prepare(
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

    public function create($data)
    {
        $stmt = $this->conn->prepare("INSERT INTO attribute_sets (name, type) VALUES (?, ?)");
        $stmt->execute([$data['name'], $data['type']]);
        $insert_id = $this->conn->lastInsertId();
        if ($insert_id) {
            return $this->findById($insert_id);
        }
        return null;
    }

    public function update($id, $data)
    {
        $stmt = $this->conn->prepare("UPDATE attribute_sets SET name = ?, type = ? WHERE id = ?");
        $stmt->execute([$data['name'], $data['type'], $id]);
        return $stmt;
    }
    
    public function delete($id)
    {
        $stmt = $this->conn->prepare("DELETE FROM attribute_sets WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt;
    }
} 