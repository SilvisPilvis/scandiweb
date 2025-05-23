<?php

namespace App\Model;

class PriceModel extends Model
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
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $price = $result->fetch_assoc();
        $stmt->close();

        return $price;
    }

    public static function findByProductId($id, $conn)
    {
        $stmt = $conn->prepare("SELECT * FROM prices WHERE product_id = ?");
        $stmt->bind_param('s', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $prices = [];
        while ($row = $result->fetch_assoc()) {
            $prices[] = [
                'amount' => (float)$row['amount'],
                'currency' => [
                    'label' => $row['currency'],
                    'symbol' => $row['currency'] === 'USD' ? '$' : $row['currency']
                ]
            ];
        }
        $stmt->close();
        return $prices;
    }

    /**
     * Creates a new price record.
     *
     * @param array   $data Expected to contain 'amount' (float) and 'currency' (array with 'label' and 'symbol').
     * @param \mysqli $conn The database connection.
     *
     * @return array|null An array representing the created price, matching GraphQL PriceType, or null on failure.
     */
    public static function create($data, $conn)
    {
        $result = $conn->prepare("INSERT INTO prices (amount, currency) VALUES (?, ?)");
        $result->bind_param('fs', $data['amount'], $data['currency']);
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
        $result = $conn->prepare("UPDATE prices SET amount = ?, currency = ? WHERE id = ?");
        $result->bind_param('sfi', $data['amount'], $data['currency'], $id);
        $result->execute();
        $result->close();
        return $result;
    }

    public static function delete($id, $conn)
    {
        $result = $conn->prepare("DELETE FROM prices WHERE id = ?");
        $result->bind_param('i', $id);
        $result->execute();
        $result->close();
        return $result;
    }
}
