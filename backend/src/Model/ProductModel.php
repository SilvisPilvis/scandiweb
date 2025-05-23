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

namespace App\Model;

use App\Database\Database;
use App\Model\AttributeModel;
use App\Model\AttributeSetModel;
use App\Model\CategoryModel;
use App\Model\PriceModel;
use Exception;
use App\Packages\Cuid\Cuid;

/**
 * Class ProductModel
 *
 * @category Model
 * @package  App\Model
 * @author   Silvestrs Lignickis <silvestrsl47@gmail.com>
 * @license  https://www.apache.org/licenses/LICENSE-2.0 Apache License 2.0
 * @link     None
 */

class ProductModel extends Model
{
    // Instance properties (optional if you mainly use static methods returning arrays)
    private $_id;
    private $_name;
    private $_inStock;
    private $_gallery; // Array of strings (URLs)
    private $_description;
    private $_category; // Category object/array
    private $_attributes; // Array of AttributeSet objects/arrays
    private $_prices; // Array of Price objects/arrays
    private $_brand;

    // Corrected Constructor (if you plan to instantiate ProductModel objects)
    public function __construct($id, $name, $inStock, $gallery, $description, $category, $attributes, $prices, $brand)
    {
        $this->_id = $id;
        $this->_name = $name;
        $this->_inStock = $inStock;
        $this->_gallery = $gallery;
        $this->_description = $description;
        $this->_category = $category;
        $this->_attributes = $attributes;
        $this->_prices = $prices;
        $this->_brand = $brand;
    }

    // findAll would also need to fetch related data for full product objects
    public static function findAll($conn)
    {
        $products = [];
        $rows = $conn->query('SELECT id FROM products'); // Get all product IDs
        
        if ($rows === false) { // Check if the query failed
            $error = $conn->error; // Or however your $conn object exposes errors
            error_log("Database error in ProductModel::findAll: " . $error);
            throw new \RuntimeException("Database error fetching product IDs: " . $error);
        }

        if (!is_array($rows)) {
            // Extra check if it might return something else weird
            error_log("Unexpected return type from DB query in ProductModel::findAll");
            throw new \RuntimeException("Unexpected data format from database query.");
        }
        
        foreach ($rows as $row) {
            if (isset($row['id'])) {
                $product = self::findById($row['id'], $conn);
                if ($product) {
                    $products[] = $product;
                }
            }
        }
        
        return $products;
    }

    public static function findById($id, $conn)
    {
        // 1. Fetch main product data
        $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->bind_param('s', $id);
        $stmt->execute();
        $productData = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$productData) {
            return null;
        }

        // 2. Fetch category (assuming CategoryModel::findById exists)
        if (!empty($productData['category_id'])) {
            // Assuming CategoryModel::findById returns an array like {name: "Name"}
            $categoryDetails = CategoryModel::findById($productData['category_id'], $conn);
            $productData['category'] = $categoryDetails;
        } else {
            $productData['category'] = null; // Or handle as error
        }

        // 3. Fetch gallery images
        $galleryStmt = $conn->prepare("SELECT image_url FROM product_gallery_images WHERE product_id = ?");
        $galleryStmt->bind_param('s', $id);
        $galleryStmt->execute();
        $galleryResult = $galleryStmt->get_result();
        $gallery = [];
        while ($row = $galleryResult->fetch_assoc()) {
            $gallery[] = $row['image_url'];
        }
        $productData['gallery'] = $gallery;
        $galleryStmt->close();

        // 4. Fetch prices (assuming PriceModel::findByProductId exists and returns structured price data)
        // PriceModel::findByProductId should handle currency object construction
        $productData['prices'] = PriceModel::findByProductId($id, $conn);


        // 5. Fetch attribute sets and their items
        $attributeSets = [];
        $asStmt = $conn->prepare(
            "SELECT aset.id, aset.name, aset.type
             FROM attribute_sets aset
             JOIN product_attribute_sets pas ON aset.id = pas.attribute_set_id
             WHERE pas.product_id = ?"
        );
        $asStmt->bind_param('s', $id);
        $asStmt->execute();
        $asResult = $asStmt->get_result();
        while ($asRow = $asResult->fetch_assoc()) {
            $currentSet = $asRow;
            $currentSet['items'] = AttributeSetModel::findItemsBySetId($asRow['id'], $conn); // Assumes this method exists
            $attributeSets[] = $currentSet;
        }
        $productData['attributes'] = $attributeSets;
        $asStmt->close();

