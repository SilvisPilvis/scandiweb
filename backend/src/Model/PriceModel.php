<?php

namespace App\Model;

class PriceModel
{
    public static function findAll($conn)
    {
        $prices = [];
        $rows = $conn->query('SELECT * FROM prices');
        if ($rows === false) {
            $error = $conn->error;
            error_log("Database error in PriceModel::findAll: " . $error);
            throw new \RuntimeException("Database error fetching price IDs: " . $error);
        }
        if (!is_array($rows)) {
            error_log("Unexpected return type from DB query in PriceModel::findAll");
            throw new \RuntimeException("Unexpected data format from database query.");
        }
        foreach ($rows as $row) {
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
    public static function findById($id, $conn)
    {
        $stmt = $conn->prepare("SELECT * FROM prices WHERE id = ?");
        $stmt->execute([$id]);
        $price = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $price;
    }
    public static function findByProductId($id, $conn)
    {
        $stmt = $conn->prepare("SELECT * FROM prices WHERE product_id = ?");
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
    public static function create($data, $conn)
    {
        $stmt = $conn->prepare("INSERT INTO prices (amount, currency) VALUES (?, ?)");
        $stmt->execute([$data['amount'], $data['currency']]);
        $insert_id = $conn->lastInsertId();
        if ($insert_id) {
            return self::findById($insert_id, $conn);
        }
        return null;
    }
    public static function update($id, $data, $conn)
    {
        $stmt = $conn->prepare("UPDATE prices SET amount = ?, currency = ? WHERE id = ?");
        $stmt->execute([$data['amount'], $data['currency'], $id]);
        return $stmt;
    }
    public static function delete($id, $conn)
    {
        $stmt = $conn->prepare("DELETE FROM prices WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt;
    }
} 