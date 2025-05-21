<?php

namespace App\Http\Controllers;

use App\Models\AcademicClass;
use App\Models\Education;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ClassController extends Controller
{
    public function index()
    {
        $data = AcademicClass::with('education')->get();
        return Inertia::render('Class/Index', [
            'data' => $data,
        ]);
    }

    public function create()
    {
        $education = Education::all();
        return Inertia::render('Class/Form', [
            'education' => $education,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'education_id' => 'required|exists:education,id',
            'classs' => 'required|array',
            'classs.*.name' => 'required|string',
        ]);

        foreach ($request->classs as $classdata) {
            AcademicClass::create([
                'education_id' => $request->education_id,
                'name' => $classdata['name'],
            ]);
        }

        return redirect()->route('class.index')->with('success', 'Class created successfully.');
    }

    public function edit(string $id)
    {
        $data = Education::with('classes')->findOrFail($id);

        return Inertia::render('Class/EditForm', [
            'education' => $data,
            'classs' => $data->classes()->select('id', 'name')->get(),
        ]);
    }

    public function update(Request $request, Education $education)
    {
        $validated = $request->validate([
            'classs' => 'required|array|min:1',
            'classs.*.id' => 'nullable|exists:academic_classes,id',
            'classs.*.name' => 'required|string|max:10',
        ]);

        $existingClassIds = $education->classes()->pluck('id')->toArray();
        $newClassIds = [];

        foreach ($validated['classs'] as $classData) {
            if (!empty($classData['id'])) {
                $class = $education->classes()->where('id', $classData['id'])->first();
                if ($class) {
                    $class->update([
                        'name' => $classData['name'],
                    ]);
                    $newClassIds[] = $class->id;
                }
            } else {
                $newClass = $education->classes()->create([
                    'name' => $classData['name'],
                ]);
                $newClassIds[] = $newClass->id;
            }
        }

        $toDelete = array_diff($existingClassIds, $newClassIds);
        AcademicClass::destroy($toDelete);

        return redirect()->route('class.index')->with('success', 'Class updated successfully!');
    }
}
