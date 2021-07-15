<?php

namespace Cone\Bazar\Http\Controllers;

use Cone\Bazar\Http\Requests\MediumStoreRequest as StoreRequest;
use Cone\Bazar\Http\Requests\MediumUpdateRequest as UpdateRequest;
use Cone\Bazar\Jobs\MoveFile;
use Cone\Bazar\Jobs\PerformConversions;
use Cone\Bazar\Models\Medium;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Gate;
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
                    ->paginate($request->input('per_page'))
                    ->withQueryString();

        return new JsonResponse($media);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Cone\Bazar\Http\Requests\MediumStoreRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreRequest $request): JsonResponse
    {
        $file = $request->file('file');

        $path = Storage::disk('local')->path("chunks/{$file->getClientOriginalName()}");

        File::append($path, $file->get());

        if ($request->has('is_last') && ! $request->boolean('is_last')) {
            return new JsonResponse(['uploaded' => true]);
        }

        $medium = Medium::proxy()::createFrom($path);

        MoveFile::withChain($medium->convertable() ? [new PerformConversions($medium)] : [])
                ->dispatch($medium, $path);

        return new JsonResponse($medium, JsonResponse::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  \Cone\Bazar\Models\Medium  $medium
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Medium $medium): JsonResponse
    {
        return new JsonResponse($medium);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Cone\Bazar\Http\Requests\MediumUpdateRequest  $request
     * @param  \Cone\Bazar\Models\Medium  $medium
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateRequest $request, Medium $medium): JsonResponse
    {
        $medium->update($request->validated());

        return new JsonResponse(['updated' => true]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Cone\Bazar\Models\Medium  $medium
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Medium $medium): JsonResponse
    {
        $medium->delete();

        return new JsonResponse(['deleted' => true]);
    }
}
