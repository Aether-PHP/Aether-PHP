<?php

namespace App\Models;

use Aether\Database\Models\Model;
use Aether\Database\Models\ModelTemplate;


class User extends DatabaseModel {

    public function __construct(array $_conditions = []){
        parent::__construct(
            (new ModelTemplate("aetherphp", "users", $_conditions))->_template()
        );
    }

    /**
     * @return mixed
     */
    public function _getEmail(){
        return isset($this->email) ? $this->email : null;
    }

    public function _getUsername(){
        return isset($this->username) ? $this->username : null;
    }

}