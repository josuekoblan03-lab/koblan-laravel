<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;

class CloudinaryService
{
    protected string $cloudName;
    protected string $apiKey;
    protected string $apiSecret;
    protected string $uploadPreset;

    public function __construct()
    {
        $this->cloudName   = config('cloudinary.cloud_name', env('CLOUDINARY_CLOUD_NAME', ''));
        $this->apiKey      = config('cloudinary.api_key', env('CLOUDINARY_API_KEY', ''));
        $this->apiSecret   = config('cloudinary.api_secret', env('CLOUDINARY_API_SECRET', ''));
        $this->uploadPreset = env('CLOUDINARY_UPLOAD_PRESET', '');
    }

    public function isConfigured(): bool
    {
        return !empty($this->cloudName) && (!empty($this->uploadPreset) || (!empty($this->apiKey) && !empty($this->apiSecret)));
    }

    /**
     * Upload un fichier sur Cloudinary.
     * Retourne l'URL sécurisée Cloudinary ou null en cas d'échec.
     */
    public function upload(UploadedFile $file, string $folder = 'services'): ?string
    {
        if (!$this->isConfigured()) {
            return null;
        }

        $url = "https://api.cloudinary.com/v1_1/{$this->cloudName}/auto/upload";

        try {
            if (!empty($this->uploadPreset)) {
                // Mode unsigned upload (plus simple, sans secret)
                $response = Http::attach(
                    'file', file_get_contents($file->getRealPath()), $file->getClientOriginalName()
                )->post($url, [
                    'upload_preset' => $this->uploadPreset,
                    'folder' => $folder,
                ]);
            } else {
                // Mode signed upload
                $timestamp = time();
                $paramsToSign = "folder={$folder}&timestamp={$timestamp}";
                $signature = sha1($paramsToSign . $this->apiSecret);

                $response = Http::attach(
                    'file', file_get_contents($file->getRealPath()), $file->getClientOriginalName()
                )->post($url, [
                    'api_key'    => $this->apiKey,
                    'timestamp'  => $timestamp,
                    'signature'  => $signature,
                    'folder'     => $folder,
                ]);
            }

            if ($response->successful()) {
                return $response->json('secure_url');
            }
        } catch (\Exception $e) {
            \Log::warning('Cloudinary upload failed: ' . $e->getMessage());
        }

        return null;
    }
}
