<?php

namespace Bazar\Http\Controllers;

use Bazar\Http\Controllers\Controller;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Response as ResponseFactory;
use Symfony\Component\HttpFoundation\Response;

class DownloadController extends Controller
{
    /**
     * Download the file.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function __invoke(Request $request): Response
    {
        try {
            $url = Crypt::decryptString($request->input('url'));

            return ResponseFactory::streamDownload(function () use ($url) {
                echo file_get_contents(
                    $url, false, stream_context_create(['ssl' => ['verify_peer' => false]])
                );
            }, basename($url));
        } catch (DecryptException $e) {
            return response(__('Invalid URL'), 403);
        }
    }
}
