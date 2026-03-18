<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;

class TravelController extends Controller
{
    public function index()
    {
        return view('finance.travel.index');
    }

    public function show()
    {
        return view('finance.travel.show');
    }
}
