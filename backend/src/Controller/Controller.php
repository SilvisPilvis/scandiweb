<?php

/**
 * Model
 * php version  8.2
 *
 * @category    Controller
 * @description Controller abstract class for all controllers
 * @package     App\Controller
 * @author      Silvestrs Lignickis <silvestrsl47@gmail.com>
 * @license     https://www.apache.org/licenses/LICENSE-2.0 Apache License 2.0
 * @version     GIT: main
 * @link        None
 */

namespace App\Controller;

/**
 * Class Controller
 *
 * @category Controller
 * @package  App\Controller
 * @author   Silvestrs Lignickis <silvestrsl47@gmail.com>
 * @license  https://www.apache.org/licenses/LICENSE-2.0 Apache License 2.0
 * @link     None
 */

abstract class Controller
{
    // protected string $connection;
    // protected string $table;
    // protected string $primaryKey = 'id';
    // protected string $primaryKeyType = 'int';
    // public bool $autoIncrement = true;

    abstract public function findAll();
    abstract public function findById($id);
    abstract public function create($data);
    abstract public function update($id, $data);
    abstract public function delete($id);
}
