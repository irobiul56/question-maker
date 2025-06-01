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
    $questions = Question::with(['topic', 'type', 'board', 'options', 'cqoptions'])->get();
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

    // dd($request-> all());

   $validated = $request->validate([
    // Common fields
    'academic_classes_id' => 'required|exists:academic_classes,id',
    'subject_id' => 'required|exists:subjects,id',
    'chapter_id' => 'required|exists:chapters,id',
    'topic_id' => 'required|exists:topics,id',
    'type_id' => 'required|array|min:1',
    'type_id.*' => 'exists:types,id',
    'level_id' => 'required|exists:levels,id',
    'board_id' => 'nullable|exists:boards,id',
    'format' => 'required|in:mcq,cq,mix',
    'question_text' => 'required|string',
    'explanation' => 'nullable|string',

    // MCQ fields (only required if format=mcq)
    'options' => 'nullable|array|required_if:format,mcq|min:2',
    'options.*.option_text' => 'nullable|string|required_if:format,mcq|max:500',
    'options.*.is_correct' => 'nullable|boolean|required_if:format,mcq',

    // CQ fields (only required if format=cq)
    'cq' => 'nullable|array|required_if:format,cq',
    'cq.*.cq_text' => 'nullable|string|required_if:format,cq|max:500',
    'cq.*.mark' => 'nullable|numeric|required_if:format,cq|min:0',

    // Mix fields (only required if format=mix)
    'mark' => 'nullable|numeric|required_if:format,mix|min:0',
]);

    // Create the question
    $question = Question::create([
        'academic_classes_id' => $validated['academic_classes_id'],
        'subject_id' => $validated['subject_id'],
        'chapter_id' => $validated['chapter_id'],
        'topic_id' => $validated['topic_id'],
        'level_id' => $validated['level_id'],
        'board_id' => $validated['board_id'],
        'format' => $validated['format'],
        'question_text' => $validated['question_text'],
        'explanation' => $validated['explanation'] ?? null,
        'mark' => $validated['mark'] ?? null,
    ]);

    $question->type()->attach($validated['type_id']);

    // Create options if format is MCQ
    if (in_array($validated['format'], ['mcq'])) {
        foreach ($validated['options'] as $option) {
            $question->options()->create([
                'option_text' => $option['option_text'],
                'is_correct' => $option['is_correct'],
            ]);
        }
    }


    // Create options if format is CQ
    if (in_array($validated['format'], ['cq'])) {
        foreach ($validated['cq'] as $cqs) {
            $question->cqoptions()->create([
                'cq_text' => $cqs['cq_text'],
                'mark' => $cqs['mark'],
            ]);
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
         // Common fields
    'topic_id' => 'required|exists:topics,id',
    'type_id' => 'required|array|min:1',
    'type_id.*' => 'exists:types,id',
    'level_id' => 'required|exists:levels,id',
    'board_id' => 'nullable|exists:boards,id',
    'format' => 'required|in:mcq,cq,mix',
    'question_text' => 'required|string|max:1000',
    'explanation' => 'nullable|string|max:2000',

    // MCQ fields (only required if format=mcq)
    'options' => 'nullable|array|required_if:format,mcq|min:2',
    'options.*.option_text' => 'nullable|string|required_if:format,mcq|max:500',
    'options.*.is_correct' => 'nullable|boolean|required_if:format,mcq',

    // CQ fields (only required if format=cq)
    'cq' => 'nullable|array|required_if:format,cq',
    'cq.*.cq_text' => 'nullable|string|required_if:format,cq|max:500',
    'cq.*.mark' => 'nullable|numeric|required_if:format,cq|min:0',

    // Mix fields (only required if format=mix)
    'mark' => 'nullable|numeric|required_if:format,mix|min:0',
    ]);

    $question -> update([
        'topic_id' => $request -> topic_id,
        'level_id' => $request -> level_id,
        'board_id' => $request -> board_id,
        'format' => $request -> format,
        'question_text' => $request -> question_text,
        'explanation' => $request -> explanation,
        'mark' => $request -> mark,
    ]);

      // Sync relationships if needed
    if ($request->has('type_id')) {
        $question->type()->sync($request->type_id);
    }

     if (in_array($validated['format'], ['mcq'])) {
            // First delete all existing options
            $question->options()->delete();
            
            // Then create new ones
            foreach ($validated['options'] as $option) {
                $question->options()->create([
                    'option_text' => $option['option_text'],
                    'is_correct' => $option['is_correct']
                ]);
            }
        } else {
            // For CQ format, remove any existing options
            $question->options()->delete();
        }

         if (in_array($validated['format'], ['cq'])) {
            // First delete all existing options
            $question->cqoptions()->delete();
            
            // Then create new ones
            foreach ($validated['cq'] as $cqoption) {
                $question->cqoptions()->create([
                    'cq_text' => $cqoption['cq_text'],
                    'mark' => $cqoption['mark']
                ]);
            }
        } else {
            // For CQ format, remove any existing options
            $question->cqoptions()->delete();
        }

     return redirect()->route('question.index')
        ->with('flash', ['message' => 'Question updated successfully!']);
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
