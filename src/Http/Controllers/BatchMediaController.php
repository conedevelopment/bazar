<?php

namespace Bazar\Http\Controllers;

use Bazar\Contracts\Models\Medium;
use Bazar\Proxies\Medium as MediumProxy;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;

class BatchMediaController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        if (Gate::getPolicyFor($class = MediumProxy::getProxiedClass())) {
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
        $media = MediumProxy::query()->whereIn('id', $request->input('ids', []));

        $media->each(static function (Medium $medium): void {
            Storage::disk($medium->disk)->deleteDirectory($medium->id);
        });

        $media->delete();

        return Response::json(['deleted' => true]);
    }
}
