<?php

namespace Bazar\Http\Controllers;

use Bazar\Models\Medium;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BatchMediaController extends Controller
{
    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request): JsonResponse
    {
        $media = Medium::whereIn('id', $request->input('ids', []));

        $media->each(function (Medium $medium) {
            Storage::disk($medium->disk)->deleteDirectory($medium->id);
        });

        $media->delete();

        return response()->json(['deleted' => true]);
    }
}
