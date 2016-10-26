<?php

namespace Devoralive\TrafficLimit\Services;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;

/**
 * Class TrafficLimitService
 *
 * @package Devoralive\TrafficLimit\Services
 */
class TrafficLimitService
{
    /**
     * TrafficLimitService constructor.
     *
     * @param $enabled
     * @param $sncClient
     * @param $amount
     * @param $ttl
     * @param $serviceName
     */
    public function __construct($enabled, $sncClient, $amount, $ttl, $serviceName)
    {
        $this->enabled = $enabled;
        $this->sncClient = $sncClient;
        $this->amount = $amount;
        $this->ttl = $ttl;
        $this->serviceName = $serviceName;
    }

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * SncRedis Client
     *
     * @var \Snc\RedisBundle\Client\Phpredis\Client
     */
    protected $sncClient;

    /**
     * Enable/Disable Service
     *
     * @var bool
     */
    protected $enabled;

    /**
     * Amount of request allowed
     *
     * @var int
     */
    protected $amount;

    /**
     * Amount of time to expire a request
     *
     * @var int
     */
    protected $ttl;

    /**
     * Registered Service name
     *
     * @var string
     */
    protected $serviceName;

    /**
     * @var \Snc\RedisBundle\Client\Phpredis\Client
     */
    protected $sncService;
    
    /**
     * Throw new exception
     *
     * @throws TooManyRequestsHttpException
     */
    public function addRequest()
    {

    }
}
