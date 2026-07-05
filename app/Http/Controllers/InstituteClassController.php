<?php

namespace App\Http\Controllers;

use App\Models\Classes;
use App\Models\InstituteClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class InstituteClassController extends Controller
{
    /**
     * Display a listing of the resource.
     */
   public function index()
    {
        $instituteId = Auth::user()->institute_id;

        $classes = InstituteClass::forInstitute($instituteId)
            ->with(['students', 'sections'])
            ->ordered()
            ->get()
            ->map(function($class) {
                return [
                    'id' => $class->id,
                    'name' => $class->name,
                    'bn_name' => $class->bn_name,
                    'numeric_value' => $class->numeric_value,
                    'group' => $class->group,
                    'group_label' => $class->group_label,
                    'group_badge_class' => $class->group_badge_class,
                    'display_order' => $class->display_order,
                    'has_elective' => $class->has_elective,
                    'student_count' => $class->student_count,
                    'active_student_count' => $class->active_student_count,
                    'section_count' => $class->section_count,
                    'class_type' => $class->class_type,
                    'class_type_badge_class' => $class->class_type_badge_class,
                ];
            });

        $stats = [
            'total' => $classes->count(),
            'with_science' => $classes->where('group', 'science')->count(),
            'with_commerce' => $classes->where('group', 'commerce')->count(),
            'with_arts' => $classes->where('group', 'arts')->count(),
            'total_students' => $classes->sum('student_count'),
            'active_students' => $classes->sum('active_student_count'),
            'total_sections' => $classes->sum('section_count'),
        ];

        return Inertia::render('Institute/Classes/Index', [
            'classes' => $classes,
            'stats' => $stats
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $instituteId = Auth::user()->institute_id;

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'bn_name' => 'nullable|string|max:255',
            'numeric_value' => 'required|integer|min:1|max:12',
            'group' => 'nullable|in:science,commerce,arts,general',
            'display_order' => 'nullable|integer|min:0',
            'has_elective' => 'boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Check for duplicate (same name and numeric value for the same institute)
        $exists = InstituteClass::forInstitute($instituteId)
            ->where('name', $request->name)
            ->where('numeric_value', $request->numeric_value)
            ->exists();

        if ($exists) {
            return redirect()->back()
                ->with('error', 'This class already exists for your institute.')
                ->withInput();
        }

        $class = InstituteClass::create([
            'institute_id' => $instituteId,
            'name' => $request->name,
            'bn_name' => $request->bn_name,
            'numeric_value' => $request->numeric_value,
            'group' => $request->group,
            'display_order' => $request->display_order ?? 0,
            'has_elective' => $request->has_elective ?? false
        ]);

        return redirect()->route('classes.index')
            ->with('success', 'Class created successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, InstituteClass $class)
    {
        $instituteId = Auth::user()->institute_id;

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'bn_name' => 'nullable|string|max:255',
            'numeric_value' => 'required|integer|min:1|max:12',
            'group' => 'nullable|in:science,commerce,arts,general',
            'display_order' => 'nullable|integer|min:0',
            'has_elective' => 'boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Check for duplicate (excluding current)
        $exists = InstituteClass::forInstitute($instituteId)
            ->where('id', '!=', $class->id)
            ->where('name', $request->name)
            ->where('numeric_value', $request->numeric_value)
            ->exists();

        if ($exists) {
            return redirect()->back()
                ->with('error', 'This class already exists for your institute.')
                ->withInput();
        }

        $class->update([
            'name' => $request->name,
            'bn_name' => $request->bn_name,
            'numeric_value' => $request->numeric_value,
            'group' => $request->group,
            'display_order' => $request->display_order ?? 0,
            'has_elective' => $request->has_elective ?? false
        ]);

        return redirect()->route('classes.index')
            ->with('success', 'Class updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(InstituteClass $class)
    {
        // Check if class has students
        if ($class->students()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Cannot delete this class. It has ' . $class->students()->count() . ' enrolled students.');
        }

        // Check if class has sections
        if ($class->sections()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Cannot delete this class. It has ' . $class->sections()->count() . ' sections.');
        }

        // Check if class has subjects
        if ($class->subjects()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Cannot delete this class. It has subjects assigned.');
        }

        // Check if class has exams
        if ($class->exams()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Cannot delete this class. It has exams associated.');
        }

        $class->delete();

        return redirect()->route('classes.index')
            ->with('success', 'Class deleted successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(InstituteClass $class)
    {
        $class->load(['sections', 'students', 'subjects', 'teachers']);

        return Inertia::render('Classes/Show', [
            'class' => [
                'id' => $class->id,
                'name' => $class->name,
                'bn_name' => $class->bn_name,
                'numeric_value' => $class->numeric_value,
                'group' => $class->group,
                'group_label' => $class->group_label,
                'has_elective' => $class->has_elective,
                'student_count' => $class->student_count,
                'section_count' => $class->section_count,
                'sections' => $class->sections,
                'students' => $class->students->take(10),
                'subjects' => $class->subjects,
                'teachers' => $class->teachers
            ]
        ]);
    }

    /**
     * Get classes for dropdown (API).
     */
    public function getClasses(Request $request)
    {
        $instituteId = Auth::user()->institute_id;
        
        $query = InstituteClass::forInstitute($instituteId);

        if ($request->has('group') && $request->group) {
            $query->where('group', $request->group);
        }

        if ($request->has('has_elective')) {
            $query->where('has_elective', $request->has_elective);
        }

        $classes = $query->ordered()->get(['id', 'name', 'numeric_value', 'group']);

        return response()->json([
            'success' => true,
            'data' => $classes
        ]);
    }

    /**
     * Get class sections (API).
     */
    public function getSections($classId)
    {
        $class = InstituteClass::with('sections')->findOrFail($classId);

        return response()->json([
            'success' => true,
            'data' => $class->sections
        ]);
    }

    /**
     * Get class statistics (API).
     */
    public function getStats()
    {
        $instituteId = Auth::user()->institute_id;

        $classes = InstituteClass::forInstitute($instituteId)->get();

        return response()->json([
            'success' => true,
            'data' => [
                'total' => $classes->count(),
                'with_science' => $classes->where('group', 'science')->count(),
                'with_commerce' => $classes->where('group', 'commerce')->count(),
                'with_arts' => $classes->where('group', 'arts')->count(),
                'total_students' => $classes->sum(function($class) {
                    return $class->students()->count();
                })
            ]
        ]);
    }
}