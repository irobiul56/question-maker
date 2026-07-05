<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Institute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class AcademicYearController extends Controller
{
    /**
     * Display a listing of academic years.
     */
    public function index()
    {
        $instituteId = Auth::user()->institute_id;
        
        $academicYears = AcademicYear::forInstitute($instituteId)
            ->orderBy('year', 'desc')
            ->orderBy('start_date', 'desc')
            ->get();

        $currentYear = AcademicYear::forInstitute($instituteId)
            ->current()
            ->first();

        $stats = [
            'total' => $academicYears->count(),
            'current' => $currentYear ? 1 : 0,
            'inactive' => $academicYears->where('is_current', false)->count()
        ];

        return Inertia::render('AcademicYears/Index', [
            'academicYears' => $academicYears,
            'currentYear' => $currentYear,
            'stats' => $stats
        ]);
    }

    /**
     * Show the form for creating a new academic year.
     */
    public function create()
    {
        return Inertia::render('AcademicYears/Create');
    }

    /**
     * Store a newly created academic year in storage.
     */
    public function store(Request $request)
    {
        $instituteId = Auth::user()->institute_id;

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'year' => 'required|integer|min:2000|max:2100',
            'session' => 'required|string|max:20',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_current' => 'boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // If setting as current, unset other current academic years
        if ($request->is_current) {
            AcademicYear::forInstitute($instituteId)
                ->where('is_current', true)
                ->update(['is_current' => false]);
        }

        $academicYear = AcademicYear::create([
            'institute_id' => $instituteId,
            'name' => $request->name,
            'year' => $request->year,
            'session' => $request->session,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'is_current' => $request->is_current ?? false
        ]);

        return redirect()->route('academic-years.index')
            ->with('success', 'Academic year created successfully.');
    }

    /**
     * Display the specified academic year.
     */
    public function show(AcademicYear $academicYear)
    {

        $academicYear->load(['instituteClasses', 'exams']);

        $stats = [
            'total_classes' => $academicYear->instituteClasses->count(),
            'total_exams' => $academicYear->exams->count(),
            'duration_days' => $academicYear->duration_in_days
        ];

        return Inertia::render('AcademicYears/Show', [
            'academicYear' => $academicYear,
            'stats' => $stats
        ]);
    }

    /**
     * Show the form for editing the specified academic year.
     */
    public function edit(AcademicYear $academicYear)
    {
       
        return Inertia::render('AcademicYears/Edit', [
            'academicYear' => $academicYear
        ]);
    }

    /**
     * Update the specified academic year in storage.
     */
    public function update(Request $request, AcademicYear $academicYear)
    {

        $instituteId = Auth::user()->institute_id;

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'year' => 'required|integer|min:2000|max:2100',
            'session' => 'required|string|max:20',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_current' => 'boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // If setting as current, unset other current academic years
        if ($request->is_current) {
            AcademicYear::forInstitute($instituteId)
                ->where('id', '!=', $academicYear->id)
                ->where('is_current', true)
                ->update(['is_current' => false]);
        }

        $academicYear->update([
            'name' => $request->name,
            'year' => $request->year,
            'session' => $request->session,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'is_current' => $request->is_current ?? false
        ]);

        return redirect()->route('academic-years.index')
            ->with('success', 'Academic year updated successfully.');
    }

    /**
     * Remove the specified academic year from storage.
     */
    public function destroy(AcademicYear $academicYear)
    {

        // Check if academic year has related records
        if ($academicYear->instituteClasses()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Cannot delete academic year. It has associated classes.');
        }

        if ($academicYear->exams()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Cannot delete academic year. It has associated exams.');
        }

        $academicYear->delete();

        return redirect()->route('academic-years.index')
            ->with('success', 'Academic year deleted successfully.');
    }

    /**
     * Set an academic year as current.
     */
    public function setCurrent(AcademicYear $academicYear)
    {

        $instituteId = Auth::user()->institute_id;

        // Unset all current academic years
        AcademicYear::forInstitute($instituteId)
            ->where('is_current', true)
            ->update(['is_current' => false]);

        // Set this as current
        $academicYear->update(['is_current' => true]);

        return redirect()->back()
            ->with('success', 'Academic year set as current successfully.');
    }

    /**
     * Get the current academic year.
     */
    public function getCurrent()
    {
        $instituteId = Auth::user()->institute_id;

        $currentYear = AcademicYear::forInstitute($instituteId)
            ->current()
            ->first();

        return response()->json([
            'success' => true,
            'data' => $currentYear
        ]);
    }

    /**
     * Get all academic years for dropdown.
     */
    public function getYears()
    {
        $instituteId = Auth::user()->institute_id;

        $years = AcademicYear::forInstitute($instituteId)
            ->orderBy('year', 'desc')
            ->get(['id', 'name', 'session', 'year', 'is_current']);

        return response()->json([
            'success' => true,
            'data' => $years
        ]);
    }
}