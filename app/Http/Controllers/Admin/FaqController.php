<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Models\Unit;
use Illuminate\Http\Request;

class FaqController extends Controller
{

        public function index(Request $request)
        {
            $query = Faq::with('unit');

            if ($request->filled('search')) {
                $search = mb_strtolower($request->validate([
                    'search' => ['required', 'string', 'max:100'],
                ])['search']);

                $query->where(function ($q) use ($search) {
                    $q->whereRaw('LOWER(pertanyaan) LIKE ?', ["%{$search}%"])
                        ->orWhereRaw('LOWER(jawaban) LIKE ?', ["%{$search}%"]);
                });
            }

            $faqs = $query->orderByDesc('id')->paginate(10);
            $units = Unit::orderBy('nama_unit')->get();

            return view('admin.faq', compact('faqs', 'units'));
        }

        public function store(Request $request)
        {
            $validated = $request->validate([
                'pertanyaan' => 'required|string|max:255',
                'jawaban'    => 'required|string',
                'id_unit'    => 'required|exists:unit,id',
                'status'     => 'required|in:aktif,nonaktif',
            ]);

            Faq::create($validated);

            return redirect()
                ->route('admin.faq.index')
                ->with('success', 'FAQ berhasil ditambahkan.');
        }


    public function update(Request $request, Faq $faq)
    {
        $data = $request->validate([
            'pertanyaan' => 'required|string|max:255',
            'jawaban' => 'required|string',
            'id_unit' => 'nullable|exists:unit,id',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        $faq->update($data);

        return redirect()
            ->route('admin.faq.index')
            ->with('success', 'FAQ berhasil diperbarui.');
    }

    public function destroy(Faq $faq)
    {
        $faq->delete();

        return redirect()
            ->route('admin.faq.index')
            ->with('success', 'FAQ berhasil dihapus.');
    }
}
