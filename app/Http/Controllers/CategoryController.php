<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Category::all();
        return Inertia::render('Category/Index',[
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

        Category::create([
            'name' => $request -> name,
            
        ]);

        return redirect() -> back()-> with('message', 'Category created successfull');
   
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
    public function update(Request $request, Category $category)
    {
        // dd($request -> all());
        $request -> validate([
            'name'  => 'required|string|max:255',
            
        ]);


        $category ->update([
            'name' => $request -> name,
            
        ]);

        return redirect() -> back()-> with('message', 'Category Updated successfull');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        $category -> delete();

        return redirect() -> back()-> with('message', 'Category Deleted successfull');
    }
}
