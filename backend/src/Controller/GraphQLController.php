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
use App\GraphQL\Type\ProductType;
use App\GraphQL\Type\CategoryType;
use App\GraphQL\Type\AttributeSetType;
use App\GraphQL\Type\AttributeType;
use App\GraphQL\Type\PriceType;

/**
 * Class GraphQL
 *
 * @category Database
 * @package  App\Model
 * @author   Silvestrs Lignickis <silvestrsl47@gmail.com>
 * @license  https://www.apache.org/licenses/LICENSE-2.0 Apache License 2.0
 * @link     None
 */

class GraphQLController
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
                        'type' => Type::listOf(Type::nonNull(CategoryType::getType())),
                        'resolve' => static function ($rootValue, array $args, $ctx, ResolveInfo $info) {
                            $contextDb = $ctx['db'];
                            $result = CategoryModel::findAll($contextDb);
                            return $result;
                        }
                    ],
                    'getCategory' => [
                        'type' => CategoryType::getType(),
                        'args' => [
                            'id' => Type::nonNull(Type::string()),
                        ],
                        'resolve' => static function ($rootValue, array $args, $ctx, ResolveInfo $info) {
                            $contextDb = $ctx['db'];
                            $result = CategoryModel::findById($args['id'], $contextDb);
                            return $result;
                        }
                    ],
                    'getProducts' => [
                        'type' => Type::listOf(Type::nonNull(ProductType::getType())),
                        'resolve' => static function ($rootValue, array $args, $ctx, ResolveInfo $info) {
                            $contextDb = $ctx['db'];
                            $result = ProductModel::findAll($contextDb);
                            return $result;
                        }
                    ],
                    'getProduct' => [
                        'type' => ProductType::getType(),
                        'args' => [
                            'id' => Type::nonNull(Type::string()),
                        ],
                        'resolve' => static function ($rootValue, array $args, $ctx, ResolveInfo $info) {
                            $contextDb = $ctx['db'];
                            $result = ProductModel::findById($args['id'], $contextDb);
                            return $result;
                        }
                    ],
                    'getAttributes' => [
                        'type' => Type::listOf(Type::nonNull(AttributeType::getType())),
                        'resolve' => static function ($rootValue, array $args, $ctx, ResolveInfo $info) {
                            $contextDb = $ctx['db'];
                            $result = AttributeModel::findAll($contextDb);
                            return $result;
                        }
                    ],
                    'getAttribute' => [
                        'type' => AttributeType::getType(),
                        'args' => [
                            'id' => Type::nonNull(Type::string()),
                        ],
                        'resolve' => static function ($rootValue, array $args, $ctx, ResolveInfo $info) {
                            $contextDb = $ctx['db'];
                            $result = AttributeModel::findById($args['id'], $contextDb);
                            return $result;
                        }
                    ],
                    'getAttributeSets' => [
                        'type' => Type::listOf(Type::nonNull(AttributeSetType::getType())),
                        'resolve' => static function ($rootValue, array $args, $ctx, ResolveInfo $info) {
                            $contextDb = $ctx['db'];
                            $result = AttributeSetModel::findAll($contextDb);
                            return $result;
                        }
                    ],
                    'getAttributeSet' => [
                        'type' => AttributeSetType::getType(),
                        'args' => [
                            'id' => Type::nonNull(Type::string()),
                        ],
                        'resolve' => static function ($rootValue, array $args, $ctx, ResolveInfo $info) {
                            $contextDb = $ctx['db'];
                            $result = AttributeSetModel::findById($args['id'], $contextDb);
                            return $result;
                        }
                    ],
                    'getAttributeSetItems' => [
                        'type' => Type::listOf(Type::nonNull(AttributeType::getType())),
                        'args' => [
                            'id' => Type::nonNull(Type::string()),
                        ],
                        'resolve' => static function ($rootValue, array $args, $ctx, ResolveInfo $info) {
                            $contextDb = $ctx['db'];
                            $result = AttributeSetModel::findItemsBySetId($args['id'], $contextDb);
                            return $result;
                        }
                    ],
                    'getPrices' => [
                        'type' => Type::listOf(Type::nonNull(PriceType::getType())),
                        'resolve' => static function ($rootValue, array $args, $ctx, ResolveInfo $info) {
                            $contextDb = $ctx['db'];
                            $result = PriceModel::findAll($contextDb);
                            return $result;
                        }
                    ],
                    'getPrice' => [
                        'type' => PriceType::getType(),
                        'args' => [
                            'id' => Type::nonNull(Type::string()),
                        ],
                        'resolve' => static function ($rootValue, array $args, $ctx, ResolveInfo $info) {
                            $contextDb = $ctx['db'];
                            $result = PriceModel::findById($args['id'], $contextDb);
                            return $result;
                        }
                    ]
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
                        'type' => ProductType::getType(),
                        'args' => [
                            'product' => [
                                'type' => Type::nonNull(ProductType::getInputType()),
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
                        'type' => CategoryType::getType(),
                        'args' => [
                            'category' => [
                                'type' => Type::nonNull(CategoryType::getInputType()),
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
                        'type' => AttributeSetType::getType(),
                        'args' => [
                            'attributeSet' => [
                                'type' => Type::nonNull(AttributeSetType::getInputType()),
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
                        'type' => AttributeType::getType(),
                        'args' => [
                            'attribute' => [
                                'type' => Type::nonNull(AttributeType::getInputType()),
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
