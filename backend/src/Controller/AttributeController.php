<?php

namespace App\Controller;

use App\Model\AttributeModel;

class AttributeController extends Controller
{
    private $attributeModel;
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
        $this->attributeModel = new \App\Model\AttributeModel($conn);
    }

    public function findAll()
    {
        return $this->attributeModel->findAll();
    }

    public function findById($id)
    {
        return $this->attributeModel->findById($id);
    }

    public function create($data)
    {
        return $this->attributeModel->create($data);
    }

    public function update($id, $data)
    {
        return $this->attributeModel->update($id, $data);
    }

    public function delete($id)
    {
        return $this->attributeModel->delete($id);
    }
}
