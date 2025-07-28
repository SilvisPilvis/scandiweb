<?php

namespace App\Controller;

use App\Model\AttributeSetModel;

class AttributeSetController extends Controller
{
    private $attributeSetModel;
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
        $this->attributeSetModel = new \App\Model\AttributeSetModel($conn);
    }

    public function findAll()
    {
        return $this->attributeSetModel->findAll();
    }

    public function findById($id)
    {
        return $this->attributeSetModel->findById($id);
    }

    public function findItemsBySetId($id)
    {
        return $this->attributeSetModel->findItemsBySetId($id);
    }

    public function create($data)
    {
        return $this->attributeSetModel->create($data);
    }

    public function update($id, $data)
    {
        return $this->attributeSetModel->update($id, $data);
    }

    public function delete($id)
    {
        return $this->attributeSetModel->delete($id);
    }
}
