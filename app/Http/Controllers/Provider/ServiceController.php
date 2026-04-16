<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Prestation;
use App\Models\ServiceType;
use App\Models\Media;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ServiceController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $prestations = Prestation::where('user_id', $user->id)
            ->with(['serviceType.category', 'mainMedia'])
            ->latest()
            ->paginate(10);

        return view('provider.services', compact('prestations'));
    }

    public function create()
    {
        $services = ServiceType::with('category')->get()->sortBy(function($s) {
            return $s->category->name . '-' . $s->name;
        });
        
        return view('provider.create-service', compact('services'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'service_type_id' => 'required|exists:service_types,id',
            'medias.*' => 'nullable|file|mimes:jpeg,png,jpg,gif,mp4|max:10240'
        ]);

        $prestation = tap(new Prestation($request->only('title', 'description', 'price', 'service_type_id')), function($p) {
            $p->user_id = Auth::id();
            $p->is_active = 0; // Requires admin approval based on legacy logic
            $p->save();
        });

        if ($request->hasFile('medias')) {
            foreach ($request->file('medias') as $index => $file) {
                if ($file->isValid()) {
                    $path = $file->store('services', 'public');
                    $isImage = str_starts_with($file->getMimeType(), 'image/');
                    
                    Media::create([
                        'prestation_id' => $prestation->id,
                        'media_url' => $path,
                        'media_type' => $isImage ? 'image' : 'video',
                        'is_main' => $index === 0,
                        'order' => $index
                    ]);
                }
            }
        }

        return redirect()->route('prestataire.services.index')->with('success', 'Prestation publiée avec succès ! En attente de validation.');
    }

    public function edit(Prestation $service)
    {
        if ($service->user_id !== Auth::id()) {
            abort(403);
        }

        $services = ServiceType::with('category')->get()->sortBy(function($s) {
            return $s->category->name . '-' . $s->name;
        });

        $service->load('medias');

        return view('provider.edit-service', ['prest' => $service, 'services' => $services, 'medias' => $service->medias]);
    }

    public function update(Request $request, Prestation $service)
    {
        if ($service->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'service_type_id' => 'required|exists:service_types,id'
        ]);

        $service->update($request->only('title', 'description', 'price', 'service_type_id'));

        // Handle Media updates here in a robust way if required (append new media)
        if ($request->hasFile('medias')) {
            $maxOrder = $service->medias()->max('order') ?? -1;
            foreach ($request->file('medias') as $index => $file) {
                if ($file->isValid()) {
                    $path = $file->store('services', 'public');
                    $isImage = str_starts_with($file->getMimeType(), 'image/');
                    
                    Media::create([
                        'prestation_id' => $service->id,
                        'media_url' => $path,
                        'media_type' => $isImage ? 'image' : 'video',
                        'is_main' => false,
                        'order' => $maxOrder + $index + 1
                    ]);
                }
            }
        }

        return redirect()->route('prestataire.services.index')->with('success', 'Prestation mise à jour avec succès !');
    }

    public function destroy(Prestation $service)
    {
        if ($service->user_id !== Auth::id()) {
            abort(403);
        }

        foreach($service->medias as $media) {
            Storage::disk('public')->delete($media->media_url);
            $media->delete();
        }

        $service->delete();

        return redirect()->route('prestataire.services.index')->with('success', 'Prestation supprimée avec succès.');
    }
}
