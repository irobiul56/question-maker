<?php

namespace App\Http\Controllers;

use App\Models\AcademicClass;
use App\Models\Chapter;
use App\Models\Education;
use App\Models\Subject;
use App\Models\Topic;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TopicController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
    $topics = Topic::with('chapter')->get();
    $chapters = Chapter::all();
    $education = Education::all();
    $classes = AcademicClass::all();
    $subjects = Subject::all(); // Make sure to include this
    
    return Inertia::render('Topic/Index', [
        'topics' => $topics,
        'chapters' => $chapters,
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
        // dd($request ->all());
         $request -> validate([
            'name'  => 'required|string|max:255',
            'chapter_id'  => 'required',
        ]);

        Topic::create([
            'name' => $request -> name,
            'chapter_id' => $request -> chapter_id,
        ]);

        return redirect() -> back()-> with('message', 'Topic created successfull');
   
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
    public function update(Request $request, Topic $topic)
    {
        // dd($request -> all());
       $request -> validate([
        'name'  => 'required|string|max:255',
        'chapter_id'  => 'required',
            
        ]);


        $topic ->update([
            'name' => $request -> name,
            // 'chapter_id' => $request -> chapter_id,
            
        ]);

        return redirect() -> back()-> with('message', 'Topic Updated successfull');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Topic $topic)
    {
         $topic -> delete();

        return redirect() -> back()-> with('message', 'Topic Deleted successfull');
   
    }
}