        // Ensure inStock is boolean for GraphQL
        $productData['inStock'] = (bool)$productData['in_stock'];
        unset($productData['in_stock']); // Remove the old field name

        return $productData; // Returns an associative array
    }

    public static function create($data, $conn)
    {
        // Generate ID if not provided (using ramsey/uuid as an example)
        // $productId = $data['id'] ?? Uuid::uuid4()->toString();
        $productId = $data['id'] ?? Cuid::generate(8);

        // Convert boolean inStock to integer for DB
        $inStockDb = isset($data['inStock']) ? (int)$data['inStock'] : 1;

        // Start transaction
        $conn->begin_transaction();

        try {
            // 1. Insert into products table
            $stmt = $conn->prepare(
                "INSERT INTO products (id, name, in_stock, description, category_id, brand)
                 VALUES (?, ?, ?, ?, ?, ?)"
            );
            // 's' for id, 's' for name, 'i' for in_stock, 's' for description, 's' for category_id, 's' for brand
            $stmt->bind_param(
                'ssisss',
                $productId,
                $data['name'],
                $inStockDb,
                $data['description'],
                $data['category'], // This is category_id
                $data['brand']
            );
            $stmt->execute();
            if ($stmt->affected_rows === 0) {
                throw new Exception("Failed to create product main entry.");
            }
            $stmt->close();

            // 2. Insert gallery images
            if (!empty($data['gallery']) && is_array($data['gallery'])) {
                $galleryStmt = $conn->prepare(
                    "INSERT INTO product_gallery_images (product_id, image_url) VALUES (?, ?)"
                );
                foreach ($data['gallery'] as $imageUrl) {
                    $galleryStmt->bind_param('ss', $productId, $imageUrl);
                    $galleryStmt->execute();
                }
                $galleryStmt->close();
            }

            // 3. Create prices (delegating to PriceModel)
            // Assumes PriceModel::create handles its own ID and currency details
            // and takes $productId as an argument.
            if (!empty($data['prices']) && is_array($data['prices'])) {
                foreach ($data['prices'] as $priceInput) {
                    // PriceModel::create needs to be adapted to take product_id
                    // and the $priceInput should contain 'amount' and 'currency' (object)
                    PriceModel::create($priceInput, $productId, $conn);
                }
            }

            // 4. Create attribute sets and link them
            // IMPORTANT: This assumes $data['attributes'] is an array of AttributeSetInput-like structures
            // E.g., [{id: "colors", name:"Color", type:"swatch", items: [{id:"red", value:"#FF0000", displayValue:"Red"}]}]
            if (!empty($data['attributes']) && is_array($data['attributes'])) {
                foreach ($data['attributes'] as $attributeSetInput) {
                    // AttributeSetModel::create should create the set and its items, returning the set ID or full set object
                    $createdAttributeSet = AttributeSetModel::create($attributeSetInput, $conn);
                    $attributeSetId = is_array($createdAttributeSet) ? $createdAttributeSet['id'] : $createdAttributeSet; // Adjust based on what AttributeSetModel::create returns

                    if ($attributeSetId) {
                        $linkStmt = $conn->prepare(
                            "INSERT INTO product_attribute_sets (product_id, attribute_set_id) VALUES (?, ?)"
                        );
                        $linkStmt->bind_param('ss', $productId, $attributeSetId);
                        $linkStmt->execute();
                        $linkStmt->close();
                    }
                }
            }

            $conn->commit();

            // Fetch and return the newly created product with all details
            return self::findById($productId, $conn);

        } catch (Exception $e) {
            $conn->rollback();
            // Log error: error_log($e->getMessage());
            error_log($e->getMessage());
            throw $e; // Re-throw to be caught by GraphQL handler
        }
    }

    public static function update($id, $data, $conn)
    {
        $result = $conn->prepare("UPDATE products SET name = ?, in_stock = ?, description = ?, category_id = ?, brand = ? WHERE id = ?");
        $result->bind_param('sissss', 
            $data['name'], 
            $data['inStock'] ? 1 : 0, 
            $data['description'], 
            $data['category'], 
            $data['brand'], 
            $id
        );
        $result->execute();
        $result->close();
        return $result;
    }

    public static function delete($id, $conn)
    {
        $result = $conn->prepare("DELETE FROM products WHERE id = ?");
        $result->bind_param('i', $id);
        $result->execute();
        $result->close();
        return $result;
    }
}
