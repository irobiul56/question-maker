<?php

namespace App\Http\Controllers;

use App\Models\Level;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LevelController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Level::all();
        return Inertia::render('Level/Index',[
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
        ]);

        Level::create([
            'name' => $request -> name,
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
    public function update(Request $request, Level $level)
    {
        // dd($request -> all());
        $request -> validate([
            'name'  => 'required|string|max:255',
            
        ]);


        $level ->update([
            'name' => $request -> name,
            
        ]);

        return redirect() -> back()-> with('message', 'Level Updated successfull');
   
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Level $level)
    {
        $level -> delete();

        return redirect() -> back()-> with('message', 'Level Deleted successfull');
    }
}
