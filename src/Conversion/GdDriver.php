<?php

namespace Bazar\Conversion;

use Bazar\Contracts\Models\Medium;

class GdDriver extends Driver
{
    /**
     * Create a new GD image instance.
     *
     * @param  \Bazar\Contracts\Models\Medium  $medium
     * @return \Bazar\Conversion\Image
     */
    public function createImage(Medium $medium): Image
    {
        $image = new GdImage($medium);

        return $image->quality(
            $this->config['quality'] ?? 70
        );
    }
}
