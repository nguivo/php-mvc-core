<?php

namespace framework\core\forms;

use framework\core\Model;

class Form
{

    public static function begin($action = '', $method = 'post'): self
    {
        echo sprintf('<form action="%s" method="%s">', $action, $method);
        return new Form();
    }


    public function field(Model $model, string $attribute): InputField
    {
        return new InputField($model, $attribute);
    }


    public static function end(): void
    {
        echo '</form>';
    }

}