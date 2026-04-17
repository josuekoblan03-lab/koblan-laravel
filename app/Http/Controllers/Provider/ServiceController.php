<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Prestation;
use App\Models\ServiceType;
use App\Models\Media;
use App\Services\CloudinaryService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ServiceController extends Controller
{
    protected CloudinaryService $cloudinary;

    public function __construct(CloudinaryService $cloudinary)
    {
        $this->cloudinary = $cloudinary;
    }

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
            return ($s->category ? $s->category->name : 'Sans Catégorie') . '-' . $s->name;
        });
        
        return view('provider.create-service', compact('services'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'        => 'required|string|max:255',
            'description'  => 'nullable|string',
            'price'        => 'required|numeric|min:0',
            'service_type_id' => 'required|exists:service_types,id',
            'medias.*'     => 'nullable|file|mimes:jpeg,png,jpg,gif,mp4|max:10240'
        ]);

        $data = $request->only('title', 'price', 'service_type_id');
        $data['description'] = $request->input('description') ?? '';

        $prestation = tap(new Prestation($data), function($p) {
            $p->user_id = Auth::id();
            $p->status  = 'pending';
            $p->save();
        });

        if ($request->hasFile('medias')) {
            foreach ($request->file('medias') as $index => $file) {
                if (!$file->isValid()) continue;

                $isImage = str_starts_with($file->getMimeType(), 'image/');
                $url     = null;

                // Essaye Cloudinary d'abord (stockage permanent)
                if ($this->cloudinary->isConfigured()) {
                    $url = $this->cloudinary->upload($file, 'koblan/services');
                }

                // Fallback: stockage local Laravel
                if (!$url) {
                    $url = $file->store('services', 'public');
                }

                Media::create([
                    'prestation_id' => $prestation->id,
                    'url'           => $url,
                    'type'          => $isImage ? 'image' : 'video',
                    'is_main'       => $index === 0,
                    'order'         => $index,
                ]);
            }
        }

        return redirect()->route('prestataire.services.index')
            ->with('success', 'Prestation publiée avec succès ! En attente de validation admin.');
    }

    public function edit(Prestation $service)
    {
        if ($service->user_id !== Auth::id()) abort(403);

        $services = ServiceType::with('category')->get()->sortBy(function($s) {
            return ($s->category ? $s->category->name : 'Sans Catégorie') . '-' . $s->name;
        });

        $service->load('medias');

        return view('provider.edit-service', [
            'prest'    => $service,
            'services' => $services,
            'medias'   => $service->medias,
        ]);
    }

    public function update(Request $request, Prestation $service)
    {
        if ($service->user_id !== Auth::id()) abort(403);

        $request->validate([
            'title'           => 'required|string|max:255',
            'description'     => 'nullable|string',
            'price'           => 'required|numeric|min:0',
            'service_type_id' => 'required|exists:service_types,id',
        ]);

        $service->update($request->only('title', 'description', 'price', 'service_type_id'));

        if ($request->hasFile('medias')) {
            $maxOrder = $service->medias()->max('order') ?? -1;

            foreach ($request->file('medias') as $index => $file) {
                if (!$file->isValid()) continue;

                $isImage = str_starts_with($file->getMimeType(), 'image/');
                $url     = null;

                if ($this->cloudinary->isConfigured()) {
                    $url = $this->cloudinary->upload($file, 'koblan/services');
                }

                if (!$url) {
                    $url = $file->store('services', 'public');
                }

                Media::create([
                    'prestation_id' => $service->id,
                    'url'           => $url,
                    'type'          => $isImage ? 'image' : 'video',
                    'is_main'       => false,
                    'order'         => $maxOrder + $index + 1,
                ]);
            }
        }

        return redirect()->route('prestataire.services.index')
            ->with('success', 'Prestation mise à jour avec succès !');
    }

    public function destroy(Prestation $service)
    {
        if ($service->user_id !== Auth::id()) abort(403);

        foreach ($service->medias as $media) {
            // Ne supprime le fichier local que si c'est un chemin relatif (pas une URL Cloudinary)
            if (!str_starts_with($media->url, 'http')) {
                Storage::disk('public')->delete($media->url);
            }
            $media->delete();
        }

        $service->delete();

        return redirect()->route('prestataire.services.index')
            ->with('success', 'Prestation supprimée avec succès.');
    }
}
