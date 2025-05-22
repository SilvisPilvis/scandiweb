<?php

/**
 * Model
 * php version  8.2
 *
 * @category    GraphQL
 * @description A Class for GraphQL database connections
 * @package     App\Model
 * @author      Silvestrs Lignickis <silvestrsl47@gmail.com>
 * @license     https://www.apache.org/licenses/LICENSE-2.0 Apache License 2.0
 * @version     GIT: main
 * @link        None
 */

namespace App\Controller;

use GraphQL\Error\DebugFlag;
use GraphQL\GraphQL as GraphQLBase;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Schema;
use GraphQL\Type\SchemaConfig;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Error\InvariantViolation;
use RuntimeException;
use Throwable;
use App\Model\ProductModel;
use App\Model\CategoryModel;
use App\Model\AttributeSetModel;
use App\Model\AttributeModel;
use App\Model\PriceModel;

/**
 * Class GraphQL
 *
 * @category Database
 * @package  App\Model
 * @author   Silvestrs Lignickis <silvestrsl47@gmail.com>
 * @license  https://www.apache.org/licenses/LICENSE-2.0 Apache License 2.0
 * @link     None
 */

class GraphQL
{
    private \App\Database\Database $_db;


    public function __construct(\App\Database\Database $db)
    {
        $this->_db = $db;
    }

