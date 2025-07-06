<?php

namespace App\Http\Controllers;

use App\Models\Savedquestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class MyQuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $userId = Auth::id();
        $savedQuestion = Savedquestion::where('user_id', $userId)->with([
                'questions' => function($query) {
                    $query->with([
                        'academicClass',
                        'subject',
                        ])
                        ->get();
                }
            ])->orderBy('id', 'desc')->get();

        return Inertia::render('UserDashboard/Questions/MyQuestion/Index', [
            'data' => $savedQuestion
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
        //
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
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
   public function destroy($id) // Temporarily remove model binding for testing
    {
        try {
            $question = SavedQuestion::findOrFail($id);
            $question->delete();
            
            return redirect()->back()->with([
                'message' => 'Deleted successfully',
                'type' => 'success'
            ]);
            
        } catch (\Exception $e) {
            return redirect()->back()->with([
                'message' => 'Delete failed: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }
}
