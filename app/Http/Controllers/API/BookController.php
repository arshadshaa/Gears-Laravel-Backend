<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Http\Resources\BookResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $book = Book::where('author_id', $user->id)->paginate($request->limit);
        return response([ 'books' => $book, 'message' => 'Retrieved successfully'], 200);

    }


     public function allBooks(Request $request)
    {
        $book = Book::with(['author'])->whereHas('author', function($query) {
                                        $query->where('user_status', 1);
                                    })->paginate($request->limit);
        return response([ 'books' => $book, 'message' => 'Retrieved successfully'], 200);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->all();
        $file = $request->book_image;

        $validator = Validator::make($data, [
            'book_tittle' => 'required|max:255',
            'book_detail' => 'required|max:255',
            // 'book_image' => 'required|mimes:png,jpg|max:2048',
        ]);

        if($validator->fails()){
            return response(['error' => $validator->errors(), 'Validation Error']);
        }

       if ($files = $request->file('book_image')) {
        
        //delete old file
        // File::delete('public/product/'.$request->hidden_image);
        
       //insert new file
        $destinationPath = 'public/images'; // upload path
        $coverImage = date('YmdHis') . "." . $files->getClientOriginalExtension();
        $files->move($destinationPath, $coverImage);
        $data['book_image'] = $coverImage;
        }
     
  
        $user = Auth::user();

        $data['author_id'] = $user->id;

        $book = Book::create($data);

        return response([ 'book' => new BookResource($book), 'message' => 'Created successfully'], 200);
    
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Book  $book
     * @return \Illuminate\Http\Response
     */
    public function show(Book $book)
    {
        return response([ 'book' => new BookResource($book), 'message' => 'Retrieved successfully'], 200);
    }

    // public function allBooks(Book $book)
    // {
    //     return response([ 'book' => new BookResource($book), 'message' => 'Retrieved successfully'], 200);
    // }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Book  $book
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Book $book)
    {
        $book->update($request->all());

        return response([ 'book' => new BookResource($book), 'message' => 'Retrieved successfully'], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Book  $book
     * @return \Illuminate\Http\Response
     */
    public function destroy(Book $book)
    {
        $book->delete();
        return response(['message' => 'Deleted']);
    }
}
