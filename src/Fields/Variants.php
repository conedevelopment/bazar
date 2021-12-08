<?php

namespace Cone\Bazar\Fields;

use Cone\Root\Fields\HasMany;

class Variants extends HasMany
{
    /**
     * The Vue compoent.
     *
     * @var string
     */
    protected string $component = 'Variants';
}
