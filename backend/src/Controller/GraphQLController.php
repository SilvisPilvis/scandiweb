<?php

/**
 * Controller
 * php version  8.2
 *
 * @category    GraphQL
 * @description A Class for GraphQL database connections
 * @package     App\Controller
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
use GraphQL\Type\Schema;
use GraphQL\Type\SchemaConfig;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Error\InvariantViolation;
use RuntimeException;
use Throwable;
use App\GraphQL\Type\ProductType;
use App\GraphQL\Type\CategoryType;
use App\GraphQL\Type\AttributeSetType;
use App\GraphQL\Type\AttributeType;
use App\GraphQL\Type\PriceType;
use App\GraphQL\Type\OrderType;

/**
 * Class GraphQL
 *
 * @category Database
 * @package  App\Controller
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

            // $categoryController = new \App\Controller\CategoryController($this->_db);
            $queryType = new ObjectType(
                [
                'name' => 'Query',
                'fields' => [
                    'getPrices' => [
                        'type' => Type::listOf(Type::nonNull(PriceType::getType())),
                        'resolve' => static function ($root, $args, $ctx) {
                            $resolver = new \App\Resolver\PriceResolver($ctx['db']);
                            return $resolver->findAll();
                        }
                    ],
                    'getPrice' => [
                        'type' => PriceType::getType(),
                        'args' => ['id' => Type::nonNull(Type::string())],
                        'resolve' => static function ($root, $args, $ctx) {
                            $resolver = new \App\Resolver\PriceResolver($ctx['db']);
                            return $resolver->findById($args);
                        }
                    ],
                    'getPricesByProduct' => [
                        'type' => Type::listOf(Type::nonNull(PriceType::getType())),
                        'args' => ['id' => Type::nonNull(Type::string())],
                        'resolve' => static function ($root, $args, $ctx) {
                            $resolver = new \App\Resolver\PriceResolver($ctx['db']);
                            return $resolver->findByProductId($args);
                        }
                    ],
                    'getProducts' => [
                        'type' => Type::listOf(Type::nonNull(ProductType::getType())),
                        'resolve' => static function ($root, $args, $ctx) {
                            $resolver = new \App\Resolver\ProductResolver($ctx['db']);
                            return $resolver->findAll();
                        }
                    ],
                    'getProduct' => [
                        'type' => ProductType::getType(),
                        'args' => ['id' => Type::nonNull(Type::string())],
                        'resolve' => static function ($root, $args, $ctx) {
                            $resolver = new \App\Resolver\ProductResolver($ctx['db']);
                            return $resolver->findById($args);
                        }
                    ],
                    'getProductsByCategory' => [
                        'type' => Type::listOf(Type::nonNull(ProductType::getType())),
                        'args' => ['category' => Type::nonNull(Type::string())],
                        'resolve' => static function ($root, $args, $ctx) {
                            $resolver = new \App\Resolver\ProductResolver($ctx['db']);
                            return $resolver->findByCategory($args);
                        }
                    ],
                    'getCategories' => [
                        'type' => Type::listOf(Type::nonNull(CategoryType::getType())),
                        'resolve' => static function ($root, $args, $ctx) {
                            $resolver = new \App\Resolver\CategoryResolver($ctx['db']);
                            return $resolver->findAll();
                        }
                    ],
                    'getCategory' => [
                        'type' => CategoryType::getType(),
                        'args' => ['id' => Type::nonNull(Type::string())],
                        'resolve' => static function ($root, $args, $ctx) {
                            $resolver = new \App\Resolver\CategoryResolver($ctx['db']);
                            return $resolver->findById($args);
                        }
                    ],
                    'getAttributes' => [
                        'type' => Type::listOf(Type::nonNull(AttributeType::getType())),
                        'resolve' => static function ($rootValue, array $args, $ctx, ResolveInfo $info) {
                            $resolver = new \App\Resolver\AttributeResolver($ctx['db']);
                            return $resolver->getAttributes();
                        }
                    ],
                    'getAttribute' => [
                        'type' => AttributeType::getType(),
                        'args' => [
                            'id' => Type::nonNull(Type::string()),
                        ],
                        'resolve' => static function ($rootValue, array $args, $ctx, ResolveInfo $info) {
                            $resolver = new \App\Resolver\AttributeResolver($ctx['db']);
                            return $resolver->getAttribute($args);
                        }
                    ],
                    'getAttributeSets' => [
                        'type' => Type::listOf(Type::nonNull(AttributeSetType::getType())),
                        'resolve' => static function ($rootValue, array $args, $ctx, ResolveInfo $info) {
                            $resolver = new \App\Resolver\AttributeSetResolver($ctx['db']);
                            return $resolver->findAll();
                        }
                    ],
                    'getAttributeSet' => [
                        'type' => AttributeSetType::getType(),
                        'args' => [
                            'id' => Type::nonNull(Type::string()),
                        ],
                        'resolve' => static function ($rootValue, array $args, $ctx, ResolveInfo $info) {
                            $resolver = new \App\Resolver\AttributeSetResolver($ctx['db']);
                            return $resolver->findById($args);
                        }
                    ],
                    'getAttributeSetItems' => [
                        'type' => Type::listOf(Type::nonNull(AttributeType::getType())),
                        'args' => [
                            'id' => Type::nonNull(Type::string()),
                        ],
                        'resolve' => static function ($rootValue, array $args, $ctx, ResolveInfo $info) {
                            $resolver = new \App\Resolver\AttributeSetResolver($ctx['db']);
                            return $resolver->findItemsBySetId($args);
                        }
                    ],
                    'getPrices' => [
                        'type' => Type::listOf(Type::nonNull(PriceType::getType())),
                        'resolve' => static function ($rootValue, array $args, $ctx, ResolveInfo $info) {
                            $resolver = new \App\Resolver\PriceResolver($ctx['db']);
                            return $resolver->findAll();
                        }
                    ],
                    'getPrice' => [
                        'type' => PriceType::getType(),
                        'args' => [
                            'id' => Type::nonNull(Type::string()),
                        ],
                        'resolve' => static function ($rootValue, array $args, $ctx, ResolveInfo $info) {
                            $resolver = new \App\Resolver\PriceResolver($ctx['db']);
                            return $resolver->findById($args);
                        }
                    ]
                ],
                ]
            );

            $mutationType = new ObjectType(
                [
                'name' => 'Mutation',
                'fields' => [
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
                            $resolver = new \App\Resolver\ProductResolver($ctx['db']);
                            return $resolver->create($productData);
                        }
                    ],
                    'createOrder' => [
                        'type' => OrderType::getType(),
                        'args' => [
                            'items' => [
                                'type' => Type::nonNull(OrderType::getInputType()),
                                'description' => 'The array of itms in the order',
                            ],
                        ],
                        'resolve' => static function ($root, array $args, $ctx, ResolveInfo $info) {
                            $productData = $args['items'];
                            $resolver = new \App\Resolver\OrderResolver($ctx['db']);
                            return $resolver->createOrder($productData);
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
                            $resolver = new \App\Resolver\CategoryResolver($ctx['db']);
                            return $resolver->create($categoryData);
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
                            $resolver = new \App\Resolver\AttributeSetResolver($ctx['db']);
                            return $resolver->createAttributeSet($attributeSetData);
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
                            $resolver = new \App\Resolver\AttributeResolver($ctx['db']);
                            return $resolver->createAttribute($attributeData);
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
