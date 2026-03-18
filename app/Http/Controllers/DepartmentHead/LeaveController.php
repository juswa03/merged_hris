<?php

namespace App\Http\Controllers\DepartmentHead;

use App\Http\Controllers\Controller;

class LeaveController extends Controller
{
    public function index()
    {
        return view('department-head.leaves.index');
    }

    public function show()
    {
        return view('department-head.leaves.show');
    }

    public function recommend()
    {
        return view('department-head.leaves.recommend');
    }
}
