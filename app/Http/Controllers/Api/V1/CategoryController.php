<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use \Exception;
use App\Models\Category;
use App\Http\Resources\Api\V1\CategoryCollection;
use App\Http\Resources\Api\V1\BookCollectionResource;
use App\Http\Resources\Api\V1\CategoryBookInfoCollection;
use App\Http\Controllers\Api\BaseApiController;
use App\Http\Exceptions\BaseApiException;
use Illuminate\Database\Eloquent\Collection;
use App\Http\Resources\Api\V1\CategoryResource;

class CategoryController extends BaseApiController
{
    public function index(Request $request) {
        try {
            if ($request->has('limit')) {
                $categories = Category::query()->paginate($request->get('limit'));
                $categories = new Collection($categories->items());
            } else {
                $categories = Category::query()->get();
            }
        
            return new CategoryCollection($categories);
        } catch (BaseApiException $exception) {
            return $this->_apiExceptionRepository->throwException($exception->getCode(), $exception->getMessage());
            
        } catch (Exception $exception) {
            return $this->_apiExceptionRepository->throwException(400, 'Fail to get categories.');

        }
    }

    public function show(Request $request, $id) {
        try {
            $category = Category::query()->findOrFail($id);

            CategoryResource::withoutWrapping();
            return new CategoryResource($category);

        } catch (BaseApiException $exception) {
            return $this->_apiExceptionRepository->throwException($exception->getCode(), $exception->getMessage());

        } catch (Exception $exception) {
            return $this->_apiExceptionRepository->throwException(400, 'Fail to get categories.');

        }
    }

    public function books(Request $request, $id) {
        try {
            $categories = Category::query()->with([
                'books.metas.type', 'books.authors', 'books.metas.type', 'books.summary.text', 'books.summary.audio', 'books.summary.video', 'books.categories',
                'books.recent' => function ($subQuery) use ($request) {
                    return $subQuery->where('user_id', $request->user()->id);
                },
                'books.progress' => function ($subQuery) use ($request) {
                    return $subQuery->where('user_id', $request->user()->id);
                }
            ])->findOrFail($id);
            foreach ($categories->books as $book) {
                $book->summary = $book->summary->first();
            }

            // CategoryBookInfoCollection::withoutWrapping();
            return new CategoryBookInfoCollection($categories->books);
        } catch (BaseApiException $exception) {
            return $this->_apiExceptionRepository->throwException($exception->getCode(), $exception->getMessage());

        } catch (Exception $exception) {
            return $this->_apiExceptionRepository->throwException(400, 'Fail to get books of categories.');

        }
    }
}
