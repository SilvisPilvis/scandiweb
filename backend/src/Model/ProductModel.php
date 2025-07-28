<?php

namespace App\Model;

class ProductModel
{
    // Instance properties (optional if you mainly use static methods returning arrays)
    public $id;
    public $name;
    public $in_stock;
    public $gallery; // Array of strings (URLs)
    public $description;
    public $category; // Category object/array
    public $category_id;
    public $attributes; // Array of AttributeSet objects/arrays
    public $prices; // Array of Price objects/arrays
    public $brand;
    private $conn;

    // Corrected Constructor (if you plan to instantiate ProductModel objects)
    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function findAll()
    {
        $products = [];
        $rows = $this->conn->query('SELECT id FROM products');
        if ($rows === false) {
            $error = $this->conn->error;
            error_log("Database error in ProductModel::findAll: " . $error);
            throw new \RuntimeException("Database error fetching product IDs: " . $error);
        }
        if (!is_array($rows)) {
            error_log("Unexpected return type from DB query in ProductModel::findAll");
            throw new \RuntimeException("Unexpected data format from database query.");
        }
        foreach ($rows as $row) {
            if (isset($row['id'])) {
                $product = $this->findById($row['id']);
                if ($product) {
                    $products[] = $product;
                }
            }
        }
        return $products;
    }

    public function findById($id)
    {
        $product = new self($this->conn);

        $stmt = $this->conn->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$id]);

        // 1. Fetch product data
        $stmt->setFetchMode(\PDO::FETCH_CLASS, ProductModel::class, [$this->conn]);
        $product = $stmt->fetch();
        if (!$product) return null;

        // error_log(json_encode($product));

        // 2. Fetch Category data using CategoryModel findById
        $categoryModel = new \App\Model\CategoryModel($this->conn);
        $categoryData = $categoryModel->findById($product->category_id);
        if (!empty($categoryData)){
            $product->category = $categoryData;
        } else {
            // Should handle this as error
            $product->category = null;
        }

        // 3. Fetch gallery images
        $galleryStmt = $this->conn->prepare("SELECT image_url FROM product_gallery_images WHERE product_id = ?");
        $galleryStmt->execute([$id]);
        $galleryData = $galleryStmt->fetchAll(\PDO::FETCH_ASSOC);
        if ($galleryData) {
            $galleryData = array_column($galleryData, 'image_url');
        }
        $product->gallery = $galleryData;

        // 4. Fetch prices
        $priceModel = new \App\Model\PriceModel($this->conn);
        $priceData = $priceModel->findByProductId($id);
        if ($priceData) {
            $product->prices = $priceData;
        } else {
            $product->prices = null;
        }

        // 5. Fetch attributes
        $attributeSets = [];
        $atributeSetsStmt = $this->conn->prepare(
            "SELECT aset.id, aset.name, aset.type
             FROM attribute_sets aset
             JOIN product_attribute_sets pas ON aset.id = pas.attribute_set_id
             WHERE pas.product_id = ?"
        );
        $atributeSetsStmt->execute([$id]);
        while ($asRow = $atributeSetsStmt->fetch(\PDO::FETCH_ASSOC)) {
            $currentSet = $asRow;
            $attributeSetModel = new \App\Model\AttributeSetModel($this->conn);
            $currentSet['items'] = $attributeSetModel->findItemsBySetId($currentSet['id']);
            array_push($attributeSets, $currentSet);
        }
        // $atributeSetsData = $atributeSetsStmt->fetchAll(\PDO::FETCH_ASSOC);
        // $product->attributes = $atributeSetsData;
        $product->attributes = $attributeSets;

        // Make sure inStock is a boolean
        $product->in_stock = (bool)$product->in_stock;

        // error_log(json_encode($product));

        return $product;
    }

    public function create($data)
    {
        $stmt = $this->conn->prepare("INSERT INTO products (id, name, in_stock, description, category_id, brand) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$data['id'], $data['name'], $data['in_stock'], $data['description'], $data['category_id'], $data['brand']]);
        $insert_id = $this->conn->lastInsertId();
        if ($insert_id) {
            return $this->findById($insert_id);
        }
        return null;
    }

    public function update($id, $data)
    {
        $stmt = $this->conn->prepare("UPDATE products SET name = ?, in_stock = ?, description = ?, category_id = ?, brand = ? WHERE id = ?");
        $stmt->execute([$data['name'], $data['in_stock'], $data['description'], $data['category_id'], $data['brand'], $id]);
        return $stmt;
    }
    
    public function delete($id)
    {
        $stmt = $this->conn->prepare("DELETE FROM products WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt;
    }
} 