<?php

namespace App\Resolver;

use App\Model\ProductModel;
use GraphQL\Type\Definition\ResolveInfo;

class ProductsResolver
{
    // public function __invoke($root, array $args, $ctx, ResolveInfo $info)
    // {
    //
    // }

    public function resolveProductById($root, array $args, $ctx, ResolveInfo $info)
    {
        $productId = $args['id'];
        return ProductModel::findById($productId);
    }

    public function resolveProducts()
    {
        return ProductModel::findAll();
    }

    public function resolveProductById($id)
    {

        $product = ProductModel::findById($id);
        return $product;
    }
}
