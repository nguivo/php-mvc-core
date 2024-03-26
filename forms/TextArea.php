<?php

namespace framework\core\forms;

class TextArea extends BaseField
{

    public function renderInput(): string
    {
        return sprintf('<textarea name="%s" rows="2" class="form-control %s">%s</textarea>',
            $this->attribute,
            $this->model->hasError($this->attribute),
            $this->model->{$this->attribute}? "has-error" : ""
        );
    }
}