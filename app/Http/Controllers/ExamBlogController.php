<?php

namespace App\Http\Controllers;

use App\Models\AcademicClass;
use App\Models\Blog;
use App\Models\board;
use App\Models\Category;
use App\Models\Chapter;
use App\Models\Education;
use App\Models\Institute;
use App\Models\Level;
use App\Models\Question;
use App\Models\Savedquestion;
use App\Models\Subject;
use App\Models\Type;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Illuminate\Support\Str;

class ExamBlogController extends Controller
{

    public function BlogList(){
        $bloglist = Blog::with([
                'category',
                'questions' => function($query) {
                    $query->with([
                        'academicClass',
                        'options',
                        'subject',
                        'lavel'
                        ])
                        ->get();
                }
            ])->orderBy('id', 'desc')->get();

        return Inertia::render('Blog/BlogList', [
            'data' => $bloglist
        ]);
    }
    
   public function blogexamform(Request $request) {
    $education = Education::all();
    $classes = AcademicClass::all();
    $subjects = Subject::all();
    $chapters = Chapter::all();
    $categories = Category::all();

    return Inertia::render('Blog/Index', [
        'education' => $education,
        'classes' => $classes,
        'subjects' => $subjects,
        'chapters' => $chapters,
        'categories' => $categories,
    ]);
}


