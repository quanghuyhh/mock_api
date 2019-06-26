<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BaseApiController extends Controller
{
    /**
     * @param string $resourceName
     * @param array ...$args
     *
     * @return object
     */
    public function resource($resourceName, ...$args)
    {
        // Get's the request's api version, or the latest if undefined
        $v = config('app.api_version', config('app.api_latest'));

        $className = $this->getResourceClassname($resourceName, $v);
        $class = new \ReflectionClass($className);
        return $class->newInstanceArgs($args);
    }

    /**
     * Parse Api\BusinessResource for
     * App\Http\Resources\Api\v{$v}\BusinessResource
     *
     * @param string $className
     * @param string $v
     *
     * @return string
     */
    protected function getResourceClassname($className, $v)
    {
        $re = '/.*\\\\(.*)/';
        return 'App\Http\Resources\\' .
            preg_replace($re, 'Api\\v' . $v . '\\\$1', $className);
    }
}
