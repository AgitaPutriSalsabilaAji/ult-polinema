<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;

class DashboardController extends Controller
{
    public function index()
    {
        $jumlahFaq = Faq::where('status', 'aktif')->count();

        return view('admin.dashboard', compact('jumlahFaq'));
    }
}
