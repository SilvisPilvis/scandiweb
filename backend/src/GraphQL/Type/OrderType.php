<?php

namespace App\GraphQL\Type;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\InputObjectType;

class OrderType
{
    private static ?ObjectType $type = null;
    private static ?InputObjectType $inputType = null;

    public static function getType(): ObjectType
    {
        if (self::$type === null) {
            self::$type = new ObjectType([
                'name' => 'Order',
                'fields' => [
                    'id' => Type::nonNull(Type::string()),
                    'items' => Type::nonNull(Type::string()),
                ]
            ]);
        }
        return self::$type;
    }

    public static function getInputType(): InputObjectType
    {
        if (self::$inputType === null) {
            self::$inputType = new InputObjectType([
                'name' => 'OrderInput',
                'fields' => [
                    'items' => Type::nonNull(Type::string()),
                ]
            ]);
        }
        return self::$inputType;
    }
} 