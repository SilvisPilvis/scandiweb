<?php

namespace App\Model;

use App\Database\Database;
use App\Model\ProductModel;

class DataLoaderModel
{
    public static function getProduct()
    {
        $filePath = __DIR__ . '/data.json';

        // Check if the file exists first.
        if (!file_exists($filePath)) {
            echo "Error: data.json not found at " . $filePath;
            return [];
        }

        $fileContent = file_get_contents($filePath);

        // Check if file_get_contents failed (e.g., permission issues, empty file)
        if ($fileContent === false) {
            echo "Error reading data.json";
            return [];
        }

        $data = json_decode($fileContent, true);

        // Check for JSON decoding errors
        if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
            echo "JSON decoding error: " . json_last_error_msg();
            return [];
        }

        // Check if the expected structure exists
        if (!isset($data['data']['products'])) {
            echo "Expected structure not found in data.json (missing data or products key)";
            return [];
        }

        // Now, actually return the products!
        return json_encode($data['data']['products']);
    }

    public static function createData()
    {
        $product = new ProductModel();
        $db = new Database();

        $products = $product->create($data);
    }
}
