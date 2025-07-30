<?php

namespace App\Controller;

use App\Model\CategoryModel;

class CategoryController extends Controller
{

    private $categoryModel;

    public function __construct($conn, $id = null, $name = null)
    {
        $this->categoryModel = new \App\Model\CategoryModel($conn);
    }

    public function findAll()
    {
        $categories = $this->categoryModel->findAll();
        return array_map(function($cat) {
            return $cat->toArray();
        }, $categories);
    }

    public function findById($id)
    {
        return $this->categoryModel->findById($id);
    }

    public function create($data)
    {
        return $this->categoryModel->create($data);
    }

    public function update($id, $data)
    {
        return $this->categoryModel->update($id, $data);
    }

    public function delete($id)
    {
        return $this->categoryModel->delete($id);
    }
}
