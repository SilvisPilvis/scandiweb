<?php

namespace App\Resolver;

use App\Model\PriceModel;

class PriceResolver
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * Get all prices
     * @return array
     */
    public function findAll(): array
    {
        $model = new PriceModel($this->db);
        return $model->findAll();
    }

    /**
     * Get a specific price by ID
     * @param array $args Arguments containing 'id'
     * @return array
     * @throws \Exception
     */
    public function findById(array $args): array
    {
        if (empty($args['id'])) {
            throw new \InvalidArgumentException('Price ID is required');
        }

        $model = new PriceModel($this->db);
        $price = $model->findById($args['id']);
        
        if (!$price) {
            throw new \Exception('Price not found');
        }
        
        return $price;
    }

    /**
     * Get prices by product ID
     * @param array $args Arguments containing 'id'
     * @return array
     * @throws \Exception
     */
    public function findByProductId(array $args): array
    {
        if (empty($args['id'])) {
            throw new \InvalidArgumentException('Product ID is required');
        }

        $model = new PriceModel($this->db);
        return $model->findByProductId($args['id']);
    }

    /**
     * Create a new price record
     * @param array $args Arguments containing price data
     * @return array
     * @throws \Exception
     */
    public function create(array $args): array
    {
        if (empty($args['input'])) {
            throw new \InvalidArgumentException('Price data is required');
        }

        $model = new PriceModel($this->db);
        return $model->create($args['input']);
    }

    /**
     * Update a price record
     * @param array $args Arguments containing 'id' and update data
     * @return array
     * @throws \Exception
     */
    public function update(array $args): array
    {
        if (empty($args['id'])) {
            throw new \InvalidArgumentException('Price ID is required');
        }
        
        if (empty($args['input'])) {
            throw new \InvalidArgumentException('Update data is required');
        }

        $model = new PriceModel($this->db);
        $result = $model->update($args['id'], $args['input']);
        
        if (!$result) {
            throw new \Exception('Failed to update price');
        }
        
        return $model->findById($args['id']);
    }

    /**
     * Delete a price record
     * @param array $args Arguments containing 'id'
     * @return bool
     * @throws \Exception
     */
    public function delete(array $args): bool
    {
        if (empty($args['id'])) {
            throw new \InvalidArgumentException('Price ID is required');
        }

        $model = new PriceModel($this->db);
        $result = $model->delete($args['id']);
        
        if (!$result) {
            throw new \Exception('Failed to delete price');
        }
        
        return true;
    }
}