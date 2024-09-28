<?php

class NotFound extends Controller
{
    public function __construct()
    {
        
    }
    public function index()
    {
        // TODO: Implement the logic for the 404 not found page
        $v = new View();
        $v->render('404');
    }
}