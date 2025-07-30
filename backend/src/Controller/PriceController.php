<?php

namespace App\Controller;

use App\Model\PriceModel;

class PriceController extends Controller
{
    private $priceModel;
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
        $this->priceModel = new \App\Model\PriceModel($conn);
    }

    public function findAll()
    {
        return $this->priceModel->findAll();
    }

    public function findById($id)
    {
        return $this->priceModel->findById($id);
    }

    public function findByProductId($id)
    {
        return $this->priceModel->findByProductId($id);
    }

    /**
     * Creates a new price record.
     *
     * @param array   $data Expected to contain 'amount' (float) and 'currency' (array with 'label' and 'symbol').
     * @param \PDO $conn The database connection.
     *
     * @return array|null An array representing the created price, matching GraphQL PriceType, or null on failure.
     */
    public function create($data)
    {
        return $this->priceModel->create($data);
    }

    public function update($id, $data)
    {
        return $this->priceModel->update($id, $data);
    }

    public function delete($id)
    {
        return $this->priceModel->delete($id);
    }
}
