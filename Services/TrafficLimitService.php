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
    const TRAFFIC_LIMIT_PREFIX = 'TFS';

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
     * Get the prefix of the service
     *
     * @return string
     */
    private function getPrefix()
    {
        return self::TRAFFIC_LIMIT_PREFIX. ':' . $this->serviceName . ':KEY:';
    }

    /**
     * Adds a request to the redis queue
     *
     * @param string $key
     *
     * @return int
     */
    private function addRequest($key)
    {
        $key = $this->getPrefix() . $key . ':' . microtime(true);
        $this->sncClient->setEx($key, $this->ttl, null);
        return $this->getCurrentRequests($key);
    }

    /**
     * Gets the amount of current requests by key
     *
     * @param string $key
     *
     * @return int
     */
    public function getCurrentRequests($key)
    {
        $activeRequests = $this->sncClient->keys(
            $this->getPrefix() . $key . '*'
        );
        return count($activeRequests);
    }

    /**
     * Clears the active requests and return the active requests, normally it's 0 unless error occurs.
     *
     * @param string $key
     *
     * @return int
     */
    public function clearPartnerRequests($key): int
    {
        $partnerActiveRequests = $this->sncClient->keys(
            $this->getPrefix() . $key . '*'
        );
        //Be sure to send only valid arrays to del command:
        if (!empty($partnerActiveRequests)) {
            $this->sncClient->del($partnerActiveRequests);
        }
        return $this->getCurrentRequests($key);
    }

    /**
     * Check Not max request allowed reached
     *
     * @param string $key
     *
     * @return int
     */
    public function processRequest($key)
    {
        if ($this->getCurrentRequests($key) < $this->amount) {
            return $this->addRequest($key);
        }
        throw new TooManyRequestsHttpException(
            60,
            'Too many request, please retry in 1 minute'
        );
    }

    /**
     * Process the request even if max has been exceeded
     *
     * @param string $key
     *
     * @return int
     */
    public function processRequestForce($key)
    {
        return $this->addRequest($key);
    }
}
