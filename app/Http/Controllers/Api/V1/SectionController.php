<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Section;
use App\Http\Resources\Api\V1\SectionCollection;
use App\Http\Resources\Api\V1\SectionItemCollection;

use \Exception;
use App\Http\Resources\Api\V1\SectionResource;
use App\Http\Controllers\Api\BaseApiController;
use App\Http\Exceptions\BaseApiException;
use Illuminate\Database\Eloquent\Collection;

class SectionController extends BaseApiController
{
    public function index(Request $request) {
        try {
            $query = Section::query()->with([
                'books', 'categories',
                'books.progress' => function ($subQuery) use ($request) {
                    return $subQuery->where('user_id', $request->user()->id);
                }
            ]);
            $layoutType = $request->get('layoutType', null);
            $itemType = $request->get('sectionItemType', null);

            $validLayoutType = null;
            $validItemType = null;

            if ($request->has('layoutType') && $layoutType) {
                $layoutTypes = array_flip(get_list_section_layout());
                $validLayoutType = $layoutTypes[$layoutType] ?? null;
            }

            if ($request->has('sectionItemType') && $itemType) {
                $itemTypes = array_flip(get_list_section_type());

                $validItemType = $itemTypes[$itemType] ?? null;
            }

            if (!is_null($validLayoutType)) {
                $query = $query->where('layout_type', $validLayoutType);
            }

            if (!is_null($validItemType)) {
                $query = $query->where('section_item_type', $validItemType);
            }

            if ($request->has('limit')) {
                $sections = $query->paginate($request->get('limit'));
                $sections = new Collection($sections->items());
            } else {
                $sections = $query->get();
            }

            return new SectionCollection($sections);
        } catch (BaseApiException $exception) {
            return $this->_apiExceptionRepository->throwException($exception->getCode(), $exception->getMessage());
        } catch (Exception $exception) {
            return $this->_apiExceptionRepository->throwException(400, 'Fail to get sections');
        }
    }

    public function browse(Request $request) {
        try {
            $query = Section::query()->with([
                'books', 'categories',
                'books.progress' => function ($subQuery) use ($request) {
                    return $subQuery->where('user_id', $request->user()->id);
                }
            ]);
            $layoutType = $request->get('layoutType', null);
            $itemType = $request->get('sectionItemType', null);

            $validLayoutType = null;
            $validItemType = null;

            if ($request->has('layoutType') && $layoutType) {
                $layoutTypes = array_flip(get_list_section_layout());
                $validLayoutType = $layoutTypes[$layoutType] ?? null;
            }

            if ($request->has('sectionItemType') && $itemType) {
                $itemTypes = array_flip(get_list_section_type());

                $validItemType = $itemTypes[$itemType] ?? null;
            }

            if (!is_null($validLayoutType)) {
                $query = $query->where('layout_type', $validLayoutType);
            }

            if (!is_null($validItemType)) {
                $query = $query->where('section_item_type', $validItemType);
            }

            $sections = $query->get();

            return new SectionCollection($sections);
        } catch (BaseApiException $exception) {
            return $this->_apiExceptionRepository->throwException($exception->getCode(), $exception->getMessage());
        } catch (Exception $exception) {
            return $this->_apiExceptionRepository->throwException(400, 'Fail to get sections.');
        }
    }

    public function show(Request $request, $id) {
        try {
            $sections = Section::query()->findOrFail($id);
            $resource = new SectionResource($sections);
            $resource->withoutWrapping();
            return $resource;
        } catch (BaseApiException $exception) {
            return $this->_apiExceptionRepository->throwException($exception->getCode(), $exception->getMessage());
        } catch (Exception $exception) {
            return $this->_apiExceptionRepository->throwException(400, 'Fail to get section information.');
        }
    }

    public function items(Request $request, $sectionId) {
        try {
            $section = Section::query()->findOrFail($sectionId);

            
            $type = get_list_section_type($section->section_item_type);

            if ($type == 'book') {
                $query = $section->books();
            } else {
                $query = $section->categories();
            }

            if ($request->has('limit')) {
                $query = $query->paginate($request->get('limit'));
                $items = new Collection($query->items());
            } else {
                $items = $type == 'book' ? $section->books : $section->categories;
            }
            
            return new SectionItemCollection($items);
            
        } catch (BaseApiException $exception) {
            return $this->_apiExceptionRepository->throwException($exception->getCode(), $exception->getMessage());
        } catch (Exception $exception) {
            return $this->_apiExceptionRepository->throwException(400, 'Fail to get item of section.');
        }
        
    }
}
