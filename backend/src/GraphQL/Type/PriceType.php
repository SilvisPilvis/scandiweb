<?php

namespace App\GraphQL\Type;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\InputObjectType;

class PriceType
{
    private static ?ObjectType $type = null;
    private static ?InputObjectType $inputType = null;

    public static function getType(): ObjectType
    {
        if (self::$type === null) {
            self::$type = new ObjectType([
                'name' => 'Price',
                'fields' => [
                    'amount' => Type::nonNull(Type::float()),
                    'currency' => Type::nonNull(CurrencyType::getType()),
                ]
            ]);
        }
        return self::$type;
    }

    public static function getInputType(): InputObjectType
    {
        if (self::$inputType === null) {
            self::$inputType = new InputObjectType([
                'name' => 'PriceInput',
                'fields' => [
                    'amount' => Type::nonNull(Type::float()),
                    'currency' => Type::nonNull(CurrencyType::getInputType()),
                ]
            ]);
        }
        return self::$inputType;
    }
} 