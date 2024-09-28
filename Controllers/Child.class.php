<?php

class Child extends Controller
{
    public function __construct() {
        //$model = new Child_Model();
    }

    public function index($n, $m)
    {
        // $this->render('index');
        echo "\nhello";
        echo "\n".$n;
        echo "\n".$m;
        echo "\nindex function in 'Path' class called";
    }
}

?>
