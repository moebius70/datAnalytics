<?php

class Child_Model extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    
    public function select($query, $params = [])
    {
        return parent::select($query, $params);
    }

    public function execute($query, $params = [])
    {
        return parent::execute($query, $params);
    }
}