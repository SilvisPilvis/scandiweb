<?php

/**
 * ProductModel
 * php version  8.2
 *
 * @category    Model
 * @description ProductModel class for all products
 * @package     App\Model
 * @author      Silvestrs Lignickis <silvestrsl47@gmail.com>
 * @license     https://www.apache.org/licenses/LICENSE-2.0 Apache License 2.0
 * @version     GIT: main
 * @link        None
 */

namespace App\Controller;

use App\Database\Database;
use App\Model\AttributeModel;
use App\Model\AttributeSetModel;
use App\Model\CategoryModel;
use App\Model\PriceModel;
use Exception;
use App\Packages\Cuid\Cuid;
use App\Model\ProductModel;

/**
 * Class ProductModel
 *
 * @category Model
 * @package  App\Model
 * @author   Silvestrs Lignickis <silvestrsl47@gmail.com>
 * @license  https://www.apache.org/licenses/LICENSE-2.0 Apache License 2.0
 * @link     None
 */

class ProductController extends Controller
{
    private $productModel;
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
        $this->productModel = new \App\Model\ProductModel($conn);
    }

    public function findAll()
    {
        return $this->productModel->findAll();
    }

    public function findById($id)
    {
        return $this->productModel->findById($id);
    }

    public function findByCategory($category)
    {
        $products = [];
        $stmt = $this->conn->prepare("SELECT p.id FROM products p JOIN categories c ON p.category_id = c.id WHERE c.name = ?");
        $stmt->execute([$category]);
        $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        if ($result === false) {
            error_log("Database error in ProductModel::findByCategory (fetching IDs): " . $this->conn->error);
            throw new \RuntimeException("Database error fetching product IDs by category.");
        }
        foreach ($result as $row) {
            if (isset($row['id'])) {
                $product = $this->findById($row['id']);
                if ($product) {
                    $products[] = $product;
                }
            }
        }
        return $products;
    }

    public function create($data)
    {
        return $this->productModel->create($data);
    }

    public function update($id, $data)
    {
        return $this->productModel->update($id, $data);
    }

    public static function findOrderById($id, $conn) {
        $stmt = $conn->prepare("SELECT * FROM orders WHERE id = ?");
        $stmt->execute([$id]);
        $order = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $order;
    }

    public static function create_order($conn, $data) {
        // $conn is an instance of App\Database\Database
        // If $data['items'] is correctly formatted for insertion (e.g., serialized JSON)

        // You might need to adjust how $data['items'] is handled for the INSERT.
        // If it's an array, you probably need to serialize it (e.g., json_encode).
        // Example adjustment if items is an array:
        $itemsToInsert = is_array($data['items']) ? json_encode($data['items']) : $data['items'];

        // Use the Database wrapper's insert method
        // Note: The insert method returns the last insert ID (string|false)
        $orderId = $conn->insert("INSERT INTO orders (items) VALUES (?)", [$itemsToInsert]);

        if ($orderId === false) {
            // Handle insertion failure
            throw new \RuntimeException("Failed to insert order or retrieve order ID.");
        }

        return self::findOrderById($orderId, $conn);
    }

    public function delete($id)
    {
        return $this->productModel->delete($id);
    }
}
