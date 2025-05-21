<?php

namespace App\Http\Controllers;

use App\Models\board;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BoardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = board::all();
        return Inertia::render('Board/Index',[
            'data'  => $data
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request -> validate([
            'name'  => 'required|string|max:255',
            'year'  => 'required'
        ]);

        board::create([
            'name' => $request -> name,
            'year' => $request -> year
        ]);

        return redirect() -> back()-> with('message', 'Board created successfull');
   
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, board $board)
    {
        // dd($request -> all());
        $request -> validate([
            'name'  => 'required|string|max:255',
            'year'  => 'required',
        ]);


        $board ->update([
            'name' => $request -> name,
            'year' => $request -> year,
        ]);

        return redirect() -> back()-> with('message', 'Board Updated successfull');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(board $board)
    {
        $board -> delete();

        return redirect() -> back()-> with('message', 'Board Deleted successfull');
    }
}
