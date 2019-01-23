<?php

declare(strict_types=1);

namespace KunicMarko\JMSMessengerAdapter\Features\Fixtures\Project\Query;

final class DoesItWork
{
    public $works;

    public $notExposed;

    public $shouldBeNull;

    public function __construct(string $works, string $shouldBeNull = null)
    {
        $this->works = $works;
        $this->notExposed ='evenIfWeSetValue';
        $this->shouldBeNull = $shouldBeNull;
    }
}
