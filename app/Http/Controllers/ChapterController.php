<?php

namespace App\Http\Controllers;

use App\Models\AcademicClass;
use App\Models\Chapter;
use App\Models\Education;
use App\Models\Subject;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ChapterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
{
    $chapters = Chapter::with('subject')->get();
    $education = Education::all();
    $classes = AcademicClass::all();
    $subjects = Subject::all(); // Make sure to include this
    
    return Inertia::render('Chapter/Index', [
        'data' => $chapters,
        'education' => $education,
        'classes' => $classes,
        'subjects' => $subjects, // Pass subjects to frontend
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
            'subject_id'  => 'required',
        ]);

        Chapter::create([
            'name' => $request -> name,
            'subject_id' => $request -> subject_id,
        ]);

        return redirect() -> back()-> with('message', 'Chapter created successfull');
   
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
    public function update(Request $request, Chapter $chapter)
    {
        // dd($request -> all());
       $request -> validate([
        'name'  => 'required|string|max:255',
        'subject_id'  => 'required',
            
        ]);


        $chapter ->update([
            'name' => $request -> name,
            // 'subject_id' => $request -> subject_id,
            
        ]);

        return redirect() -> back()-> with('message', 'Chapter Updated successfull');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Chapter $chapter)
    {
        $chapter -> delete();

        return redirect() -> back()-> with('message', 'Chapter Deleted successfull');
   
    }
}
