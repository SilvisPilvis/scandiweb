<?php

namespace App\Resolver;

use App\Model\ProductModel;
use Exception;
use PDO;

class ProductResolver
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * Get all products
     * @return array
     */
    public function findAll(): array
    {
        $model = new ProductModel($this->db);
        return $model->findAll();
    }

    /**
     * Get a specific product by ID
     * @param array $args Arguments containing 'id'
     * @return array
     * @throws Exception
     */
    public function findById(array $args): ?ProductModel
    {
        if (empty($args['id'])) {
            throw new \InvalidArgumentException('Product ID is required');
        }

        $model = new ProductModel($this->db);
        $product = $model->findById($args['id']);
        
        if (!$product) {
            throw new Exception('Product not found');
        }
        
        return $product;
    }

    /**
     * Get products by category name
     * @param array $args Arguments containing 'category'
     * @return array
     * @throws Exception
     */
    public function findByCategory(array $args): array
    {
        if (empty($args['category'])) {
            throw new \InvalidArgumentException('Category name is required');
        }

        $product = new ProductModel($this->db);
        $products = $product->findByCategory($args);

        return $products;
    }

    /**
     * Create a new product
     * @param array $args Arguments containing product data
     * @return array
     * @throws Exception
     */
    public function create(array $args): ?ProductModel
    {
        if (empty($args['input'])) {
            throw new \InvalidArgumentException('Product data is required');
        }

        $model = new ProductModel($this->db);
        return $model->create($args['input']);
    }

    /**
     * Update a product
     * @param array $args Arguments containing 'id' and update data
     * @return array
     * @throws Exception
     */
    public function update(array $args): ?ProductModel
    {
        if (empty($args['id'])) {
            throw new \InvalidArgumentException('Product ID is required');
        }
        
        if (empty($args['input'])) {
            throw new \InvalidArgumentException('Update data is required');
        }

        $model = new ProductModel($this->db);
        $result = $model->update($args['id'], $args['input']);
        
        if (!$result) {
            throw new Exception('Failed to update product');
        }
        
        return $model->findById($args['id']);
    }

    /**
     * Delete a product
     * @param array $args Arguments containing 'id'
     * @return bool
     * @throws Exception
     */
    public function delete(array $args): bool
    {
        if (empty($args['id'])) {
            throw new \InvalidArgumentException('Product ID is required');
        }

        $model = new ProductModel($this->db);
        $result = $model->delete($args['id']);
        
        if (!$result) {
            throw new Exception('Failed to delete product');
        }
        
        return true;
    }
}