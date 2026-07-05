<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Exam;
use App\Models\Result;
use App\Models\Mark;
use App\Models\Attendance;
use App\Models\Grade;
use App\Models\Classes;
use App\Models\Subject;
use App\Models\AcademicYear;
use App\Models\InstituteClass;
use App\Models\InstituteSubject;
use App\Models\StudentAcademicInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Inertia\Inertia;

class DashboardController extends Controller
{
    /**
     * Display the dashboard.
     */
    public function index()
    {
        // Get current academic year
        $currentAcademicYear = AcademicYear::where('is_current', true)->first();
        
        // Get all stats
        $stats = $this->getStats();
        
        // Get chart data
        $chartData = $this->getChartData();
        
        // Get recent activities
        $recentActivities = $this->getRecentActivities();
        
        // Get upcoming exams
        $upcomingExams = $this->getUpcomingExams();
        
        // Get recent results
        $recentResults = $this->getRecentResults();
        
        // Get grade distribution
        $gradeDistribution = $this->getGradeDistribution();
        
        // Get attendance summary
        $attendanceSummary = $this->getAttendanceSummary();
        
        // Get subject performance
        $subjectPerformance = $this->getSubjectPerformance();
        
        // Get quick stats for cards
        $quickStats = $this->getQuickStats();
        
        // Get notifications
        $notifications = $this->getNotifications();
        
        // Get system status
        $systemStatus = $this->getSystemStatus();
        
        // Get top performing students
        $topPerformers = $this->getTopPerformers();
        
        // Get class-wise student count
        // $classWiseCount = $this->getClassWiseStudentCount();

        return Inertia::render('Institute/Dashboard', [
            'stats' => $stats,
            'chartData' => $chartData,
            'recentActivities' => $recentActivities,
            'upcomingExams' => $upcomingExams,
            'recentResults' => $recentResults,
            'gradeDistribution' => $gradeDistribution,
            'attendanceSummary' => $attendanceSummary,
            'subjectPerformance' => $subjectPerformance,
            'quickStats' => $quickStats,
            'notifications' => $notifications,
            'systemStatus' => $systemStatus,
            'topPerformers' => $topPerformers,
            // 'classWiseCount' => $classWiseCount,
            'currentAcademicYear' => $currentAcademicYear,
        ]);
    }

    /**
     * Get main statistics for the dashboard.
     */
    private function getStats()
    {
        $currentYear = AcademicYear::where('is_current', true)->first();
        $currentYearId = $currentYear ? $currentYear->id : null;

        return [
            'total_students' => Student::count(),
            'total_teachers' => Teacher::count(),
            'total_classes' => InstituteClass::count(),
            'total_subjects' => InstituteSubject::count(),
            'total_exams' => Exam::count(),
            'total_results' => Result::count(),
            
            'active_students' => Student::where('status', 'active')->count(),
            'passed_students' => Student::where('status', 'passed')->count(),
            
            'current_year_students' => $currentYearId ? 
                StudentAcademicInfo::where('academic_year_id', $currentYearId)->distinct('student_id')->count() : 0,
            
            'this_month_students' => Student::whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->count(),
            
            'today_attendance' => Attendance::whereDate('date', Carbon::today())->count(),
            'present_today' => Attendance::whereDate('date', Carbon::today())
                ->where('status', 'present')
                ->count(),
        ];
    }

    /**
     * Get chart data for performance overview.
     */
    private function getChartData()
    {
        // Subject-wise performance
        $subjectPerformance = InstituteSubject::with(['marks' => function($query) {
            $query->select('subject_id', DB::raw('AVG(total_marks) as avg_marks'))
                ->groupBy('subject_id');
        }])->take(6)->get();

        $subjectNames = $subjectPerformance->pluck('name')->toArray();
        $subjectAverages = $subjectPerformance->map(function($subject) {
            return round($subject->marks->avg('avg_marks') ?? 0, 2);
        })->toArray();

        // Monthly student enrollment
        $monthlyEnrollment = Student::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('COUNT(*) as count')
        )
        ->whereYear('created_at', Carbon::now()->year)
        ->groupBy('month')
        ->orderBy('month')
        ->get();

        $monthlyData = array_fill(0, 12, 0);
        foreach ($monthlyEnrollment as $data) {
            $monthlyData[$data->month - 1] = $data->count;
        }

        // Grade distribution data
        $gradeData = Grade::withCount(['results' => function($query) {
            $query->whereNotNull('final_grade');
        }])->get();

        $gradeLabels = $gradeData->pluck('name')->toArray();
        $gradeCounts = $gradeData->pluck('results_count')->toArray();

