<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\BookResource;
use App\Models\Book;

class BookController extends Controller
{
    public function show(Request $request, $id) {
        BookResource::withoutWrapping();
        $book = Book::query()->with(['authors', 'metas.type', 'summaries'])->find($id);
        return new BookResource($book);
    }
}
