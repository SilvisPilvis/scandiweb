<?php

namespace App\GraphQL\Type;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\InputObjectType;

class CurrencyType
{
    private static ?ObjectType $type = null;
    private static ?InputObjectType $inputType = null;

    public static function getType(): ObjectType
    {
        if (self::$type === null) {
            self::$type = new ObjectType([
                'name' => 'Currency',
                'fields' => [
                    'label' => Type::nonNull(Type::string()),
                    'symbol' => Type::nonNull(Type::string()),
                ]
            ]);
        }
        return self::$type;
    }

    public static function getInputType(): InputObjectType
    {
        if (self::$inputType === null) {
            self::$inputType = new InputObjectType([
                'name' => 'CurrencyInput',
                'fields' => [
                    'label' => Type::nonNull(Type::string()),
                    'symbol' => Type::nonNull(Type::string()),
                ]
            ]);
        }
        return self::$inputType;
    }
} 