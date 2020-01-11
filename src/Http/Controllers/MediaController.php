<?php

namespace Bazar\Http\Controllers;

use Bazar\Filters\Filters;
use Bazar\Filters\Type;
use Bazar\Http\Requests\MediumStoreRequest as StoreRequest;
use Bazar\Http\Requests\MediumUpdateRequest as UpdateRequest;
use Bazar\Jobs\MoveFile;
use Bazar\Jobs\PerformConversions;
use Bazar\Models\Medium;
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

        if (Gate::getPolicyFor(Medium::class)) {
            $this->authorizeResource(Medium::class);
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Resources\Json\ResourceCollection
     */
    public function index(Request $request): JsonResponse
    {
        $media = Medium::query()->filter(
            $request,
            Filters::make(Medium::class, [Type::make()])->searchIn('name')
        )->paginate($request->input('per_page', 25));

        return response()->json($media);
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
            return response()->json(['uploaded' => true]);
        }

        $medium = Medium::createFrom($path);

        MoveFile::withChain(
            $medium->isImage ? [new PerformConversions($medium)] : []
        )->dispatch($medium, $path);

        return response()->json($medium);
    }

    /**
     * Display the specified resource.
     *
     * @param  \Bazar\Models\Medium  $medium
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Medium $medium): JsonResponse
    {
        return response()->json($medium);
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

        return response()->json(['updated' => true]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Bazar\Models\Medium  $medium
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Medium $medium): JsonResponse
    {
        Storage::disk($medium->disk)->deleteDirectory($medium->id);

        $medium->delete();

        return response()->json(['deleted' => true]);
    }
}
