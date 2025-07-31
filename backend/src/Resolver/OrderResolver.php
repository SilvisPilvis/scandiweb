<?php

namespace App\Resolver;

use App\Database\Database;
use Exception;
use PDO;

class OrderResolver
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * Find an order by ID
     * @param string $id Order ID
     * @return array
     * @throws Exception
     */
    public function findOrderById(string $id): array
    {
        $stmt = $this->db->prepare("SELECT * FROM orders WHERE id = ?");
        $stmt->execute([$id]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$order) {
            throw new Exception('Order not found');
        }
        
        return $order;
    }

    /**
     * Create a new order
     * @param array $args Arguments containing order data
     * @return array
     * @throws Exception
     */
    public function createOrder(array $args): array
    {
        if (empty($args['input']['items'])) {
            throw new \InvalidArgumentException('Order items are required');
        }

        // Serialize items if array, otherwise use as-is
        $itemsToInsert = is_array($args['input']['items']) 
            ? json_encode($args['input']['items']) 
            : $args['input']['items'];

        // Insert order and get ID
        $orderId = $this->db->insert(
            "INSERT INTO orders (items) VALUES (?)",
            [$itemsToInsert]
        );

        if ($orderId === false) {
            throw new \RuntimeException("Failed to create order");
        }

        return $this->findOrderById($orderId);
    }
}