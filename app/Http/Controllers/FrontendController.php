<?php

namespace App\Http\Controllers;

use App\Models\AcademicClass;
use App\Models\board;
use App\Models\Chapter;
use App\Models\Education;
use App\Models\Level;
use App\Models\Question;
use App\Models\Savedquestion;
use App\Models\Subject;
use App\Models\Type;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class FrontendController extends Controller
{
    
   public function qstIndex(Request $request) {
    $education = Education::all();
    $classes = AcademicClass::all();
    $subjects = Subject::all();
    $chapters = Chapter::all();

    return Inertia::render('UserDashboard/Questions/Index', [
        'education' => $education,
        'classes' => $classes,
        'subjects' => $subjects,
        'chapters' => $chapters,
    ]);
}


   public function sltquestion(Request $request)
    {
     
    
        $request->validate([
            'chapter_id' => 'required|exists:chapters,id',
            'topic_id' => 'nullable|array',
            'exam_name' => 'nullable',
            'topic_id.*' => 'exists:topics,id',
            'type' => 'nullable|in:mcq,cq,mix',
            'count' => 'nullable|integer|min:1|max:100',
            'board_ids' => 'nullable|array',
            'board_ids.*' => 'exists:boards,id',
            'years' => 'nullable|array',
            'years.*' => 'integer|min:2000|max:2099',
            'question_types' => 'nullable|array',
            'question_types.*' => 'exists:types,id',
            'levels' => 'nullable|array',
            'levels.*' => 'exists:levels,id',
        ]);

        $chapter = Chapter::with('topic')->find($request->chapter_id);
        $quetype = Type::all();
        $level = Level::all();

        $questions = Question::with(['options', 'board', 'lavel', 'type', 'academicClass', 'subject', 'chapter', 'topic'])
            ->where('chapter_id', $request->chapter_id)
            ->when($request->topic_id, function($query, $topicId) {
                return $query->whereHas('topic', function($q) use ($topicId) {
                    $q->whereIn('topic_id', $topicId);
                });
            })
            ->when($request->type, function($query, $type) {
                return $query->where('format', $type);
            })
            ->when($request->board_ids, function($query, $boardIds) {
                return $query->whereIn('board_id', $boardIds);
            })
            ->when($request->years, function($query, $years) {
                return $query->whereHas('board', function($q) use ($years) {
                    $q->whereIn('year', $years);
                });
            })
            ->when($request->question_types, function($query, $questionTypes) {
                return $query->whereHas('type', function($q) use ($questionTypes) {
                    $q->whereIn('types.id', $questionTypes);
                });
            })
            ->when($request->levels, function($query, $levels) {
                return $query->whereIn('level_id', $levels);
            })
            ->inRandomOrder()
            ->limit($request->count ?? 30)
            ->get();

        // Group questions by board name
        $groupedQuestions = $questions->groupBy('board.name');

        // Get available years from boards table
        $availableYears = Board::distinct('year')->orderBy('year', 'desc')->pluck('year');

        return Inertia::render('UserDashboard/Questions/SelectedQuestion', [
            'questions' => $questions,
            'groupedQuestions' => $groupedQuestions,
            'chapter' => $chapter,
            'exam_name' => $request -> exam_name,
            'quetype' => $quetype,
            'level' => $level, // Fixed: removed $ prefix
            'availableYears' => $availableYears,
            'boards' => Board::all(),
            'filters' => $request->only([
                'chapter_id', 
                'topic_id', 
                'type', 
                'count', 
                'board_ids', 
                'years',
                'question_types',
                'levels'
            ])
        ]);
    }


    public function saveQuestions(Request $request)
    {
        // dd(Auth::id());
        $request->validate([
            'exam_name' => 'required|string|max:255',
            'question_ids' => 'required|array',
            'question_ids.*' => 'exists:questions,id'
        ]);

        try {
            DB::transaction(function () use ($request) {
                // Create or find existing saved question set
                $savedQuestion = Savedquestion::firstOrCreate([
                    'exam_name' => $request->exam_name,
                    'user_id' => Auth::id(),
                ]);

                // Attach questions (will avoid duplicates automatically)
                $savedQuestion->questions()->syncWithoutDetaching($request->question_ids);
            });

            return redirect(route('qstSetting'))->with('success','Questions Saved Succefully');

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to save questions: ' . $e->getMessage(),
            ], 500);
        }
    }


    //Question Setting
    public function qstSetting()
    {
        $userId = Auth::id();
        
        $latestExam = Savedquestion::where('user_id', $userId)
            ->with([
                'user:id,name',
                'questions' => function($query) {
                    $query->with([
                        'options',
                        'academicClass',
                        'subject',
                        'chapter'
                        ])
                        ->latest();
                }
            ])
            ->latest()
            ->first();
        // dd($latestExam);

        return Inertia::render('UserDashboard/Questions/QuestionSetting', [
            'savedquestion' => $latestExam ? [$latestExam] : [] // Wrap single item in array
        ]);
    }
}
