<?php

namespace Bazar\Jobs;

use Bazar\Models\Medium;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Throwable;

class MoveFile implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The medium instance.
     *
     * @var \Bazar\Models\Medium
     */
    public Medium $medium;

    /**
     * The path to the file.
     *
     * @var string
     */
    public string $path;

    /**
     * Preserve the original file.
     *
     * @var bool
     */
    public bool $preserve = false;

    /**
     * Delete the job if its models no longer exist.
     *
     * @var bool
     */
    public $deleteWhenMissingModels = true;

    /**
     * Create a new job instance.
     *
     * @param  \Bazar\Models\Medium  $medium
     * @param  string  $path
     * @param  bool  $preserve
     * @return void
     */
    public function __construct(Medium $medium, string $path, bool $preserve = false)
    {
        $this->path = $path;
        $this->medium = $medium;
        $this->preserve = $preserve;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        Storage::disk($this->medium->disk)->put(
            $this->medium->path(), File::get($this->path)
        );

        if (! $this->preserve && ! filter_var($this->path, FILTER_VALIDATE_URL)) {
            File::delete($this->path);
        }
    }

    /**
     * Handle a job failure.
     *
     * @param  \Throwable  $exception
     * @return void
     */
    public function failed(Throwable $exception): void
    {
        $this->medium->delete();

        if (! filter_var($this->path, FILTER_VALIDATE_URL)) {
            File::delete($this->path);
        }
    }
}

