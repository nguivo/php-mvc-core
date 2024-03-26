<?php

namespace framework\core;

use framework\core\db\DbModel;

abstract class UserModel extends DbModel
{

    abstract public function getDisplayName(): string;

}