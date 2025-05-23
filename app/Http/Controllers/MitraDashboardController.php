<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class MitraDashboardController extends Controller
{
    public function index()
    {
        return view('mitra.dashboardMitra');
    }

    public function __construct()
    {
        $this->middleware(['auth', 'role:mitra']);
    }
}
