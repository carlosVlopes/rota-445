<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(Request $request): View
    {
        $query = Category::withCount('products');

        if ($search = $request->get('search')) {
            $query->where('name', 'like', "%{$search}%");
        }

        if ($request->filled('status')) {
            $query->where('active', $request->get('status') === 'active');
        }

        $categories = $query->orderBy('order')->paginate(15)->withQueryString();

        return view('admin.categories.index', compact('categories'));
    }

    public function create(): View
    {
        return view('admin.categories.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'   => ['required', 'string', 'max:255', 'unique:categories,name'],
            'order'  => ['nullable', 'integer', 'min:0'],
            'active' => ['boolean'],
        ]);

        Category::create([
            'name'   => $validated['name'],
            'order'  => $validated['order'] ?? 0,
            'active' => $request->boolean('active', true),
        ]);

        return redirect()->route('admin.categorias.index')
            ->with('success', 'Categoria criada com sucesso.');
    }

    public function edit(Category $category): View
    {
        $category->loadCount('products');

        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $validated = $request->validate([
            'name'   => ['required', 'string', 'max:255', 'unique:categories,name,' . $category->id],
            'order'  => ['nullable', 'integer', 'min:0'],
            'active' => ['boolean'],
        ]);

        $category->update([
            'name'   => $validated['name'],
            'order'  => $validated['order'] ?? $category->order,
            'active' => $request->boolean('active', true),
        ]);

        return redirect()->route('admin.categorias.index')
            ->with('success', 'Categoria atualizada com sucesso.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        if ($category->products()->exists()) {
            return back()->with('error', 'Não é possível remover uma categoria que possui produtos.');
        }

        $category->delete();

        return redirect()->route('admin.categorias.index')
            ->with('success', 'Categoria removida com sucesso.');
    }
}
