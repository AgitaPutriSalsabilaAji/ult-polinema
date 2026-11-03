<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Models\Unit;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FaqController extends Controller
{
    public function index(Request $request): View
    {
        $categories = Unit::query()
            ->whereIn('id', Faq::query()
                ->select('id_unit')
                ->where('status', 'aktif'))
            ->orderBy('nama_unit')
            ->get(['id', 'nama_unit']);
        $categoryIds = $categories->modelKeys();

        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'kategori' => ['nullable', 'integer', Rule::in($categoryIds)],
        ]);
        $search = trim($validated['search'] ?? '');
        $selectedCategory = isset($validated['kategori']) ? (int) $validated['kategori'] : null;

        $faqs = Faq::query()
            ->with('unit')
            ->where('status', 'aktif')
            ->when($search !== '', function (Builder $query) use ($search): void {
                $query->where(function (Builder $query) use ($search): void {
                    $query->where('pertanyaan', 'like', '%'.$search.'%')
                        ->orWhere('jawaban', 'like', '%'.$search.'%');
                });
            })
            ->when($selectedCategory !== null, function (Builder $query) use ($selectedCategory): void {
                $query->where('id_unit', $selectedCategory);
            })
            ->orderBy('id')
            ->get();

        return view('user.faq', [
            'faqs' => $faqs,
            'categories' => $categories,
            'search' => $search,
            'selectedCategory' => $selectedCategory,
        ]);
    }
}
