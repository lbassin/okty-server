<?php

declare(strict_types=1);

namespace App\Event\Build;

/**
 * @author Laurent Bassin <laurent@bassin.info>
 */
interface BuildEvent
{
    public const BEFORE_BUILD = 'app.builder.before';
    public const ADD_CONTAINER = 'app.builder.container.add';
    public const AFTER_BUILD = 'app.builder.after';
}
