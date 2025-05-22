<?php

/**
 * Model
 * php version  8.2
 *
 * @category    Model
 * @description Model abstract class for all models
 * @package     App\Model
 * @author      Silvestrs Lignickis <silvestrsl47@gmail.com>
 * @license     https://www.apache.org/licenses/LICENSE-2.0 Apache License 2.0
 * @version     GIT: main
 * @link        None
 */

namespace App\Model;

/**
 * Class Model
 *
 * @category Model
 * @package  App\Model
 * @author   Silvestrs Lignickis <silvestrsl47@gmail.com>
 * @license  https://www.apache.org/licenses/LICENSE-2.0 Apache License 2.0
 * @link     None
 */

abstract class Model
{
    // protected string $connection;
    // protected string $table;
    // protected string $primaryKey = 'id';
    // protected string $primaryKeyType = 'int';
    // public bool $autoIncrement = true;

    abstract public static function findAll($conn);
    abstract public static function findById($id, $conn);
    abstract public static function create($data, $conn);
    abstract public static function update($id, $data, $conn);
    abstract public static function delete($id, $conn);
}
