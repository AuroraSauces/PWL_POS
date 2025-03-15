<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function foodBeverage()
    {
        return view('category.food_beverage');
    }

    public function beautyHealth()
    {
        return view('category.beauty_health');
    }

    public function homeCare()
    {
        return view('category.home_care');
    }

    public function babyKid()
    {
        return view('category.baby_kid');
    }
}
