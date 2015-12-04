<?php
class User_model extends common_model {
    private $table_name;

    public function __construct() {
        $this->table_name = 'users';

        parent::__construct($this->table_name);
    }
    
    

}