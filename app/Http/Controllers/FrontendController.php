<?php

namespace App\Http\Controllers;

use App\Models\AcademicClass;
use App\Models\board;
use App\Models\Chapter;
use App\Models\Education;
use App\Models\Question;
use App\Models\Subject;
use App\Models\Type;
use Illuminate\Http\Request;
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
        'topic_id.*' => 'exists:topics,id',
        'type' => 'nullable|in:mcq,cq,mix',
        'count' => 'nullable|integer|min:1|max:100',
        'board_ids' => 'nullable|array',
        'board_ids.*' => 'exists:boards,id',
        'years' => 'nullable|array',
        'years.*' => 'integer|min:2000|max:2099',
    ]);

    $chapter = Chapter::with('topic')->find($request->chapter_id);

    $quetype = Type::all();

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
        'quetype' => $quetype,
        'availableYears' => $availableYears,
        'boards' => Board::all(),
        'filters' => $request->only(['chapter_id', 'topic_id', 'type', 'count', 'board_ids', 'years'])
    ]);
}
}
