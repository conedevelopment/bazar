<?php

namespace Bazar\Http\Controllers;

use Bazar\Http\Requests\MediumStoreRequest as StoreRequest;
use Bazar\Http\Requests\MediumUpdateRequest as UpdateRequest;
use Bazar\Jobs\MoveFile;
use Bazar\Jobs\PerformConversions;
use Bazar\Models\Medium;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
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
        File::ensureDirectoryExists(Storage::disk('local')->path('chunks'));

        if (Gate::getPolicyFor($class = Medium::getProxiedClass())) {
            $this->authorizeResource($class);
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $media = Medium::proxy()
                    ->newQuery()
                    ->filter($request)
                    ->latest()
                    ->paginate($request->input('per_page'));

        return Response::json($media);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Bazar\Http\Requests\MediumStoreRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreRequest $request): JsonResponse
    {
        $file = $request->file('file');

        $path = Storage::disk('local')->path("chunks/{$file->getClientOriginalName()}");

        File::append($path, $file->get());

        if ($request->has('is_last') && ! $request->boolean('is_last')) {
            return Response::json(['uploaded' => true]);
        }

        $medium = Medium::proxy()::createFrom($path);

        MoveFile::withChain($medium->convertable() ? [new PerformConversions($medium)] : [])
                ->dispatch($medium, $path);

        return Response::json($medium, JsonResponse::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  \Bazar\Models\Medium  $medium
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Medium $medium): JsonResponse
    {
        return Response::json($medium);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Bazar\Http\Requests\MediumUpdateRequest  $request
     * @param  \Bazar\Models\Medium  $medium
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateRequest $request, Medium $medium): JsonResponse
    {
        $medium->update($request->validated());

        return Response::json(['updated' => true]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Bazar\Models\Medium  $medium
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Medium $medium): JsonResponse
    {
        $medium->delete();

        return Response::json(['deleted' => true]);
    }
}
