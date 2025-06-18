<?php

namespace App\Http\Controllers\Settings;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use App\Http\Controllers\Controller;

class CertificateController extends Controller
{
    public function edit()
    {
        $certificateName = null;
        $certificatePath = storage_path('app/public/certificates');

        if (File::exists($certificatePath)) {
            $files = File::files($certificatePath);
            $latestFile = collect($files)->sortByDesc(function ($file) {
                return $file->getMTime();
            })->first();

            $certificateName = $latestFile ? $latestFile->getFilename() : null;
        }

        return Inertia::render('settings/Certificate', [
            'certificateName' => $certificateName,
        ]);
    }

    public function upload(Request $request)
    {
        $request->validate([
            'certificate' => [
                'required',
                'file',
                'max:2048',
                function ($attribute, $value, $fail) {
                    $extension = strtolower($value->getClientOriginalExtension());
                    if ($extension !== 'pem') {
                        $fail('El certificado debe ser un archivo PEM.');
                    }
                },
            ],
        ]);

        try {
            $file = $request->file('certificate');
            $filename = $file->getClientOriginalName();

            if (Storage::disk('public')->exists('certificates')) {
                Storage::disk('public')->deleteDirectory('certificates');
            }

            Storage::disk('public')->putFileAs('certificates', $file, $filename);

            return redirect()->route('certificate.edit')->with('success', 'Certificado subido correctamente');
        } catch (\Exception $e) {
            Log::error('Certificate upload failed', ['error' => $e->getMessage()]);
            return redirect()->route('certificate.edit')->with('error', 'Fallo al subir el certificado: ' . $e->getMessage());
        }
    }
}