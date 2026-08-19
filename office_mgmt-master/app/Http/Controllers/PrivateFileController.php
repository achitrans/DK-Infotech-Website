<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class PrivateFileController extends Controller
{
    public function show($path)
    {
        try {
            $path = decrypt($path);
        } catch (DecryptException $e) {
            abort(404);
        }

        if (!Storage::exists($path)) {
            abort(404);
        }

        $mime = Storage::mimeType($path);

        return response(
            Storage::get($path),
            200,
            [
                'Content-Type' => $mime,
                'Content-Disposition' => 'inline',
            ]
        );
    }
}
