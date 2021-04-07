<?php

namespace Bazar\Http\Controllers\Batch;

use Bazar\Http\Controllers\Controller;
use Bazar\Models\Medium;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        if (Gate::getPolicyFor($class = Medium::getProxiedClass())) {
            $this->middleware("can:batchDelete,{$class}")->only('destroy');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request): JsonResponse
    {
        $media = Medium::proxy()->newQuery()->whereIn('id', $request->input('id', []));

        $media->each(static function (Medium $medium): void {
            Storage::disk($medium->disk)->deleteDirectory($medium->id);
        });

        $media->delete();

        return Response::json(['deleted' => true]);
    }
}
