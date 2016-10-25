<?php

namespace Devoralive\TrafficLimit\Services;

use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;

class TrafficLimitService
{

    public function __construct($enabled, $redisDsn)
    {
        $this->enabled = $enabled;
        $this->redisDsn = $redisDsn;
    }

    /**
     * Redis DSN
     *
     * @var string
     */
    protected $redisDsn;

    /**
     * Enable/Disable Service
     *
     * @var bool
     */
    protected $enabled;

    /**
     * Throw new exception
     *
     * @throws TooManyRequestsHttpException
     */
    public function addRequest()
    {
        throw new TooManyRequestsHttpException('Too many request limit');
    }
}
