<?php

namespace App\Model;

class AttributeModel
{
    public $id;
    public $displayValue;
    public $value;
    private $conn;

    public function __construct($conn, $id = null, $displayValue = null, $value = null)
    {
        $this->conn = $conn;
        $this->id = $id;
        $this->displayValue = $displayValue;
        $this->value = $value;
    }

    public function findAll()
    {
        $attributes = [];
        $rows = $this->conn->query('SELECT id FROM attributes');
        if ($rows === false) {
            $error = $this->conn->error;
            error_log("Database error in AttributeModel::findAll: " . $error);
            throw new \RuntimeException("Database error fetching attribute IDs: " . $error);
        }
        if (!is_array($rows)) {
            error_log("Unexpected return type from DB query in AttributeModel::findAll");
            throw new \RuntimeException("Unexpected data format from database query.");
        }
        foreach ($rows as $row) {
            if (isset($row['id'])) {
                $attribute = $this->findById($row['id']);
                if ($attribute) {
                    $attributes[] = $attribute;
                }
            }
        }
        return $attributes;
    }

    public function findById($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM attributes WHERE id = ?");
        $stmt->execute([$id]);
        $stmt->setFetchMode(\PDO::FETCH_CLASS, AttributeModel::class, [$this->conn]);
        $attribute = $stmt->fetch();
        if (!$attribute) return null;
        $displayValue = $attribute['display_value'];
        return $attribute;
    }

    public function create($data)
    {
        $stmt = $this->conn->prepare("INSERT INTO attributes (display_value, value) VALUES (?, ?)");
        $stmt->execute([$data['displayValue'], $data['value']]);
        $insert_id = $this->conn->lastInsertId();
        if ($insert_id) {
            return $this->findById($insert_id);
        }
        return null;
    }

    public function update($id, $data)
    {
        $stmt = $this->conn->prepare("UPDATE attributes SET display_value = ?, value = ? WHERE id = ?");
        $stmt->execute([$data['displayValue'], $data['value'], $id]);
        return $stmt;
    }

    public function delete($id)
    {
        $stmt = $this->conn->prepare("DELETE FROM attributes WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt;
    }
} 