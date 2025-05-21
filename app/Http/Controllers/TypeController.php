<?php

namespace App\Http\Controllers;

use App\Models\Type;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Type::all();
        return Inertia::render('Type/Index',[
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

        Type::create([
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
    public function update(Request $request, Type $Type)
    {
       // dd($request -> all());
       $request -> validate([
        'name'  => 'required|string|max:255',
        
    ]);


    $Type ->update([
        'name' => $request -> name,
        
    ]);

    return redirect() -> back()-> with('message', 'Type Updated successfull');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Type $type)
    {
        $type -> delete();

        return redirect() -> back()-> with('message', 'Type Deleted successfull');
   
    }
}
