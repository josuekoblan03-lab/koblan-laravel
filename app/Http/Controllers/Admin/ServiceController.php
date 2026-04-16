<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ServiceType;
use App\Models\Category;

class ServiceController extends Controller
{
    public function index()
    {
        $categories = Category::orderBy('name')->get();
        // Récupérer les services typés (les sous-catégories)
        $services_list = ServiceType::with('category')->withCount('prestations')->get();
        
        return view('admin.services.index', compact('services_list', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id'
        ]);

        ServiceType::create([
            'name' => $request->name,
            'category_id' => $request->category_id
        ]);

        return back()->with('success', '✅ Service ajouté avec succès !');
    }

    public function update(Request $request, ServiceType $service)
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $service->update(['name' => $request->name]);

        return back()->with('success', '✅ Nom du service modifié.');
    }

    public function destroy(ServiceType $service)
    {
        $service->delete();
        return back()->with('success', '🗑️ Service supprimé.');
    }
}
