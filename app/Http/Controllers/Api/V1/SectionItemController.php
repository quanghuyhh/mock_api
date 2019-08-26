<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseApiController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Exceptions\BaseApiException;
use App\Models\SectionItem;

use \Exception;
use App\Http\Resources\Api\V1\SectionItemResource;

class SectionItemController extends BaseApiController
{
    public function show(Request $request, $id) {
        try {
            $sectioItems = SectionItem::query()->findOrFail($id);
            
            $resource = new SectionItemResource($sectioItems);
            $resource->withoutWrapping();

            return $resource;
        } catch (BaseApiException $exception) {
            return $this->_apiExceptionRepository->throwException($exception->getCode(), $exception->getMessage());
        } catch (Exception $exception) {
            return $this->_apiExceptionRepository->throwException(400, 'Fail to get section items information');
        }
    }
}
