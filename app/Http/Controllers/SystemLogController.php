<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\SystemLog;

class SystemLogController extends Controller
{
    public function index(Request $request) {
        return response(['success' => ['logs' => SystemLog::orderBy('id', 'desc')->get()]], 200);
    }
}
