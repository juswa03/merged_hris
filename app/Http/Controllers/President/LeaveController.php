<?php

namespace App\Http\Controllers\President;

use App\Http\Controllers\Controller;

class LeaveController extends Controller
{
    public function index()
    {
        return view('president.leaves.index');
    }

    public function show()
    {
        return view('president.leaves.show');
    }
}
