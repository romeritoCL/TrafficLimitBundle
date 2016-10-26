# TrafficLimitBundle
Limit the amount of request to your application based on a defined key

It uses SNC\RedisBundle to connect to redis.
You can create as many traffic limit services as you require

Installation
============

Step 1: Download the Bundle
---------------------------

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```console
$ composer require devoralive/traffic-limit-bundle "dev-master"
```

This will also install snc\RedisBundle if you did not have it installed

This command requires you to have Composer installed globally, as explained
in the [installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

Step 2: Enable the Bundle
-------------------------

Then, enable the bundle by adding it to the list of registered bundles
in the `app/AppKernel.php` file of your project:

```php
<?php
// app/AppKernel.php

// ...
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // ...
            new Snc\RedisBundle\SncRedisBundle(), //Mandatory of using this bundle
            new Devoralive\TrafficLimit\TrafficLimitBundle(), //Include the bundle
        );

        // ...
    }

    // ...
}
```

Step 3: Configure the bundle
-------------------------

To use this bundle you need to add configuration into the config.yml file inside you app/config directory

We supose you al ready included at least one connection to redis from the sncRedisBundle
If you need more information have a look at [SncRedisBundle configuration](https://github.com/snc/SncRedisBundle/blob/master/Resources/doc/index.md)


```yaml
snc_redis:
    clients:
        default:
            type: phpredis
            alias: default
            dsn: redis://localhost:6379

        traffic_limit:
            type: phpredis
            alias: default
            dsn: redis://localhost:6379
            
traffic_limit:
    low_limit:
        enabled: true
        snc_client: traffic_limit
        amount: 600
        ttl: 60
    high_limit:
        enabled: true
        snc_client: default
        amount: 6000
        ttl: 60
```

As you can see you can define as many traffic limit services as you desire. You can have 
infinite configurations. All are available in the container:

```php
<?php

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

namespace AppBundle\Controller;

/**
 * Class ApiController
 *
 * @package AppBundle\Controller
 */
class ApiController
{
    /**
     * Example on how to limit requests by IP
     *     
     * @param string $ip
     *
     * @return string 
     */
    public function getLocationAction(Request $request, $ip)
    {
        $this->get('traffic_limit.low_limit')->processRequest(
            $request->getClientIp()
        );
        $ipRangeManager = $this->get('ip_range_manager');
        $ipRange        = $ipRangeManager->getIpRangeByIp($ip);

        return new JsonResponse($ipRange);
    }
}
```

You can define the key, so you can limit the request by IP, userId, or anything you 
can identify as a variable.