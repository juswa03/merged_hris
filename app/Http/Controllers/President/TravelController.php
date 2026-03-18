<?php

namespace App\Http\Controllers\President;

use App\Http\Controllers\Controller;

class TravelController extends Controller
{
    public function index()
    {
        return view('president.travel.index');
    }

    public function show()
    {
        return view('president.travel.show');
    }
}
