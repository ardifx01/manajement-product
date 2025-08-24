<?php

namespace App\Controllers;

class Products extends BaseController
{
    public function index(): string
    {
        return view('pages/products/content');
    }

    public function incomingItems(): string
    {
        return view('pages/products/incomingitems');
    }

    public function outgoingItems(): string
    {
        return view('pages/products/outgoingitems');
    }
}
