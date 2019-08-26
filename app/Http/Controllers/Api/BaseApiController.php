<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Exceptions\BaseApiException;
use App\Repositories\ApiExceptionRepository;

class BaseApiController extends Controller
{
    public $_apiExceptionRepository;

    public function __construct(Request $request, ApiExceptionRepository $apiExceptionRepository)
    {
        $this->_apiExceptionRepository = $apiExceptionRepository;

        if ($request->has('page_token') || $request->has('page_size')) {
            $page = $request->get('page_token', 1);
            $limit = $request->get('page_size', PAGINATE_LIMIT_RECORD);

            $request->request->add(['page' => $page, 'limit' => $limit]);
        }
    }
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
