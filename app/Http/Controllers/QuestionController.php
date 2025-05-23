<?php

namespace App\Http\Controllers;

use App\Models\AcademicClass;
use App\Models\board;
use App\Models\Chapter;
use App\Models\Education;
use App\Models\Level;
use App\Models\Question;
use App\Models\Subject;
use App\Models\Topic;
use App\Models\Type;
use Illuminate\Http\Request;
use Inertia\Inertia;

class QuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
{
    $questions = Question::with(['topic', 'type', 'board', 'options'])->get();
    $topics = Topic::all();
    $chapters = Chapter::all();
    $education = Education::all();
    $classes = AcademicClass::all();
    $subjects = Subject::all();
    $levels = Level::all();
    $types = Type::all();
    $boards = Board::all();
    
    return Inertia::render('Question/Index', [
        'topics' => $topics,
        'chapters' => $chapters,
        'education' => $education,
        'classes' => $classes,
        'questions' => $questions,
        'subjects' => $subjects,
        'levels' => $levels,
        'types' => $types,
        'boards' => $boards,
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
    // dd($request -> all());
    $validated = $request->validate([
        'topic_id' => 'required|exists:topics,id',
        'type_id' => 'required|exists:types,id',
        'level_id' => 'required|exists:levels,id',
        'board_id' => 'required|exists:boards,id',
        'format' => 'required|in:mcq,cq,mix',
        'question_text' => 'required|string',
        'explanation' => 'nullable|string',
        'options' => 'required_if:format,mcq|array',
        'options.*.option_text' => 'required_if:format,mcq|string',
        'options.*.is_correct' => 'required_if:format,mcq|boolean',
    ]);

    $question = Question::create([
        'topic_id' => $request -> topic_id,
        'type_id' => $request-> type_id,
        'level_id' => $request -> level_id,
        'board_id' => $request -> board_id,
        'format' => $request -> format,
        'question_text' => $request -> question_text,
        'explanation' => $request -> explanation,
    ]);

    if (in_array($validated['format'], ['mcq'])) {
        foreach ($validated['options'] as $option) {
            $question->options()->create($option);
        }
    }

    return redirect()->back()->with('message', 'Question created successfully!');
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
    public function update(Request $request, Question $question)
    {
        // dd($request -> all());
    $validated = $request->validate([
        'topic_id' => 'required|exists:topics,id',
        'type_id' => 'required|exists:types,id',
        'level_id' => 'required|exists:levels,id',
        'board_id' => 'required|exists:boards,id',
        'format' => 'required|in:mcq,cq,mix',
        'question_text' => 'required|string',
        'explanation' => 'nullable|string',
        'options' => 'required_if:format,mcq|array',
        'options.*.option_text' => 'required_if:format,mcq|string',
        'options.*.is_correct' => 'required_if:format,mcq|boolean',
    ]);

    $question -> update([
        'topic_id' => $request -> topic_id,
        'type_id' => $request-> type_id,
        'level_id' => $request -> level_id,
        'board_id' => $request -> board_id,
        'format' => $request -> format,
        'question_text' => $request -> question_text,
        'explanation' => $request -> explanation,
    ]);

    if (in_array($validated['format'], ['mcq'])) {
        foreach ($validated['options'] as $option) {
            $question->options()->update($option);
        }
    }

    return redirect()->back()->with('message', 'Update created successfully!');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Question $question)
    {
        // Delete associated options if needed
        if ($question->options()->exists()) {
            $question->options()->delete();
        }
            
        // Delete the question
        $question->delete();
    }
}
