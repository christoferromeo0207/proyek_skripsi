<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\Request;
use Carbon\Carbon;


class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        $selectedYear = $request->input('year', date('Y'));
        $years        = range(date('Y') - 5, date('Y') + 5);
        
        $categories = Category::with(['posts' => function($query) use ($selectedYear) {
            $query->whereYear('tanggal_awal', '<=', $selectedYear)
                ->whereYear('tanggal_akhir', '>=', $selectedYear)
                ->orderBy('title'); 
        }])->orderBy('name')->get();

        // ← ADD THIS:
        $totalMitra = Post::count();

        return view('schedule', [
            'categories'    => $categories,
            'years'         => $years,
            'selectedYear'  => $selectedYear,
            'currentYear'   => date('Y'),
            'currentMonth'  => date('n'),
            'totalMitra'    => $totalMitra,
        ]);
    }
}