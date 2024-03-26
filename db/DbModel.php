<?php

namespace framework\core\db;

use app\models\User;
use framework\core\Application;
use framework\core\Model;

abstract class DbModel extends Model
{


    abstract public function tableName(): string;
    abstract public function primaryKey(): string;
    abstract public function attributes(): array;


    public function save(): bool
    {
        $tableName = $this->tableName();
        $attributes = $this->attributes();
        $params = array_map(fn($attr) => ":$attr", $attributes);

        $stm = self::prepare("INSERT INTO $tableName (".implode(',',$attributes).") 
                            VALUES(".implode(',',$params).")");

        foreach ($attributes as $attribute) {
            $stm->bindValue(":$attribute", $this->{$attribute});
        }

        $stm->execute();
        return true;
    }


    public function findOne(array $where)
    {
        $tableName = $this->tableName();
        $attributes = array_keys($where);

        $sql = implode("AND ", array_map(fn($attr) => "$attr = :$attr", $attributes));
        $stm = self::prepare("SELECT * FROM $tableName WHERE $sql");

        foreach($where as $key => $item) {
            $stm->bindValue(":$key", $item);
        }
        $stm->execute();
        return $stm->fetchObject(static::class);
    }


    public static function prepare(string $sql)
    {
        return Application::$app->db->pdo->prepare($sql);
    }
}