    /**
     * Handle GraphQL requests
     *
     * @return string
     */
    public function handle(): string
    // public static function handle(): string
    {
        try {
            $contextValue = ['db' => $this->_db];

            $currencyType = new ObjectType(
                [
                    'name' => 'Currency',
                    'fields' => [
                        'label' => Type::nonNull(Type::string()),
                        'symbol' => Type::nonNull(Type::string()),
                    ]
                ]
            );

            $currencyInputType = new InputObjectType(
                [
                    'name' => 'CurrencyInput',
                    'fields' => [
                        'label' => Type::nonNull(Type::string()),
                        'symbol' => Type::nonNull(Type::string()),
                    ],
                ]
            );

            $priceType = new ObjectType(
                [
                    'name' => 'Price',
                    'fields' => [
                        'amount' => Type::nonNull(Type::float()),
                        'currency' => Type::nonNull($currencyType),
                    ]
                ]
            );

            $priceInputType = new InputObjectType(
                [
                    'name' => 'PriceInput',
                    'fields' => [
                        'amount' => Type::nonNull(Type::float()),
                        'currency' => Type::nonNull($currencyInputType),
                    ]
                ]
            );

            $attributeType = new ObjectType(
                [
                    'name' => 'AttributeItem',
                    'fields' => [
                        'displayValue' => Type::nonNull(Type::string()),
                        'value' => Type::nonNull(Type::string()),
                        'id' => Type::nonNull(Type::string()),
                    ]
                ]
            );

            $attributeInputType = new InputObjectType(
                [
                    'name' => 'AttributeInput',
                    'fields' => [
                        'id' => Type::nonNull(Type::string()),
                        'displayValue' => Type::nonNull(Type::string()),
                        'value' => Type::nonNull(Type::string()),
                    ]
                ]
            );

            $attributeSetType = new ObjectType(
                [
                    'name' => 'AttributeSet',
                    'fields' => [
                        'id' => Type::nonNull(Type::string()),
                        'items' => Type::nonNull(Type::listOf(Type::nonNull($attributeType))),
                        'name' => Type::nonNull(Type::string()),
                        'type' => Type::nonNull(Type::string()), // Assuming 'swatch' or 'text'
                    ]
                ]
            );

            $attributeSetInputType = new InputObjectType(
                [
                    'name' => 'AttributeSetInput',
                    'fields' => [
                        'id' => Type::nonNull(Type::string()),
                        'items' => Type::nonNull(Type::listOf(Type::nonNull($attributeInputType))),
                        'name' => Type::nonNull(Type::string()),
                        'type' => Type::nonNull(Type::string()), // Assuming 'swatch' or 'text'
                    ]
                ]
            );

            $productInputType = new InputObjectType(
                [
                'name' => 'ProductInput',
                'fields' => [
                    'id' => Type::string(), // Allow null for auto-generated ID
                    'name' => Type::nonNull(Type::string()),
                    'inStock' => Type::nonNull(Type::boolean()),
                    'gallery' => Type::nonNull(Type::listOf(Type::nonNull(Type::string()))),
                    'description' => Type::nonNull(Type::string()),
                    // You'll need a way to handle category input - could be an ID or another input type
                    'category' => Type::nonNull(Type::string()), // Assuming category ID for simplicity
                    'attributes' => Type::nonNull(Type::listOf(Type::nonNull($attributeSetInputType))),
                    'prices' => Type::nonNull(Type::listOf(Type::nonNull($priceInputType))),
                    'brand' => Type::nonNull(Type::string()),
                ],
                ]
            );

            $categoryType = new ObjectType(
                [
                    'name' => 'Category',
                    'fields' => [
                        'name' => Type::nonNull(Type::string()),
                    ]
                ]
            );

            $categoryInputType = new InputObjectType(
                [
                    'name' => 'CategoryInput',
                    'fields' => [
                        'name' => Type::nonNull(Type::string()),
                    ],
                ]
            );

            $productType = new ObjectType(
                [
                            'name' => 'Product',
                            'fields' => [
                                'id' => Type::nonNull(Type::string()),
                                'name' => Type::nonNull(Type::string()),
                                'inStock' => Type::nonNull(Type::boolean()),
                                'gallery' => Type::nonNull(Type::listOf(Type::nonNull(Type::string()))),
                                'description' => Type::nonNull(Type::string()),
                                'category' => Type::nonNull($categoryType),
                                'attributes' => Type::nonNull(Type::listOf(Type::nonNull($attributeSetType))),
                                'prices' => Type::nonNull(Type::listOf(Type::nonNull($priceType))),
                                'brand' => Type::nonNull(Type::string()),
                            ],
                ]
            );

            $queryType = new ObjectType(
                [
                'name' => 'Query',
                'fields' => [
                    'echo' => [
                        'type' => Type::string(),
                        'args' => [
                            'message' => ['type' => Type::string()],
                        ],
                        'resolve' => static fn ($rootValue, array $args): string => $rootValue['prefix'] . $args['message'],
                    ],
                    'getCategories' => [
                        'type' => Type::listOf(Type::nonNull($categoryType)),
                        'resolve' => static function ($rootValue, array $args, $ctx, ResolveInfo $info) {
                            $contextDb = $ctx['db'];
                            $result = CategoryModel::findAll($contextDb);

                            return $result;
                        }

                    ],
                ],
                ]
            );

            $mutationType = new ObjectType(
                [
                'name' => 'Mutation',
                'fields' => [
                    'sum' => [
                        'type' => Type::int(),
                        'args' => [
                            'x' => ['type' => Type::int()],
                            'y' => ['type' => Type::int()],
                        ],
                        'resolve' => static fn ($calc, array $args): int => $args['x'] + $args['y'],
                    ],
                    'createProduct' => [
                        'type' => $productType,
                        'args' => [
                            'product' => [
                                'type' => Type::nonNull($productInputType),
                                'description' => 'The product to create',
                            ],
                        ],
                        'resolve' => static function ($root, array $args, $ctx, ResolveInfo $info) {
                            $productData = $args['product'];
                            $contextDb = $ctx['db'];
                            $result = ProductModel::create($productData, $contextDb);

                            return $result;
                        }
                    ],
                    'createCategory' => [
                        'type' => $categoryType,
                        'args' => [
                            'category' => [
                                'type' => Type::nonNull($categoryInputType),
                                'description' => 'The category to create',
                            ],
                        ],
                        'resolve' => static function ($root, array $args, $ctx, ResolveInfo $info) {
                            $categoryData = $args['category'];
                            $contextDb = $ctx['db'];
                            $result = CategoryModel::create($categoryData, $contextDb);

                            return $result;
                        }
                    ],
                    'createAttributeSet' => [
                        'type' => $attributeSetType,
                        'args' => [
                            'attributeSet' => [
                                'type' => Type::nonNull($attributeSetInputType),
                                'description' => 'The attribute set to create',
                            ]
                        ],
                        'resolve' => static function ($root, array $args, $ctx, ResolveInfo $info) {
                            $attributeSetData = $args['attributeSet'];
                            $contextDb = $ctx['db'];
                            $result = AttributeSetModel::create($attributeSetData, $contextDb);

                            return $result;
                        }
                    ],
                    'createAttribute' => [
                        'type' => $attributeType,
                        'args' => [
                            'attribute' => [
                                'type' => Type::nonNull($attributeInputType),
                                'description' => 'The attribute to create',
                            ]
                        ],
                        'resolve' => static function ($root, array $args, $ctx, ResolveInfo $info) {
                            $attributeData = $args['attribute'];
                            $contextDb = $ctx['db'];
                            $result = AttributeModel::create($attributeData, $contextDb);

                            return $result;
                        }
                    ],
                ],
                ]
            );

            // See docs on schema options:
            // https://webonyx.github.io/graphql-php/schema-definition/#configuration-options

            try {
                $schema = new Schema(
                    (new SchemaConfig())
                        ->setQuery($queryType)
                        ->setMutation($mutationType)
                );
                $schema->assertValid();
            } catch (InvariantViolation $e) {
                echo $e->getMessage();
            }


            $rawInput = file_get_contents('php://input');
            if ($rawInput === false) {
                throw new RuntimeException('Failed to get php://input');
            }

            $input = json_decode($rawInput, true);
            if ($input === null || count($input) === 0) {
                $output = [
                    'error' => [
                        'message' => "Expected query string",
                    ],
                ];

                header('Content-Type: application/json; charset=UTF-8');
                return json_encode($output);
            }

            $query = $input['query'];
            $variableValues = $input['variables'] ?? null;

            $rootValue = ['prefix' => 'You said: '];
            // $result = GraphQLBase::executeQuery($schema, $query, $rootValue, null, $variableValues);
            $result = GraphQLBase::executeQuery($schema, $query, $rootValue, $contextValue, $variableValues);
            // $output = $result->toArray();
            $output = $result->toArray(DebugFlag::INCLUDE_DEBUG_MESSAGE | DebugFlag::INCLUDE_TRACE);
        } catch (Throwable $e) {
            $output = [
                'error' => [
                    'message' => $e->getMessage(),
                ],
            ];
        }

        header('Content-Type: application/json; charset=UTF-8');
        return json_encode($output);
    }
}
