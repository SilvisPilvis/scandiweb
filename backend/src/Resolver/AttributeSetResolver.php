<?php

namespace App\Resolver;
use App\Model\AttributeSetModel;

class AttributeSetResolver
{
    private $attributeSetModel;

    public function __construct($conn)
    {
        $this->attributeSetModel = new AttributeSetModel($conn);
    }

    // Query Resolvers
    public function findAll(): array
    {
        return $this->attributeSetModel->findAll();
    }

    public function findById(array $args): ?array
    {
        return $this->attributeSetModel->findById($args['id']);
    }

    public function findItemsBySetId(array $args): array
    {
        return $this->attributeSetModel->findItemsBySetId($args['id']);
    }

    // Mutation Resolvers
    public function createAttributeSet(array $args): array
    {
        return $this->attributeSetModel->create($args['input']);
    }

    public function updateAttributeSet(array $args): array
    {
        return $this->attributeSetModel->update($args['id'], $args['input']);
    }

    public function deleteAttributeSet(array $args): array
    {
        return $this->attributeSetModel->delete($args['id']);
    }
}