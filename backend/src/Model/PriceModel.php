<?php

namespace App\Model;

class PriceModel
{
    public $id;
    public $amount;
    public $currency;
    private $conn;

    public function __construct($conn, $id = null, $amount = null, $currency = null)
    {
        $this->conn = $conn;
        $this->id = $id;
        $this->amount = $amount;
        $this->currency = $currency;
    }

    public function findAll()
    {
        $prices = [];
        $rows = $this->conn->query('SELECT * FROM prices');
        if ($rows === false) {
            $error = $this->conn->error;
            error_log("Database error in PriceModel::findAll: " . $error);
            throw new \RuntimeException("Database error fetching price IDs: " . $error);
        }
        if (!is_array($rows)) {
            error_log("Unexpected return type from DB query in PriceModel::findAll");
            throw new \RuntimeException("Unexpected data format from database query.");
        }
        foreach ($rows as $row) {
            $currency = [
                'label' => $row['currency'],
                'symbol' => $row['currency'] === 'USD' ? '$' : $row['currency']
            ];
            $prices[] = new self($this->conn, $row['id'], (float)$row['amount'], $currency);
        }
        return $prices;
    }

    public function findById($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM prices WHERE id = ?");
        $stmt->execute([$id]);
        $stmt->setFetchMode(\PDO::FETCH_CLASS, PriceModel::class, [$this->conn]);
        $price = $stmt->fetch();
        if (!$price) return null;

        return $price;
    }

    public function findByProductId($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM prices WHERE product_id = ?");
        $stmt->execute([$id]);
        $prices = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $prices[] = [
                'amount' => (float)$row['amount'],
                'currency' => [
                    'label' => $row['currency'],
                    'symbol' => $row['currency'] === 'USD' ? '$' : $row['currency']
                ]
            ];
        }
        return $prices;
    }

    public function create($data)
    {
        $stmt = $this->conn->prepare("INSERT INTO prices (amount, currency) VALUES (?, ?)");
        $stmt->execute([$data['amount'], $data['currency']]);
        $insert_id = $this->conn->lastInsertId();
        if ($insert_id) {
            return $this->findById($insert_id);
        }
        return null;
    }

    public function update($id, $data)
    {
        $stmt = $this->conn->prepare("UPDATE prices SET amount = ?, currency = ? WHERE id = ?");
        $stmt->execute([$data['amount'], $data['currency'], $id]);
        return $stmt;
    }

    public function delete($id)
    {
        $stmt = $this->conn->prepare("DELETE FROM prices WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt;
    }
} 