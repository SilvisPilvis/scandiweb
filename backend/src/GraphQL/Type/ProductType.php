<?php

namespace App\GraphQL\Type;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\InputObjectType;

class ProductType
{
    private static ?ObjectType $type = null;
    private static ?InputObjectType $inputType = null;

    public static function getType(): ObjectType
    {
        if (self::$type === null) {
            self::$type = new ObjectType([
                'name' => 'Product',
                'fields' => [
                    'id' => Type::nonNull(Type::string()),
                    'name' => Type::nonNull(Type::string()),
                    'inStock' => Type::nonNull(Type::boolean()),
                    'gallery' => Type::nonNull(Type::listOf(Type::nonNull(Type::string()))),
                    'description' => Type::nonNull(Type::string()),
                    'category' => Type::nonNull(CategoryType::getType()),
                    'attributes' => Type::nonNull(Type::listOf(Type::nonNull(AttributeSetType::getType()))),
                    'prices' => Type::nonNull(Type::listOf(Type::nonNull(PriceType::getType()))),
                    'brand' => Type::nonNull(Type::string()),
                ]
            ]);
        }
        return self::$type;
    }

    public static function getInputType(): InputObjectType
    {
        if (self::$inputType === null) {
            self::$inputType = new InputObjectType([
                'name' => 'ProductInput',
                'fields' => [
                    'id' => Type::string(),
                    'name' => Type::nonNull(Type::string()),
                    'inStock' => Type::nonNull(Type::boolean()),
                    'gallery' => Type::nonNull(Type::listOf(Type::nonNull(Type::string()))),
                    'description' => Type::nonNull(Type::string()),
                    'category' => Type::nonNull(Type::listOf(Type::nonNull(CategoryType::getInputType()))),
                    'attributes' => Type::nonNull(Type::listOf(Type::nonNull(AttributeSetType::getInputType()))),
                    'prices' => Type::nonNull(Type::listOf(Type::nonNull(PriceType::getInputType()))),
                    'brand' => Type::nonNull(Type::string()),
                ]
            ]);
        }
        return self::$inputType;
    }
} 