<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('prestations')->latest()->get();
        return view('admin.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'icon'        => 'nullable|string|max:100',
            'color'       => 'nullable|string|max:20',
            'description' => 'nullable|string|max:500',
        ]);

        $slug = \Str::slug($request->name);

        Category::create([
            'name'        => $request->name,
            'slug'        => $slug,
            'icon'        => $request->icon ?? 'fas fa-briefcase',
            'color'       => $request->color ?? '#FFD700',
            'description' => $request->description,
            'is_active'   => true,
        ]);

        return back()->with('success', '✅ Catégorie "' . $request->name . '" créée avec succès !');
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'icon'  => 'nullable|string',
            'color' => 'nullable|string',
        ]);

        $category->update([
            'name'  => $request->name,
            'slug'  => \Str::slug($request->name),
            'icon'  => $request->icon ?? $category->icon,
            'color' => $request->color ?? $category->color,
        ]);

        return back()->with('success', '✅ Catégorie mise à jour !');
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return back()->with('success', '🗑️ Catégorie supprimée.');
    }
}
