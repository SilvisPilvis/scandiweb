<?php

namespace App\Model;

class PriceModel extends Model
{
    public static function findAll($conn)
    {
        $result = $conn->query('SELECT * FROM prices');
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public static function findById($id, $conn)
    {
        $result = $conn->prepare("SELECT * FROM prices WHERE id = ?");
        $result->bind_param('i', $id);
        $result->get_result()->fetch_assoc();
        $result->close();
        return $result;
    }

    public static function findByProductId($id, $conn)
    {
        $result = $conn->prepare("SELECT * FROM prices WHERE product_id = ?");
        $result->bind_param('i', $id);
        $result->get_result()->fetch_assoc();
        $result->close();
        return $result;
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
            $result_stmt = $conn->prepare("SELECT * FROM prices WHERE id = ?");
            $result_stmt->bind_param('i', $insert_id);
            $result_stmt->execute();
            $record = $result_stmt->get_result()->fetch_assoc();
            $result_stmt->close();
            return $record;
        }

        return null; // Or handle error, L bozo if insert failed
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
