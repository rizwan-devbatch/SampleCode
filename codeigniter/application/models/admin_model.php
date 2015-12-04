<?php
class Admin_model extends common_model {
    private $table_name;

    public function __construct() {
        $this->table_name = 'Admins';

        parent::__construct($this->table_name);
    }

    public function isValidAdmin($email,$password)
    {
        $query = $this->db->where(array('email'=>$email,'password'=>$password))->get('admins');
        return $query->result();
    }

}