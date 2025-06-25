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

        $categories = Category::with(['posts' => function ($q) use ($selectedYear) {
            $q->where(function ($query) use ($selectedYear) {
                $query->whereYear('tanggal_awal', '<', $selectedYear)
                    ->orWhere(function ($sub) use ($selectedYear) {
                        $sub->whereYear('tanggal_awal', $selectedYear);
                    });
            })
                ->where(function ($query) use ($selectedYear) {
                    $query->whereYear('tanggal_akhir', '>', $selectedYear)
                        ->orWhere(function ($sub) use ($selectedYear) {
                            $sub->whereYear('tanggal_akhir', $selectedYear);
                        });
                })
                ->with('transactions') // ← transactions ikut dimuat setelah filter posts
                ->orderBy('title');
        }])->orderBy('name')->get();

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
