<?php

namespace App\Controllers;

class Categories extends BaseController
{
    public function index(): string
    {
        return view('pages/categories/content');
    }
}
