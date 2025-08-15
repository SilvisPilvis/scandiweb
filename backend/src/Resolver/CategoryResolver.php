<?php

namespace App\Resolver;

use App\Model\CategoryModel;

class CategoryResolver
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * Get all categories
     * @return array
     */
    public function findAll(): array
    {
        $model = new CategoryModel($this->db);
        $categories = $model->findAll();
        return array_map(fn($cat) => $cat->toArray(), $categories);
    }

    /**
     * Get a specific category by ID
     * @param array $args Arguments containing 'id'
     * @return array
     * @throws \Exception
     */
    public function findById(array $args): array
    {
        if (empty($args['id'])) {
            throw new \InvalidArgumentException('Category ID is required');
        }

        $model = new CategoryModel($this->db);
        $category = $model->findById($args['id']);
        
        if (!$category) {
            throw new \Exception('Category not found');
        }
        
        return $category->toArray();
    }

    /**
     * Create a new category
     * @param array $args Arguments containing category data
     * @return array
     * @throws \Exception
     */
    public function create(array $args): array
    {
        if (empty($args['input'])) {
            throw new \InvalidArgumentException('Category data is required');
        }

        $model = new CategoryModel($this->db);
        $result = $model->create($args['input']);
        return $result->toArray();
    }

    /**
     * Update an existing category
     * @param array $args Arguments containing 'id' and update data
     * @return array
     * @throws \Exception
     */
    public function update(array $args): array
    {
        if (empty($args['id'])) {
            throw new \InvalidArgumentException('Category ID is required');
        }
        
        if (empty($args['input'])) {
            throw new \InvalidArgumentException('Update data is required');
        }

        $model = new CategoryModel($this->db);
        $result = $model->update($args['id'], $args['input']);
        
        if (!$result) {
            throw new \Exception('Failed to update category');
        }
        
        return $model->findById($args['id'])->toArray();
    }

    /**
     * Delete a category
     * @param array $args Arguments containing 'id'
     * @return bool
     * @throws \Exception
     */
    public function delete(array $args): bool
    {
        if (empty($args['id'])) {
            throw new \InvalidArgumentException('Category ID is required');
        }

        $model = new CategoryModel($this->db);
        $result = $model->delete($args['id']);
        
        if (!$result) {
            throw new \Exception('Failed to delete category');
        }
        
        return true;
    }
}