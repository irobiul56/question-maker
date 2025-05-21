<?php

namespace App\Http\Controllers;

use App\Models\Education;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EducationLevelController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Education::all();
        return Inertia::render('EducationLevel/Education',[
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

        Education::create([
            'name' => $request -> name,
        ]);

        return redirect() -> back()-> with('message', 'Education Level created successfull');
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
    public function update(Request $request, Education $education)
    {
        // dd($request -> all());
       $request -> validate([
        'name'  => 'required|string|max:255',
        
    ]);


    $education ->update([
        'name' => $request -> name,
        
    ]);

    return redirect() -> back()-> with('message', 'Education Level Updated successfull');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Education $education)
    {
        $education -> delete();

        return redirect() -> back()-> with('message', 'Education Deleted successfull');
   
    }
}