        // Exam pass/fail ratio
        $examStats = Exam::select('id', 'name')
            ->withCount(['results as passed_count' => function($query) {
                $query->where('gpa', '>=', 2.00);
            }])
            ->withCount(['results as failed_count' => function($query) {
                $query->where('gpa', '<', 2.00);
            }])
            ->latest()
            ->take(5)
            ->get();

        $examNames = $examStats->pluck('name')->toArray();
        $passedCounts = $examStats->pluck('passed_count')->toArray();
        $failedCounts = $examStats->pluck('failed_count')->toArray();

        return [
            'subject_performance' => [
                'labels' => $subjectNames,
                'data' => $subjectAverages,
                'colors' => ['#3b82f6', '#10b981', '#8b5cf6', '#f59e0b', '#ef4444', '#ec4899']
            ],
            'monthly_enrollment' => [
                'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                'data' => $monthlyData
            ],
            'grade_distribution' => [
                'labels' => $gradeLabels,
                'data' => $gradeCounts,
                'colors' => ['#10b981', '#3b82f6', '#8b5cf6', '#f59e0b', '#f97316', '#ef4444']
            ],
            'exam_results' => [
                'labels' => $examNames,
                'passed' => $passedCounts,
                'failed' => $failedCounts
            ]
        ];
    }

    /**
     * Get recent activities.
     */
    private function getRecentActivities()
    {
        $activities = collect();

        // Recent Students
        $recentStudents = Student::latest()->take(3)->get()->map(function($student) {
            return [
                'id' => 'student_' . $student->id,
                'type' => 'student',
                'title' => 'New Student Registered',
                'description' => $student->name . ' has been registered.',
                'time' => $student->created_at->diffForHumans(),
                'icon' => 'fa-user-plus',
                'color' => 'blue'
            ];
        });

        // Recent Results
        $recentResults = Result::with(['student', 'exam'])
            ->latest()
            ->take(3)
            ->get()
            ->map(function($result) {
                return [
                    'id' => 'result_' . $result->id,
                    'type' => 'result',
                    'title' => 'Results Published',
                    'description' => $result->student->name . ' - ' . $result->exam->name,
                    'time' => $result->created_at->diffForHumans(),
                    'icon' => 'fa-file-lines',
                    'color' => 'green'
                ];
            });

        // Recent Exams
        $recentExams = Exam::latest()->take(2)->get()->map(function($exam) {
            return [
                'id' => 'exam_' . $exam->id,
                'type' => 'exam',
                'title' => 'New Exam Created',
                'description' => $exam->name . ' for Class ' . $exam->class->name,
                'time' => $exam->created_at->diffForHumans(),
                'icon' => 'fa-calendar-plus',
                'color' => 'yellow'
            ];
        });

        // Merge and sort activities
        $activities = $activities
            ->merge($recentStudents)
            ->merge($recentResults)
            ->merge($recentExams)
            ->sortByDesc('time')
            ->take(10)
            ->values();

        return $activities;
    }

    /**
     * Get upcoming exams.
     */
    private function getUpcomingExams()
    {
        return Exam::with(['class', 'subjects'])
            ->where('start_date', '>=', Carbon::today())
            ->orderBy('start_date', 'asc')
            ->take(5)
            ->get()
            ->map(function($exam) {
                return [
                    'id' => $exam->id,
                    'name' => $exam->name,
                    'class_name' => $exam->class->name,
                    'start_date' => $exam->start_date->format('M d, Y'),
                    'end_date' => $exam->end_date->format('M d, Y'),
                    'days_left' => Carbon::today()->diffInDays($exam->start_date),
                    'subjects_count' => $exam->subjects->count(),
                    'status' => $this->getExamStatus($exam)
                ];
            });
    }

    /**
     * Get exam status.
     */
    private function getExamStatus($exam)
    {
        if ($exam->start_date > Carbon::today()) {
            return 'upcoming';
        } elseif ($exam->end_date < Carbon::today()) {
            return 'completed';
        } else {
            return 'ongoing';
        }
    }

    /**
     * Get recent results.
     */
    private function getRecentResults()
    {
        return Result::with(['student', 'exam', 'class'])
            ->where('is_published', true)
            ->latest()
            ->take(5)
            ->get()
            ->map(function($result) {
                return [
                    'id' => $result->id,
                    'student_name' => $result->student->name,
                    'exam_name' => $result->exam->name,
                    'class_name' => $result->class->name,
                    'gpa' => $result->gpa,
                    'grade' => $result->final_grade,
                    'status' => $result->gpa >= 2.00 ? 'Passed' : 'Failed',
                    'position' => $result->position,
                    'total_marks' => $result->total_marks
                ];
            });
    }

    /**
     * Get grade distribution.
     */
    private function getGradeDistribution()
    {
        $grades = Grade::withCount(['results' => function($query) {
            $query->whereNotNull('final_grade');
        }])->get();

        return $grades->map(function($grade) {
            return [
                'name' => $grade->name,
                'count' => $grade->results_count,
                'point' => $grade->point,
                'color' => $this->getGradeColor($grade->name)
            ];
        });
    }

    /**
     * Get grade color.
     */
    private function getGradeColor($grade)
    {
        $colors = [
            'A+' => '#10b981',
            'A' => '#3b82f6',
            'A-' => '#60a5fa',
            'B' => '#f59e0b',
            'C' => '#f97316',
            'D' => '#ef4444',
            'F' => '#dc2626'
        ];
        return $colors[$grade] ?? '#6b7280';
    }

    /**
     * Get attendance summary.
     */
    private function getAttendanceSummary()
    {
        $today = Carbon::today();
        $weekAgo = Carbon::today()->subDays(7);

        $totalStudents = Student::where('status', 'active')->count();
        $todayAttendance = Attendance::whereDate('date', $today)->count();
        $presentToday = Attendance::whereDate('date', $today)
            ->where('status', 'present')
            ->count();

        $weeklyAttendance = Attendance::whereBetween('date', [$weekAgo, $today])
            ->select('date', DB::raw('COUNT(*) as total'), 
                DB::raw('SUM(CASE WHEN status = "present" THEN 1 ELSE 0 END) as present'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'total_students' => $totalStudents,
            'today_attendance' => $todayAttendance,
            'present_today' => $presentToday,
            'attendance_percentage' => $todayAttendance > 0 ? 
                round(($presentToday / $todayAttendance) * 100, 2) : 0,
            'weekly_data' => $weeklyAttendance->map(function($day) {
                return [
                    'date' => $day->date->format('D'),
                    'present' => $day->present,
                    'absent' => $day->total - $day->present
                ];
            })
        ];
    }

    /**
     * Get subject performance.
     */
    private function getSubjectPerformance()
    {
        return InstituteSubject::with(['marks' => function($query) {
            $query->select('subject_id', DB::raw('AVG(total_marks) as avg_marks'))
                ->groupBy('subject_id');
        }])
        ->take(8)
        ->get()
        ->map(function($subject) {
            $avgMarks = $subject->marks->avg('avg_marks') ?? 0;
            $passingMarks = $subject->pass_marks ?? 33;
            
            return [
                'name' => $subject->name,
                'avg_marks' => round($avgMarks, 2),
                'pass_percentage' => $avgMarks > 0 ? 
                    round(($avgMarks / $subject->full_marks) * 100, 2) : 0,
                'status' => $avgMarks >= $passingMarks ? 'Good' : 'Needs Improvement'
            ];
        });
    }

    /**
     * Get quick stats for dashboard cards.
     */
    private function getQuickStats()
    {
        $currentYear = AcademicYear::where('is_current', true)->first();
        $currentYearId = $currentYear ? $currentYear->id : null;

        return [
            'students' => [
                'total' => Student::count(),
                'active' => Student::where('status', 'active')->count(),
                'this_month' => Student::whereMonth('created_at', Carbon::now()->month)
                    ->whereYear('created_at', Carbon::now()->year)
                    ->count()
            ],
            'teachers' => [
                'total' => Teacher::count(),
                'active' => Teacher::where('status', 'active')->count()
            ],
            'exams' => [
                'total' => Exam::count(),
                'ongoing' => Exam::where('start_date', '<=', Carbon::today())
                    ->where('end_date', '>=', Carbon::today())
                    ->count(),
                'upcoming' => Exam::where('start_date', '>', Carbon::today())->count()
            ],
            'results' => [
                'total' => Result::count(),
                'published' => Result::where('is_published', true)->count(),
                'pending' => Result::where('is_published', false)->count()
            ],
            'attendance' => [
                'today' => Attendance::whereDate('date', Carbon::today())->count(),
                'present' => Attendance::whereDate('date', Carbon::today())
                    ->where('status', 'present')
                    ->count()
            ]
        ];
    }

    /**
     * Get notifications.
     */
    private function getNotifications()
    {
        $notifications = collect();

        // Pending marks entry
        $pendingMarks = Exam::where('end_date', '<', Carbon::today())
            ->whereDoesntHave('marks')
            ->count();

        if ($pendingMarks > 0) {
            $notifications->push([
                'id' => 'notification_1',
                'type' => 'warning',
                'title' => 'Pending Marks Entry',
                'message' => "{$pendingMarks} exams require marks entry.",
                'time' => Carbon::now()->diffForHumans(),
                'icon' => 'fa-exclamation-triangle'
            ]);
        }

        // Upcoming exams
        $upcomingExams = Exam::whereBetween('start_date', [Carbon::today(), Carbon::today()->addDays(3)])
            ->count();

        if ($upcomingExams > 0) {
            $notifications->push([
                'id' => 'notification_2',
                'type' => 'info',
                'title' => 'Upcoming Exams',
                'message' => "{$upcomingExams} exams scheduled in the next 3 days.",
                'time' => Carbon::now()->diffForHumans(),
                'icon' => 'fa-calendar-alt'
            ]);
        }

        // Results to publish
        $pendingResults = Result::where('is_published', false)
            ->whereHas('exam', function($query) {
                $query->where('end_date', '<', Carbon::today());
            })
            ->count();

        if ($pendingResults > 0) {
            $notifications->push([
                'id' => 'notification_3',
                'type' => 'success',
                'title' => 'Results Ready to Publish',
                'message' => "{$pendingResults} results are ready for publication.",
                'time' => Carbon::now()->diffForHumans(),
                'icon' => 'fa-file-lines'
            ]);
        }

        // Low attendance alert
        $lowAttendance = Attendance::whereDate('date', Carbon::today())
            ->where('status', 'absent')
            ->count();

        if ($lowAttendance > 10) {
            $notifications->push([
                'id' => 'notification_4',
                'type' => 'error',
                'title' => 'Low Attendance Alert',
                'message' => "{$lowAttendance} students are absent today.",
                'time' => Carbon::now()->diffForHumans(),
                'icon' => 'fa-user-times'
            ]);
        }

        return $notifications->sortByDesc('time')->take(5)->values();
    }

    /**
     * Get system status.
     */
    private function getSystemStatus()
    {
        $dbConnection = DB::connection()->getPdo() ? true : false;

        $storageUsed = $this->getDirectorySize(storage_path());
        $storageLimit = 5 * 1024 * 1024 * 1024; // 5GB
        $storageUsagePercentage = ($storageUsed / $storageLimit) * 100;

        return [
            'database' => [
                'status' => $dbConnection ? 'Operational' : 'Error',
                'color' => $dbConnection ? 'green' : 'red'
            ],
            'server' => [
                'status' => 'Online',
                'color' => 'green'
            ],
            'storage' => [
                'used' => $this->formatBytes($storageUsed),
                'total' => $this->formatBytes($storageLimit),
                'percentage' => round($storageUsagePercentage, 2),
                'color' => $storageUsagePercentage < 70 ? 'green' : 
                          ($storageUsagePercentage < 85 ? 'yellow' : 'red')
            ],
            'backup' => [
                'status' => 'Successful',
                'last_backup' => $this->getLastBackupTime(),
                'color' => 'green'
            ],
            'pending_tasks' => $this->getPendingTasksCount(),
            'uptime' => $this->getUptime()
        ];
    }

    /**
     * Get directory size.
     */
    private function getDirectorySize($path)
    {
        $size = 0;
        if (is_dir($path)) {
            $files = scandir($path);
            foreach ($files as $file) {
                if ($file != '.' && $file != '..') {
                    $filePath = $path . '/' . $file;
                    if (is_file($filePath)) {
                        $size += filesize($filePath);
                    } elseif (is_dir($filePath)) {
                        $size += $this->getDirectorySize($filePath);
                    }
                }
            }
        }
        return $size;
    }

    /**
     * Format bytes to human readable format.
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    /**
     * Get last backup time.
     */
    private function getLastBackupTime()
    {
        $backupPath = storage_path('app/backup');
        if (!is_dir($backupPath)) {
            return 'Never';
        }

        $files = glob($backupPath . '/*.zip');
        if (empty($files)) {
            return 'Never';
        }

        $lastBackup = max($files);
        return date('M d, Y H:i', filemtime($lastBackup));
    }

    /**
     * Get pending tasks count.
     */
    private function getPendingTasksCount()
    {
        $count = 0;

        // Pending marks entry
        $count += Exam::where('end_date', '<', Carbon::today())
            ->whereDoesntHave('marks')
            ->count();

        // Pending results
        $count += Result::where('is_published', false)
            ->whereHas('exam', function($query) {
                $query->where('end_date', '<', Carbon::today());
            })
            ->count();

        // Pending attendance
        $count += Attendance::whereDate('date', Carbon::today())->count() == 0 ? 1 : 0;

        return $count;
    }

    /**
     * Get system uptime.
     */
    private function getUptime()
    {
        if (function_exists('exec')) {
            $uptime = exec('uptime -p');
            if ($uptime) {
                return str_replace('up ', '', $uptime);
            }
        }
        return 'Unknown';
    }

    /**
     * Get top performing students.
     */
    private function getTopPerformers()
    {
        return Result::with(['student', 'exam'])
            ->where('is_published', true)
            ->whereNotNull('gpa')
            ->orderBy('gpa', 'desc')
            ->orderBy('total_marks', 'desc')
            ->take(5)
            ->get()
            ->map(function($result) {
                return [
                    'student_name' => $result->student->name,
                    'gpa' => $result->gpa,
                    'grade' => $result->final_grade,
                    'exam_name' => $result->exam->name
                ];
            });
    }

    /**
     * Get class-wise student count.
     */
    // private function getClassWiseStudentCount()
    // {
    //     return InstituteClass::withCount('students')
    //         ->orderBy('numeric_value')
    //         ->get()
    //         ->map(function($class) {
    //             return [
    //                 'name' => $class->name,
    //                 'count' => $class->students_count,
    //                 'color' => $this->getRandomColor($class->id)
    //             ];
    //         });
    // }

    /**
     * Get random color for classes.
     */
    private function getRandomColor($id)
    {
        $colors = ['#3b82f6', '#10b981', '#8b5cf6', '#f59e0b', '#ef4444', '#ec4899', '#06b6d4', '#f97316'];
        return $colors[$id % count($colors)];
    }

    // ============================================
    // API METHODS FOR AJAX REQUESTS
    // ============================================

    /**
     * Get stats for API.
     */
    public function stats()
    {
        return response()->json([
            'success' => true,
            'data' => $this->getStats()
        ]);
    }

    /**
     * Get chart data for API.
     */
    public function chartData()
    {
        return response()->json([
            'success' => true,
            'data' => $this->getChartData()
        ]);
    }

    /**
     * Get recent activities for API.
     */
    public function recentActivities()
    {
        return response()->json([
            'success' => true,
            'data' => $this->getRecentActivities()
        ]);
    }

    /**
     * Get notifications for API.
     */
    public function notifications()
    {
        return response()->json([
            'success' => true,
            'data' => $this->getNotifications()
        ]);
    }

    /**
     * Get system status for API.
     */
    public function systemStatus()
    {
        return response()->json([
            'success' => true,
            'data' => $this->getSystemStatus()
        ]);
    }

    /**
     * Get top performers for API.
     */
    public function topPerformers()
    {
        return response()->json([
            'success' => true,
            'data' => $this->getTopPerformers()
        ]);
    }

    /**
     * Get attendance summary for API.
     */
    public function attendanceSummary()
    {
        return response()->json([
            'success' => true,
            'data' => $this->getAttendanceSummary()
        ]);
    }

    /**
     * Get grade distribution for API.
     */
    public function gradeDistribution()
    {
        return response()->json([
            'success' => true,
            'data' => $this->getGradeDistribution()
        ]);
    }

    /**
     * Get subject performance for API.
     */
    public function subjectPerformance()
    {
        return response()->json([
            'success' => true,
            'data' => $this->getSubjectPerformance()
        ]);
    }

    /**
     * Get quick stats for API.
     */
    public function quickStats()
    {
        return response()->json([
            'success' => true,
            'data' => $this->getQuickStats()
        ]);
    }

    /**
     * Get class-wise student count for API.
     */
    public function classWiseCount()
    {
        return response()->json([
            'success' => true,
            // 'data' => $this->getClassWiseStudentCount()
        ]);
    }

    /**
     * Get upcoming exams for API.
     */
    public function upcomingExams()
    {
        return response()->json([
            'success' => true,
            'data' => $this->getUpcomingExams()
        ]);
    }

    /**
     * Get recent results for API.
     */
    public function recentResults()
    {
        return response()->json([
            'success' => true,
            'data' => $this->getRecentResults()
        ]);
    }

    /**
     * Get dashboard summary for API.
     */
    public function summary()
    {
        return response()->json([
            'success' => true,
            'data' => [
                'stats' => $this->getStats(),
                'quick_stats' => $this->getQuickStats(),
                'attendance_summary' => $this->getAttendanceSummary(),
                'system_status' => $this->getSystemStatus()
            ]
        ]);
    }
}