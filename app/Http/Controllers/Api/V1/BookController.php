<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\BookResource;
use App\Models\Book;
use App\Http\Resources\Api\V1\BookCollectionResource;
use App\Http\Controllers\Api\BaseApiController;
use App\Http\Exceptions\BaseApiException;
use App\Http\Resources\Api\V1\BookInfoResource;
use App\Http\Resources\Api\V1\NoteCollection;
use App\Http\Resources\Api\V1\NoteResource;
use App\Http\Resources\Api\V1\RecentBookCollection;
use App\Models\Highlight;
use App\Models\ReadingBook;
use App\Models\RecentBook;
use \Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class BookController extends BaseApiController
{
    public function index(Request $request) {
        try {
            $query = Book::query()
                ->with([
                    'authors', 'metas.type', 'summary.text', 'summary.audio', 'summary.video', 'categories',
                    'recent' => function ($subQuery) use ($request) {
                        return $subQuery->where('user_id', $request->user()->id);
                    },
                    'progress' => function ($subQuery) use ($request) {
                        return $subQuery->where('user_id', $request->user()->id);
                    }
                ]);

            if ($request->has('keyword') && $request->get('keyword')) {
                $_keyword = $request->get('keyword');
                $query = $query->where(function($subquery) use ($_keyword) {
                    return $subquery->where('title', 'LIKE', "%$_keyword%")
                                    ->orWhere('short_description', 'LIKE', "%$_keyword%");
                });
            }

            if ($request->has('limit')) {
                $books = $query->paginate($request->get('limit'));
                $books = new Collection($books->items());
            } else {
                $books = $query->get();
            }

            foreach ($books as $book) {
                $book->summary = $book->summary->first();
            }

            return new BookCollectionResource($books);
        } catch (BaseApiException $exception) {
            return $this->_apiExceptionRepository->throwException($exception->getCode(), $exception->getMessage());
        } catch (Exception $exception) {
            return $this->_apiExceptionRepository->throwException(400, 'Fail to get list book information.');
        }
    }

    public function show(Request $request, $id, $field = null) {
        try {
            BookResource::withoutWrapping();
            $book = Book::query()->with([
                'authors', 'metas.type', 'summary.text', 'summary.audio', 'summary.video', 'categories',
                'recent' => function ($subQuery) use ($request) {
                    return $subQuery->where('user_id', $request->user()->id);
                },
                'progress' => function ($subQuery) use ($request) {
                    return $subQuery->where('user_id', $request->user()->id);
                }
            ])->find($id);

            $validField = get_valid_book_fields($field);
            $book->summary = $book->summary->first();
            $book->field = $validField;
            return new BookResource($book);
        } catch (BaseApiException $exception) {
            return $this->_apiExceptionRepository->throwException($exception->getCode(), $exception->getMessage());
        } catch (Exception $exception) {
            return $this->_apiExceptionRepository->throwException(400, 'Fail to get book information.');
        }
    }

    public function highlights(Request $request, $id) {
        try {
            $book = Book::query()->find($id);
            if (!$book)
                throw new BaseApiException('Book not found!', 404);

            $query = $book->highlights()->where('user_id', $request->user()->id);
            if ($request->has('limit')) {
                $highlights = $query->paginate($request->get('limit'));
                $highlights = new Collection($highlights->items());
            } else {
                $highlights = $query->get();
            }

            return new NoteCollection($highlights);

        } catch (BaseApiException $exception) {
            return $this->_apiExceptionRepository->throwException($exception->getCode(), $exception->getMessage());

        } catch (Exception $exception) {
            return $this->_apiExceptionRepository->throwException(400, 'Fail to get book information.');

        }
    }

    public function allHighlight(Request $request) {
        try {
            $query = Book::query()->with([
                'authors', 'metas.type', 'summary.text', 'summary.audio', 'summary.video', 'categories',
                'recent' => function ($subQuery) use ($request) {
                    return $subQuery->where('user_id', $request->user()->id);
                },
                'progress' => function ($subQuery) use ($request) {
                    return $subQuery->where('user_id', $request->user()->id);
                },
                'highlightsCount' => function ($subQuery) use ($request) {
                    return $subQuery->where('user_id', $request->user()->id);
                }
            ])
            ->whereHas('highlights', function ($subQuery) use ($request) {
                $subQuery->where('user_id', $request->user()->id);
            });
            
            if ($request->has('limit')) {
                $books = $query->paginate($request->get('limit'));
                $books = new Collection($books->items());
            } else {
                $books = $query->get();
            }

            foreach ($books as $book) {
                $book->summary = $book->summary->first();
            }

            return new BookCollectionResource($books);

        } catch (BaseApiException $exception) {
            return $this->_apiExceptionRepository->throwException($exception->getCode(), $exception->getMessage());

        } catch (Exception $exception) {
            return $this->_apiExceptionRepository->throwException(400, 'Fail to get book information.');

        }
    }

    public function recent(Request $request) {
        try {
            $query = RecentBook::query()
                ->with(['book.authors', 'book.metas.type', 'book.categories',
                    'book.recent' => function ($subQuery) use ($request) {
                        return $subQuery->where('user_id', $request->user()->id);
                    },
                    'book.progress' => function ($subQuery) use ($request) {
                        return $subQuery->where('user_id', $request->user()->id);
                    }
                ])
                ->where('user_id', $request->user()->id)
                ->orderBy('updated_at', 'desc');

            if ($request->has('limit')) {
                $books = $query->paginate($request->get('limit'));
                $books = new Collection($books->items());
            } else {
                $books = $query->get();
            }

            $books = $books->unique('book_id');

            return new RecentBookCollection($books);
        } catch (BaseApiException $exception) {
            return $this->_apiExceptionRepository->throwException($exception->getCode(), $exception->getMessage());
        } catch (Exception $exception) {
            return $this->_apiExceptionRepository->throwException(400, 'Fail to get list book information.' . $exception->getMessage());
        }
    }

    public function addBookToRecent(Request $request, $bookId) {
        try {
            DB::beginTransaction();
            $book = Book::query()->find($bookId);
            if (!$book)
                throw new BaseApiException('Book not found!', 404);

            $recent = RecentBook::query()
                ->where('book_id', $bookId)
                ->where('user_id', $request->user()->id)
                ->first();

            if (!$recent) {
                $recent = new RecentBook();
            }

            $recentData = [
                'book_id' => $bookId,
                'user_id' => $request->user()->id,
            ];

            $recent->fill($recentData)->save();
            $recent->touch();

            DB::commit();
            return $this->_apiExceptionRepository->throwException(200, 'Add book to recent successful!');

        } catch (BaseApiException $exception) {
            DB::rollBack();
            return $this->_apiExceptionRepository->throwException($exception->getCode(), $exception->getMessage());

        } catch (Exception $exception) {
            DB::rollBack();
            return response()->json(['message' => 'Fail to get list book information.'], 400);

        }
    }

    public function progress(Request $request, $bookId) {
        try {
            DB::beginTransaction();
            $book = Book::query()->with(['authors', 'metas.type', 'categories',
                'recent' => function ($subQuery) use ($request) {
                    return $subQuery->where('user_id', $request->user()->id);
                },
                'progress' => function ($subQuery) use ($request) {
                    return $subQuery->where('user_id', $request->user()->id);
                }
            ])->find($bookId);
            if (!$book)
                throw new BaseApiException('Book not found!', 404);

            $progress = $request->get('progress', 0);
            $reading = ReadingBook::query()
                ->where('book_id', $bookId)
                ->where('user_id', $request->user()->id)
                ->first();

            if (!$request->has('progress') && $reading)
                $progress = $reading->progress;

            if (!$reading) {
                $reading = new ReadingBook();
            }

            $readingData = [
                'book_id' => $bookId,
                'user_id' => $request->user()->id,
                'progress' => $progress
            ];

            $reading->fill($readingData)->save();

            DB::commit();

            BookInfoResource::withoutWrapping();
            return BookInfoResource::make($book);

        } catch (BaseApiException $exception) {
            DB::rollBack();
            return $this->_apiExceptionRepository->throwException($exception->getCode(), $exception->getMessage());

        } catch (Exception $exception) {
            DB::rollBack();
            return response()->json(['message' => 'Fail to update book progress.'], 400);

        }
    }

    public function createBookHighlight(Request $request) {
        try {
            DB::beginTransaction();
            $bookId = $request->get('book_id');
            $book = Book::query()->find($bookId);
            if (!$book)
                throw new BaseApiException('Book not found.', 404);

            $noteData = [
                'highlight_ref' => $request->get('highlight_ref', ''),
                'note' => $request->get('note', ''),
                'quote' => $request->get('quote', ''),
                'short_desc' => $request->get('short_desc', ''),
                'user_id' => $request->user()->id,
                'book_id' => $bookId
            ];

            $hightLight = new Highlight();
            $hightLight->fill($noteData)->save();
            
            DB::commit();
            
            NoteResource::withoutWrapping();
            return NoteResource::make($hightLight);

        } catch (BaseApiException $exception) {
            DB::rollBack();
            return $this->_apiExceptionRepository->throwException($exception->getCode(), $exception->getMessage());

        } catch (Exception $exception) {
            DB::rollBack();
            return $this->_apiExceptionRepository->throwException(400, 'Fail to create highlight' . $exception->getMessage());

        }
    }

    public function updateHighlight(Request $request, $highlightId) {
        try {
            DB::beginTransaction();

            $highlight = Highlight::query()->find($highlightId);
            if (!$highlight)
                throw new BaseApiException('Highlight not found', 404);

            if ($request->user()->cant('update', $highlight))
                throw new BaseApiException('You don\'t have permission to view this highlight', 404);

            $noteData = [
                'highlight_ref' => $request->get('highlight_ref', $highlight->highlight_ref),
                'note' => $request->get('note', $highlight->note),
                'quote' => $request->get('quote', $highlight->quote),
                'short_desc' => $request->get('short_desc', $highlight->short_desc),
            ];

            $highlight->fill($noteData)->update();

            DB::commit();
            
            NoteResource::withoutWrapping();
            return NoteResource::make($highlight);

        } catch (BaseApiException $exception) {
            DB::rollBack();
            return $this->_apiExceptionRepository->throwException($exception->getCode(), $exception->getMessage());

        } catch (Exception $exception) {
            DB::rollBack();
            return $this->_apiExceptionRepository->throwException(400, 'Fail to update highlight');

        }
    }

    public function deleteHighlight(Request $request, $highlightId)
    {
        try {
            DB::beginTransaction();
            $hightLight = Highlight::query()->find($highlightId);
            if (!$hightLight)
                throw new BaseApiException('Highlight not found', 404);

            if ($request->user()->cant('delete', $hightLight))
                throw new BaseApiException('You don\'t have permission to view this highlight', 400);

            $hightLight->delete();

            DB::commit();
            
            NoteResource::withoutWrapping();
            return NoteResource::make($hightLight);

        } catch (BaseApiException $exception) {
            DB::rollBack();
            return $this->_apiExceptionRepository->throwException($exception->getCode(), $exception->getMessage());

        } catch (Exception $exception) {
            DB::rollBack();
            return $this->_apiExceptionRepository->throwException(400, 'Fail to delete highlight');
        }
    }
}
