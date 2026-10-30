<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Models\Layanan;

class DashboardController extends Controller
{
    public function index()
    {
        $jumlahFaq = Faq::count();
        $jumlahLayanan = Layanan::count();

        return view('admin.dashboard', compact(
            'jumlahFaq',
            'jumlahLayanan'
        ));
    }
}
