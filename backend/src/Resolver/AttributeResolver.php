<?php

namespace App\Resolver;

use App\Model\AttributeModel;
use GraphQL\Type\Definition\ResolveInfo;

class AttributeResolver
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * Get all attributes
     * @return array
     */
    public function getAttributes(): array
    {
        $attributeModel = new AttributeModel($this->db);
        return $attributeModel->findAll();
    }

    /**
     * Get a specific attribute by ID
     * @param array $args Arguments containing 'id'
     * @return array
     * @throws \Exception
     */
    public function getAttribute(array $args): array
    {
        if (empty($args['id'])) {
            throw new \InvalidArgumentException('Attribute ID is required');
        }

        $attributeModel = new AttributeModel($this->db);
        $attribute = $attributeModel->findById($args['id']);
        
        if (!$attribute) {
            throw new \Exception('Attribute not found');
        }
        
        return $attribute;
    }

    /**
     * Create a new attribute
     * @param array $args Arguments containing 'attribute' data
     * @return array
     * @throws \Exception
     */
    public function createAttribute(array $args): array
    {
        if (empty($args['attribute']['displayValue']) || empty($args['attribute']['value'])) {
            throw new \InvalidArgumentException('Display value and value are required');
        }

        $attributeModel = new AttributeModel($this->db);
        return $attributeModel->create($args['attribute']);
    }

    /**
     * Update an existing attribute
     * @param array $args Arguments containing 'id' and 'attribute' data
     * @return array
     * @throws \Exception
     */
    public function updateAttribute(array $args): array
    {
        if (empty($args['id'])) {
            throw new \InvalidArgumentException('Attribute ID is required');
        }
        
        if (empty($args['attribute']['displayValue']) || empty($args['attribute']['value'])) {
            throw new \InvalidArgumentException('Display value and value are required');
        }

        $attributeModel = new AttributeModel($this->db);
        $result = $attributeModel->update($args['id'], $args['attribute']);
        
        if (!$result) {
            throw new \Exception('Failed to update attribute');
        }
        
        // Return the updated attribute
        return $attributeModel->findById($args['id']);
    }

    /**
     * Delete an attribute
     * @param array $args Arguments containing 'id'
     * @return bool
     * @throws \Exception
     */
    public function deleteAttribute(array $args): bool
    {
        if (empty($args['id'])) {
            throw new \InvalidArgumentException('Attribute ID is required');
        }

        $attributeModel = new AttributeModel($this->db);
        $result = $attributeModel->delete($args['id']);
        
        if (!$result) {
            throw new \Exception('Failed to delete attribute');
        }
        
        return true;
    }
}