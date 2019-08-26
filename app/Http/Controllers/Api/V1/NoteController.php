<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseApiController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Exceptions\BaseApiException;
use App\Http\Resources\Api\V1\NoteCollection;
use App\Http\Resources\Api\V1\NoteResource;
use Exception;
use App\Http\Resources\Api\V1\ResponseResource;
use App\Models\ResponseData;
use Illuminate\Support\Facades\DB;
use App\Models\Book;
use App\Models\Highlight;

class NoteController extends BaseApiController
{
    public function index(Request $request, $id)
    {
        try {
            $hightLight = Highlight::query()->find($id);
            if (!$hightLight)
                throw new BaseApiException('Highlight not found!', 404);
            
            if ($request->user()->cant('view', $hightLight))
                throw new BaseApiException('You don\'t have permission to view this note');

            NoteResource::withoutWrapping();
            return NoteResource::make($hightLight);

        } catch (BaseApiException $exception) {
            return $this->_apiExceptionRepository->throwException($exception->getCode(), $exception->getMessage());

        } catch (Exception $exception) {
            return $this->_apiExceptionRepository->throwException(400, 'Fail to get list highlights');

        }
    }

    public function create(Request $request)
    {
        try {
            DB::beginTransaction();
            $bookId = $request->get('book_id');
            $book = Book::query()->find($bookId);
            if (!$book)
                throw new BaseApiException('Book not found.', 404);

            $noteData = [
                'highlight_ref' => $request->get('highlight_ref', ''),
                'note' => $request->get('note', ''),
                'short_desc' => $request->get('short_desc', ''),
                'user_id' => $request->user()->id,
                'book_id' => $bookId
            ];

            $hightLight = new Highlight();
            $hightLight->fill($noteData)->save();
            
            DB::commit();
            
            return $this->_apiExceptionRepository->throwException(200, 'Create new note success');

        } catch (BaseApiException $exception) {
            DB::rollBack();
            return $this->_apiExceptionRepository->throwException($exception->getCode(), $exception->getMessage());

        } catch (Exception $exception) {
            DB::rollBack();
            return $this->_apiExceptionRepository->throwException(400, 'Fail to create note');

        }
    }

    public function edit(Request $request, $highlightId)
    {
        try {
            DB::beginTransaction();

            $highlight = Highlight::query()->findOrFail($highlightId);
            if ($request->user()->cant('update', $highlight))
                throw new BaseApiException('You don\'t have permission to view this note', 404);

            $noteData = [
                'highlight_ref' => $request->get('highlight_ref', $highlight->highlight_ref),
                'note' => $request->get('note', $highlight->note),
                'short_desc' => $request->get('short_desc', $highlight->short_desc),
            ];

            $highlight->fill($noteData)->update();

            DB::commit();
            
            return $this->_apiExceptionRepository->throwException(200, 'Update note successful');

        } catch (BaseApiException $exception) {
            DB::rollBack();
            return $this->_apiExceptionRepository->throwException($exception->getCode(), $exception->getMessage());

        } catch (Exception $exception) {
            DB::rollBack();
            return $this->_apiExceptionRepository->throwException(400, 'Fail to update note');

        }
    }

    public function show(Request $request, $id)
    {
        try {
            $hightLight = Highlight::query()->find($id);
            if (!$hightLight)
                throw new BaseApiException('Highlight not found!', 404);
            
            if ($request->user()->cant('view', $hightLight))
                throw new BaseApiException('You don\'t have permission to view this note', 400);

            NoteResource::withoutWrapping();
            return NoteResource::make($hightLight);

        } catch (BaseApiException $exception) {
            return $this->_apiExceptionRepository->throwException($exception->getCode(), $exception->getMessage());

        } catch (Exception $exception) {
            return $this->_apiExceptionRepository->throwException(400, 'Fail to get highlight information');

        }
    }

    public function delete(Request $request, $highlightId)
    {
        try {
            DB::beginTransaction();
            $hightLight = Highlight::query()->find($highlightId);
            if (!$hightLight)
                throw new BaseApiException('Highlight not found', 404);

            if ($request->user()->cant('delete', $hightLight))
                throw new BaseApiException('You don\'t have permission to view this note', 400);

            $hightLight->delete();

            DB::commit();
            
            return $this->_apiExceptionRepository->throwException(200, 'Delete note successful');

        } catch (BaseApiException $exception) {
            DB::rollBack();
            return $this->_apiExceptionRepository->throwException($exception->getCode(), $exception->getMessage());

        } catch (Exception $exception) {
            DB::rollBack();
            
            return $this->_apiExceptionRepository->throwException(400, 'Fail to delete note');
        }
    }

    public function listOfBook(Request $request, $bookId)
    {
        try {
            $book = Book::query()->find($bookId);
            if (!$book)
                throw new BaseApiException('Book not found', 404);

            $highlights = Highlight::query()
                ->where('user_id', $request->user()->id)
                ->where('book_id', $bookId)
                ->get();
            
            return NoteCollection::make($highlights);
        } catch (BaseApiException $exception) {
            DB::rollBack();
            return $this->_apiExceptionRepository->throwException($exception->getCode(), $exception->getMessage());

        } catch (Exception $exception) {
            return $this->_apiExceptionRepository->throwException(400, 'Fail to get list highlight of book');
        }
    }
}