   // In your controller
public function sltquestionblog(Request $request)
{
    $request->validate([
        'chapter_id' => 'required|array', // Changed to array
        'chapter_id.*' => 'exists:chapters,id', // Validate each chapter ID
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

    // Get chapters (multiple)
    $chapters = Chapter::with('topic')
                       ->whereIn('id', $request->chapter_id)
                       ->get();
    
    $quetype = Type::all();
    $level = Level::all();

    // Build the query
    $query = Question::with(['options', 'cqoptions', 'board', 'lavel', 'type', 'academicClass', 'subject', 'chapter', 'topic'])
        ->whereIn('chapter_id', $request->chapter_id) // Changed to whereIn for array
        ->when($request->topic_id, function($query, $topicId) {
            return $query->whereHas('topic', function($q) use ($topicId) {
                $q->whereIn('topic_id', $topicId);
            });
        })
        ->when($request->type, function($query, $type) {
            if ($type === 'mix') {
                return $query->whereIn('format', ['mcq', 'cq', 'mix']);
            }
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
        });

    // Get total count for distribution
    $totalCount = $request->count ?? 30;
    
    // If multiple chapters, distribute questions evenly or proportionally
    if (count($request->chapter_id) > 1) {
        $chaptersCount = count($request->chapter_id);
        $perChapterCount = ceil($totalCount / $chaptersCount);
        
        $questions = collect();
        
        foreach ($request->chapter_id as $chapterId) {
            $chapterQuestions = (clone $query)
                ->where('chapter_id', $chapterId)
                ->inRandomOrder()
                ->limit($perChapterCount)
                ->get();
            
            $questions = $questions->merge($chapterQuestions);
        }
        
        // Shuffle and limit to exact count
        $questions = $questions->shuffle()->take($totalCount);
    } else {
        // Single chapter - original logic
        $questions = $query->inRandomOrder()->limit($totalCount)->get();
    }

    // Group questions by board name
    $groupedQuestions = $questions->groupBy('board.name');

    // Get available years from boards table
    $availableYears = Board::distinct('year')->orderBy('year', 'desc')->pluck('year');

    return Inertia::render('Blog/BlogForm', [
        'questions' => $questions,
        'groupedQuestions' => $groupedQuestions,
        'chapters' => $chapters, // Changed to plural
        'exam_name' => $request->exam_name,
        'quetype' => $quetype,
        'level' => $level,
        'availableYears' => $availableYears,
        'boards' => Board::all(),
        'categories' => Category::all(),
        'filters' => $request->only([
            'chapter_id', 
            'topic_id', 
            'type', 
            'count', 
            'board_ids', 
            'years',
            'question_types',
            'levels',
            'categories'
        ])
    ]);
}


    public function saveQuestionsBlog(Request $request)
{

// dd($request->all());
    $request->validate([
        'blog_title' => 'required|string|max:255',
        'blog_description' => 'required|string|max:255',
        'blog_category' => 'exists:categories,id',
        'question_ids' => 'required|array',
        'question_ids.*' => 'exists:questions,id'
    ]);

    try {
        // Create saved question
        $savedQuestionblog = Blog::create([
            'title' => $request->blog_title,
            'slug' => Str::slug($request->blog_title),
            'description' => $request->blog_description,
            'category_id' => $request->blog_category,
            'user_id' => Auth::id(),
            
        ]);

        $savedQuestionblog->questions()->attach($request->question_ids);

      return redirect(route('blog.list'))->with('Created Successfully');

    } catch (\Exception $e) {
        
        return response()->json([
            'success' => false,
            'message' => 'Failed to save questions: ' . $e->getMessage(),
        ], 500);
    }
}


    //Question Setting
   public function qstSetting(Request $request, $exam_id = null)
{
    $userId = Auth::id();
    $institute = Institute::where('user_id', $userId)->first(); 
    
    // Check if exam_id is provided in the URL
     $examId = $exam_id ?: $request->query('exam_id');

    // dd($examId);
    
    if ($examId) {
        // Get the specific exam by ID
        $latestExam = Savedquestion::where('user_id', $userId)
            ->where('id', $examId)
            ->with([
                'user:id,name',
                'questions' => function($query) {
                    $query->with([
                        'options',
                        'cqoptions',
                        'academicClass',
                        'subject',
                        'chapter',
                        'lavel'
                    ])
                    ->latest();
                }
            ])
            ->first();
    } else {
        // Fallback to latest exam
        $latestExam = Savedquestion::where('user_id', $userId)
            ->with([
                'user:id,name',
                'questions' => function($query) {
                    $query->with([
                        'options',
                        'cqoptions',
                        'academicClass',
                        'subject',
                        'chapter',
                        'lavel'
                    ])
                    ->latest();
                }
            ])
            ->latest()
            ->first();
    }

    return Inertia::render('UserDashboard/Questions/question-setting', [
        'savedquestion' => $latestExam ? [$latestExam] : [],
        'institute' => $institute
    ]);
}



    //Dashboard
    public function userdashboard()
    {
        $userId = Auth::id();
        $institute = Institute::where('user_id', $userId)->first(); 
        $savedQuestion = Savedquestion::where('user_id', $userId)->with([
                'questions' => function($query) {
                    $query->with([
                        'academicClass',
                        'subject',
                        ])
                        ->get();
                }
            ])->orderBy('id', 'desc')->get();

        return Inertia::render('UserDashboard/Dashboard', [
            'institute' => $institute,
            'savedQuestion' => $savedQuestion
        ]);
    }


public function updateIn(Request $request)
{
    $userId = Auth::id();
    $institute = Institute::where('user_id', $userId)->firstOrFail();
    
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'address' => 'required|string|max:255',
        'phone' => 'required|string|max:255',
        // Add other fields you want to update
    ]);
    
    $institute->update($validated);
    
    return redirect()->route('userdashboard')->with('success', 'Institute updated successfully');
}

    public function qstshow($id){
        
        $userId = Auth::id();
            $institute = Institute::where('user_id', $userId)->first(); 
            $question = Savedquestion::where('user_id', $userId)
                ->with([
                    'user:id,name',
                    'questions' => function($query) {
                        $query->with([
                            'options',
                            'cqoptions',
                            'academicClass',
                            'subject',
                            'chapter',
                            'lavel'
                            ])
                            ->get();
                    }
                ])
                ->findOrFail($id);

            return Inertia::render('UserDashboard/Questions/question-setting', [
                'savedquestion' => $question ? [$question] : [],
                'institute' => $institute,
            ]);
    }

}
