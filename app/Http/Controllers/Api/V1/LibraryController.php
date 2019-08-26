<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseApiController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Exceptions\BaseApiException;
use App\Http\Resources\Api\V1\LibraryCollection;
use App\Http\Resources\Api\V1\LibraryResource;
use App\Http\Resources\Api\V1\ResponseResource;
use App\Models\Book;
use App\Models\Library;
use App\Models\ResponseData;

use \Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class LibraryController extends BaseApiController
{
    public function list(Request $request)
    {
        try {
            $books = Library::query()
                ->with('book')
                ->where('user_id', $request->user()->id)
                ->get();
                
            return LibraryCollection::make($books);

        } catch (BaseApiException $exception) {
            return $this->_apiExceptionRepository->throwException($exception->getCode(), $exception->getMessage());
        } catch (Exception $exception) {
            $error = new ResponseData();
            $error->fill(['message' => 'Fail to get library of user' . $exception->getMessage(), 'code' => 404]);
            ResponseResource::withoutWrapping();

            return response()->json($error, 400);
        }
    }

    public function add(Request $request)
    {
        try {
            DB::beginTransaction();
            $bookId = $request->get('book_id', null);
            $book = Book::query()->findOrFail($bookId);

            $listStatus = array_flip(get_library_status());
            $status = strtolower($request->get('status', null));
            $libraryStatus = !is_null($listStatus[$status]) && is_numeric($listStatus[$status]) ? $listStatus[$status] : LIBRARY_STATUS_UNREAD;

            $libraryData = [
                'user_id' => $request->user()->id,
                'book_id' => $bookId,
                'status' => $libraryStatus,
                'progress' => (int) $request->get('progress', 0)
            ];

            $library = Library::query()
                ->where('book_id', $bookId)
                ->where('user_id', $request->user()->id)
                ->first();
            if (!$library)
                $library = new Library();
            
            $library->fill($libraryData)->save();
            
            DB::commit();
            ResponseResource::withoutWrapping();

            return new ResponseResource(new ResponseData(['code' => 200, 'message' => 'Add book to library success']));
        } catch (BaseApiException $exception) {
            return $this->_apiExceptionRepository->throwException($exception->getCode(), $exception->getMessage());
        } catch (Exception $exception) {
            $error = new ResponseData();
            $error->fill(['message' => 'Fail to add book to library' . $exception->getMessage(), 'code' => 400]);
            ResponseResource::withoutWrapping();

            DB::rollBack();
            return response()->json($error, 400);
        }
    }

    public function edit(Request $request, $bookId)
    {
        try {
            DB::beginTransaction();
            $book = Book::query()->findOrFail($bookId);

            $library = Library::query()
                ->where('book_id', $bookId)
                ->where('user_id', $request->user()->id)
                ->first();

            $libraryData = [];
            if ($request->has('status')) {
                $listStatus = array_flip(get_library_status());
                $status = strtolower($request->get('status'));
                $libraryStatus = $status && !is_null($listStatus[$status]) && is_numeric($listStatus[$status]) ? $listStatus[$status] : LIBRARY_STATUS_UNREAD;
                $libraryData['status'] = $libraryStatus;
            }
            
            if ($request->has('progress') || is_numeric($request->get('progress')))
                $libraryData['progress'] = (int) $request->get('progress', 0);

            if (!$library) {
                $libraryData['user_id'] = $request->user()->id;
                $libraryData['book_id'] = $bookId;

                $library = new Library();
            }
            
            $library->fill($libraryData)->save();
            
            DB::commit();
            ResponseResource::withoutWrapping();

            return new ResponseResource(new ResponseData(['code' => 200, 'message' => 'Update book of library success']));
        } catch (BaseApiException $exception) {
            return $this->_apiExceptionRepository->throwException($exception->getCode(), $exception->getMessage());
        } catch (Exception $exception) {
            $error = new ResponseData();
            $error->fill(['message' => 'Fail to update book of library' . $exception->getMessage(), 'code' => 400]);
            ResponseResource::withoutWrapping();

            DB::rollBack();
            return response()->json($error, 400);
        }
    }

    public function delete(Request $request, $bookId)
    {
        try {
            DB::beginTransaction();
            $library = Library::query()->where('book_id', $bookId)->where('user_id', $request->user()->id)->first();
            if (!$library)
                throw new Exception('Book not found on library');
                
            $library->delete();
            
            DB::commit();
            ResponseResource::withoutWrapping();

            return new ResponseResource(new ResponseData(['code' => 200, 'message' => 'Remove book from library success']));
        } catch (BaseApiException $exception) {
            return $this->_apiExceptionRepository->throwException($exception->getCode(), $exception->getMessage());
        } catch (Exception $exception) {
            $error = new ResponseData();
            $error->fill(['message' => 'Fail to remove book from library' . $exception->getMessage(), 'code' => 400]);
            ResponseResource::withoutWrapping();

            DB::rollBack();
            return response()->json($error, 400);
        }
    }

    public function show(Request $request, $bookId)
    {
        try {
            DB::beginTransaction();
            $library = Library::query()->where('book_id', $bookId)->where('user_id', $request->user()->id)->first();
            if (!$library)
                throw new Exception('Book not found in library');
            
            DB::commit();
            LibraryResource::withoutWrapping();

            return LibraryResource::make($library);
        } catch (BaseApiException $exception) {
            return $this->_apiExceptionRepository->throwException($exception->getCode(), $exception->getMessage());
        } catch (Exception $exception) {
            $error = new ResponseData();
            $error->fill(['message' => 'Book not found' . $exception->getMessage(), 'code' => 400]);
            ResponseResource::withoutWrapping();

            DB::rollBack();
            return response()->json($error, 400);
        }
    }

    public function getBooksByStatus(Request $request)
    {
        try {
            $query = Library::query()->with('book')->where('user_id', $request->user()->id);
            if ($request->has('status') && $request->get('status')) {
                $validStatus = array_flip(get_library_status());
                $_status = isset($validStatus[$request->get('status')]) ? $validStatus[$request->get('status')] : null;
                if ($_status != null) {
                    $query = $query->where('status', $_status);
                }
            }

            $library = $query->count();
            if (!$library)
                return new LibraryCollection(new Collection());

            if ($request->has('limit')) {
                $libraries = $query->paginate($request->get('limit'));
                $libraries = new Collection($libraries->items());
            } else {
                $libraries = $query->get();
            }

            return new LibraryCollection($libraries);

        } catch (BaseApiException $exception) {
            return $this->_apiExceptionRepository->throwException($exception->getCode(), $exception->getMessage());

        } catch (Exception $exception) {
            return $this->_apiExceptionRepository->throwException(400, 'Book not found');

        }
    }

    public function addBookToLibrary(Request $request, $bookId)
    {
        try {
            DB::beginTransaction();
            $book = Book::query()->find($bookId);
            if (!$book)
                throw new BaseApiException('Book not found!', 404);

            $listStatus = array_flip(get_library_status());
            $status = strtolower($request->get('status', null));
            $libraryStatus = isset($listStatus[$status]) ? $listStatus[$status] : LIBRARY_STATUS_UNREAD;

            $libraryData = [
                'user_id' => $request->user()->id,
                'book_id' => $bookId,
                'status' => $libraryStatus,
                'progress' => (int) $request->get('progress', 0)
            ];

            $library = Library::query()
                ->where('book_id', $bookId)
                ->where('user_id', $request->user()->id)
                ->first();
            if (!$library)
                $library = new Library();
            
            $library->fill($libraryData)->save();
            
            DB::commit();
 
            return $this->_apiExceptionRepository->throwException(200, 'Add book to library success');
        } catch (BaseApiException $exception) {
            DB::rollBack();
            return $this->_apiExceptionRepository->throwException($exception->getCode(), $exception->getMessage());

        } catch (Exception $exception) {
            DB::rollBack();
            return $this->_apiExceptionRepository->throwException(400, 'Fail to add book to library' . $exception->getMessage());

        }
    }

    public function updateBookInLibrary(Request $request, $bookId)
    {
        try {
            DB::beginTransaction();
            $book = Book::query()->find($bookId);
            if (!$book)
                throw new BaseApiException('Book not found', 404);

            $library = Library::query()
                ->where('book_id', $bookId)
                ->where('user_id', $request->user()->id)
                ->first();

            $libraryData = [];
            if ($request->has('status')) {
                $listStatus = array_flip(get_library_status());
                $status = strtolower($request->get('status'));
                $libraryStatus = $status && !is_null($listStatus[$status]) && is_numeric($listStatus[$status]) ? $listStatus[$status] : LIBRARY_STATUS_UNREAD;
                $libraryData['status'] = $libraryStatus;
            }
            
            if ($request->has('progress') || is_numeric($request->get('progress')))
                $libraryData['progress'] = (int) $request->get('progress', 0);

            if (!$library) {
                $libraryData['user_id'] = $request->user()->id;
                $libraryData['book_id'] = $bookId;

                $library = new Library();
            }
            
            $library->fill($libraryData)->save();
            
            DB::commit();

            return $this->_apiExceptionRepository->throwException(200, 'Update book of library success');

        } catch (BaseApiException $exception) {
            DB::rollBack();
            return $this->_apiExceptionRepository->throwException($exception->getCode(), $exception->getMessage());

        } catch (Exception $exception) {
            DB::rollBack();
            return $this->_apiExceptionRepository->throwException(400, 'Fail to update book of library');

        }
    }

    public function deleteBookFromLibrary(Request $request, $bookId)
    {
        try {
            DB::beginTransaction();
            $library = Library::query()
                ->where('book_id', $bookId)
                ->where('user_id', $request->user()->id)
                ->first();

            if (!$library)
                throw new BaseApiException('Book not found on library', 404);
                
            $library->delete();
            
            DB::commit();
            
            return $this->_apiExceptionRepository->throwException(200, 'Remove book from library success');

        } catch (BaseApiException $exception) {
            DB::rollBack();
            return $this->_apiExceptionRepository->throwException($exception->getCode(), $exception->getMessage());

        } catch (Exception $exception) {
            DB::rollBack();
            
            return $this->_apiExceptionRepository->throwException(400, 'Fail to remove book from library');
        }
    }
}
