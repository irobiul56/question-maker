<?php

namespace App\Http\Controllers;

use App\Models\AcademicClass;
use App\Models\Education;
use App\Models\Subject;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SubjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $subjects = Subject::with('classs')->get();
        $classes = AcademicClass::all(); // Get all classes
        
        return Inertia::render('Subject/Index', [
            'data' => $subjects,
            'classs' => $classes, // Note: I'm keeping your naming convention here
        ]);
    }


    public function getClassesByEducation($id)
    {
        $classes = AcademicClass::where('education_id', $id)->get();
        return response()->json($classes);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $education = Education::all();
        $classes = AcademicClass::all();
        return Inertia::render('Subject/Form', [
            'education' => $education,
            'classes' => $classes,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request -> all());

        $request->validate([
            'class_id' => 'required|exists:academic_classes,id',
            'subject' => 'required|array',
            'subject.*.name' => 'required|string',
        ]);

        foreach ($request->subject as $subjectdata) {
            Subject::create([
                'academic_classes_id' => $request->class_id,
                'name' => $subjectdata['name'],
            ]);
        }

        return redirect()->route('subject.index')->with('success', 'Subject created successfully.');
   
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
    public function update(Request $request, Subject $subject)
    {
        //  dd($request -> all());
        $request -> validate([
            'name'  => 'required|string|max:255',
            
        ]);


        $subject ->update([
            'name' => $request -> name,
            
        ]);

        return redirect() -> back()-> with('message', 'Subject Updated successfull');
   
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Subject $subject)
    {
        $subject -> delete();

        return redirect() -> back()-> with('message', 'Subject Deleted successfull');
   
    }
}
