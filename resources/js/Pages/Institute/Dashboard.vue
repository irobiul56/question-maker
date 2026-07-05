<!-- resources/js/Pages/Dashboard.vue -->
<script setup>
import { ref, computed, onMounted } from 'vue';
import { Head } from '@inertiajs/inertia-vue3';
import InstituteLayout from '@/Layouts/InstituteLayout.vue';

// Props from controller
const props = defineProps({
    stats: {
        type: Object,
        default: () => ({
            total_students: 0,
            total_teachers: 0,
            total_exams: 0,
            total_results: 0
        })
    },
    chartData: {
        type: Object,
        default: () => ({
            subject_performance: { labels: [], data: [] },
            grade_distribution: { labels: [], data: [] },
            monthly_enrollment: { labels: [], data: [] }
        })
    },
    recentActivities: {
        type: Array,
        default: () => []
    },
    upcomingExams: {
        type: Array,
        default: () => []
    },
    quickStats: {
        type: Object,
        default: () => ({
            students: { total: 0, active: 0, this_month: 0 },
            exams: { total: 0, ongoing: 0, upcoming: 0 },
            attendance: { today: 0, present: 0 }
        })
    },
    notifications: {
        type: Array,
        default: () => []
    },
    systemStatus: {
        type: Object,
        default: () => ({
            database: { status: 'Operational', color: 'green' },
            server: { status: 'Online', color: 'green' },
            storage: { used: '0 GB', total: '5 GB', percentage: 0, color: 'green' }
        })
    },
    topPerformers: {
        type: Array,
        default: () => []
    },
    classWiseCount: {
        type: Array,
        default: () => []
    },
    currentAcademicYear: {
        type: Object,
        default: () => ({ session: '2024-2025' })
    }
});

// State for animated counters
const animatedStats = ref({
    total_students: 0,
    total_teachers: 0,
    total_exams: 0,
    total_results: 0
});

// Animate numbers on mount
onMounted(() => {
    animateNumbers();
});

const animateNumbers = () => {
    const targets = {
        total_students: props.stats.total_students || 0,
        total_teachers: props.stats.total_teachers || 0,
        total_exams: props.stats.total_exams || 0,
        total_results: props.stats.total_results || 0
    };

    const duration = 1500;
    const startTime = Date.now();

    const animate = () => {
        const elapsed = Date.now() - startTime;
        const progress = Math.min(elapsed / duration, 1);
        
        // Ease out cubic
        const ease = 1 - Math.pow(1 - progress, 3);
        
        animatedStats.value.total_students = Math.round(targets.total_students * ease);
        animatedStats.value.total_teachers = Math.round(targets.total_teachers * ease);
        animatedStats.value.total_exams = Math.round(targets.total_exams * ease);
        animatedStats.value.total_results = Math.round(targets.total_results * ease);
        
        if (progress < 1) {
            requestAnimationFrame(animate);
        }
    };
    
    animate();
};

// Helper methods
const getStatusBadge = (status) => {
    const badges = {
        upcoming: 'bg-yellow-100 text-yellow-800',
        ongoing: 'bg-green-100 text-green-800',
        completed: 'bg-blue-100 text-blue-800'
    };
    return badges[status] || badges.upcoming;
};

const getStatusText = (status) => {
    const texts = {
        upcoming: 'Upcoming',
        ongoing: 'Ongoing',
        completed: 'Completed'
    };
    return texts[status] || status;
};

// Calculate max value for bars
const maxSubjectMarks = computed(() => {
    const data = props.chartData?.subject_performance?.data || [];
    return Math.max(...data, 100);
});

const maxGradeCount = computed(() => {
    const data = props.chartData?.grade_distribution?.data || [];
    return Math.max(...data, 1);
});

const maxEnrollment = computed(() => {
    const data = props.chartData?.monthly_enrollment?.data || [];
    return Math.max(...data, 1);
});

// Get color for grade bars
const getGradeColor = (grade) => {
    const colors = {
        'A+': '#10b981',
        'A': '#3b82f6',
        'B': '#8b5cf6',
        'C': '#f59e0b',
        'D': '#f97316',
        'F': '#ef4444'
    };
    return colors[grade] || '#6b7280';
};

// Get progress bar color
const getProgressColor = (value) => {
    if (value >= 80) return 'bg-green-500';
    if (value >= 60) return 'bg-blue-500';
    if (value >= 40) return 'bg-yellow-500';
    return 'bg-red-500';
};
</script>

<template>
    <Head title="Dashboard" />
    
    <InstituteLayout>
        <!-- Header -->
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-semibold text-gray-800">Dashboard</h2>
                    <p class="text-sm text-gray-500 mt-1">
                        Welcome back! Here's what's happening with your school.
                    </p>
                </div>
                <div class="flex items-center space-x-3">
                    <span class="text-sm text-gray-500">
                        {{ currentAcademicYear?.session || '2024-2025' }}
                    </span>
                </div>
            </div>
        </template>

        <div class="space-y-6 w-full p-5">
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-l-blue-500 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Total Students</p>
                            <p class="text-2xl font-bold text-gray-800 mt-1">{{ animatedStats.total_students.toLocaleString() }}</p>
                        </div>
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                            <i class="fa-solid fa-users text-2xl text-blue-600"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-l-green-500 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Total Teachers</p>
                            <p class="text-2xl font-bold text-gray-800 mt-1">{{ animatedStats.total_teachers.toLocaleString() }}</p>
                        </div>
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                            <i class="fa-solid fa-chalkboard-user text-2xl text-green-600"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-l-purple-500 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Total Exams</p>
                            <p class="text-2xl font-bold text-gray-800 mt-1">{{ animatedStats.total_exams.toLocaleString() }}</p>
                        </div>
                        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                            <i class="fa-solid fa-pen-to-square text-2xl text-purple-600"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-l-yellow-500 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Total Results</p>
                            <p class="text-2xl font-bold text-gray-800 mt-1">{{ animatedStats.total_results.toLocaleString() }}</p>
                        </div>
                        <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                            <i class="fa-solid fa-file-lines text-2xl text-yellow-600"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h3 class="text-sm font-medium text-gray-500 mb-4">Students Overview</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Total</span>
                            <span class="text-sm font-bold text-gray-800">{{ quickStats.students.total || 0 }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Active</span>
                            <span class="text-sm font-bold text-green-600">{{ quickStats.students.active || 0 }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">New This Month</span>
                            <span class="text-sm font-bold text-blue-600">{{ quickStats.students.this_month || 0 }}</span>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h3 class="text-sm font-medium text-gray-500 mb-4">Exams Overview</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Total</span>
                            <span class="text-sm font-bold text-gray-800">{{ quickStats.exams.total || 0 }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Ongoing</span>
                            <span class="text-sm font-bold text-yellow-600">{{ quickStats.exams.ongoing || 0 }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Upcoming</span>
                            <span class="text-sm font-bold text-blue-600">{{ quickStats.exams.upcoming || 0 }}</span>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h3 class="text-sm font-medium text-gray-500 mb-4">Today's Attendance</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Present</span>
                            <span class="text-sm font-bold text-green-600">{{ quickStats.attendance.present || 0 }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Total</span>
                            <span class="text-sm font-bold text-gray-800">{{ quickStats.attendance.today || 0 }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Percentage</span>
                            <span class="text-sm font-bold text-blue-600">
                                {{ quickStats.attendance.today > 0 ? 
                                    Math.round((quickStats.attendance.present / quickStats.attendance.today) * 100) : 0 }}%
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Custom Charts -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Subject Performance - Custom Bar Chart -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">Subject Performance</h3>
                        <span class="text-xs text-gray-400">Average Marks</span>
                    </div>
                    <div class="space-y-4">
                        <div 
                            v-for="(item, index) in chartData?.subject_performance?.labels || ['Math', 'Physics', 'Chemistry', 'Biology', 'English', 'Bangla']" 
                            :key="index"
                            class="space-y-1"
                        >
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">{{ item }}</span>
                                <span class="font-medium text-gray-800">
                                    {{ chartData?.subject_performance?.data?.[index] || 0 }}%
                                </span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2.5 overflow-hidden">
                                <div 
                                    class="h-2.5 rounded-full transition-all duration-1000 ease-out"
                                    :class="getProgressColor(chartData?.subject_performance?.data?.[index] || 0)"
                                    :style="{ 
                                        width: Math.min((chartData?.subject_performance?.data?.[index] || 0), 100) + '%',
                                        transition: 'width 1s ease-out'
                                    }"
                                ></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Grade Distribution - Custom Bar Chart -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">Grade Distribution</h3>
                        <span class="text-xs text-gray-400">Number of Students</span>
                    </div>
                    <div class="h-64 flex items-end space-x-2">
                        <div 
                            v-for="(item, index) in chartData?.grade_distribution?.labels || ['A+', 'A', 'B', 'C', 'D', 'F']" 
                            :key="index"
                            class="flex-1 flex flex-col items-center space-y-2"
                        >
                            <div class="relative w-full flex justify-center">
                                <div 
                                    class="w-full max-w-[40px] rounded-t-lg transition-all duration-1000 ease-out"
                                    :style="{
                                        height: Math.max(((chartData?.grade_distribution?.data?.[index] || 0) / maxGradeCount) * 200, 4) + 'px',
                                        backgroundColor: getGradeColor(item),
                                        transition: 'height 1s ease-out'
                                    }"
                                ></div>
                            </div>
                            <span class="text-xs font-medium text-gray-600">{{ item }}</span>
                            <span class="text-xs text-gray-400">{{ chartData?.grade_distribution?.data?.[index] || 0 }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Monthly Enrollment - Custom Line Chart -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Monthly Student Enrollment</h3>
                    <span class="text-xs text-gray-400">2024</span>
                </div>
                <div class="relative h-64">
                    <!-- Grid lines -->
                    <div class="absolute inset-0 flex flex-col justify-between">
                        <div class="border-t border-gray-100"></div>
                        <div class="border-t border-gray-100"></div>
                        <div class="border-t border-gray-100"></div>
                        <div class="border-t border-gray-100"></div>
                    </div>
                    
                    <!-- Chart area -->
                    <div class="relative h-full flex items-end">
                        <div class="absolute inset-0 flex items-end">
                            <div class="w-full flex justify-between px-2">
                                <div 
                                    v-for="(item, index) in chartData?.monthly_enrollment?.data || Array(12).fill(0)" 
                                    :key="index"
                                    class="flex flex-col items-center flex-1"
                                >
                                    <div 
                                        class="w-6 rounded-t transition-all duration-1000 ease-out"
                                        :style="{
                                            height: Math.max((item / maxEnrollment) * 200, 2) + 'px',
                                            backgroundColor: '#3b82f6',
                                            opacity: 0.7,
                                            transition: 'height 1s ease-out'
                                        }"
                                    ></div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Line connecting dots -->
                        <div class="absolute inset-0 flex items-end pointer-events-none">
                            <svg class="w-full h-[85%]" preserveAspectRatio="none">
                                <polyline
                                    :points="(chartData?.monthly_enrollment?.data || Array(12).fill(0)).map((val, idx) => {
                                        const x = (idx / ((chartData?.monthly_enrollment?.data || []).length - 1)) * 100;
                                        const y = 100 - ((val / maxEnrollment) * 85);
                                        return `${x},${y}`;
                                    }).join(' ')"
                                    fill="none"
                                    stroke="#3b82f6"
                                    stroke-width="2"
                                    stroke-linejoin="round"
                                    stroke-linecap="round"
                                />
                            </svg>
                        </div>
                    </div>
                    
                    <!-- Labels -->
                    <div class="flex justify-between mt-2 text-xs text-gray-500">
                        <span v-for="month in ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']" 
                              :key="month"
                              class="flex-1 text-center">
                            {{ month }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Recent Activities & Upcoming Exams -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Recent Activities -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">Recent Activities</h3>
                        <button class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                            View All
                        </button>
                    </div>
                    <div v-if="recentActivities.length > 0" class="space-y-4 max-h-64 overflow-y-auto">
                        <div 
                            v-for="activity in recentActivities.slice(0, 5)" 
                            :key="activity.id"
                            class="flex items-start space-x-3 p-2 rounded-lg hover:bg-gray-50 transition-colors"
                        >
                            <div 
                                class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0"
                                :class="{
                                    'bg-blue-100 text-blue-600': activity.color === 'blue',
                                    'bg-green-100 text-green-600': activity.color === 'green',
                                    'bg-yellow-100 text-yellow-600': activity.color === 'yellow',
                                    'bg-purple-100 text-purple-600': activity.color === 'purple'
                                }"
                            >
                                <i :class="['fa-solid', activity.icon]"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-800">{{ activity.title }}</p>
                                <p class="text-xs text-gray-500">{{ activity.description }}</p>
                                <p class="text-xs text-gray-400 mt-1">{{ activity.time }}</p>
                            </div>
                        </div>
                    </div>
                    <div v-else class="text-center py-8 text-gray-400">
                        <i class="fa-solid fa-clock text-3xl mb-2"></i>
                        <p>No recent activities</p>
                    </div>
                </div>

                <!-- Upcoming Exams -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">Upcoming Exams</h3>
                        <button class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                            View All
                        </button>
                    </div>
                    <div v-if="upcomingExams.length > 0" class="space-y-4">
                        <div 
                            v-for="exam in upcomingExams.slice(0, 4)" 
                            :key="exam.id"
                            class="flex items-start space-x-4 p-3 border border-gray-100 rounded-lg hover:border-blue-300 transition-colors"
                        >
                            <div class="w-12 h-12 bg-blue-100 rounded-lg flex flex-col items-center justify-center flex-shrink-0">
                                <span class="text-xs font-bold text-blue-600">
                                    {{ new Date(exam.start_date).toLocaleDateString('en', { month: 'short' }) }}
                                </span>
                                <span class="text-lg font-bold text-blue-700">
                                    {{ new Date(exam.start_date).toLocaleDateString('en', { day: 'numeric' }) }}
                                </span>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-800">{{ exam.name }}</p>
                                <p class="text-xs text-gray-500">{{ exam.class_name }}</p>
                                <div class="flex items-center mt-1 space-x-2">
                                    <span class="text-xs text-gray-400">
                                        <i class="fa-regular fa-clock mr-1"></i>
                                        {{ exam.days_left }} days left
                                    </span>
                                    <span 
                                        class="text-xs px-2 py-0.5 rounded-full"
                                        :class="getStatusBadge(exam.status)"
                                    >
                                        {{ getStatusText(exam.status) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-else class="text-center py-8 text-gray-400">
                        <i class="fa-solid fa-calendar-check text-3xl mb-2"></i>
                        <p>No upcoming exams</p>
                    </div>
                </div>
            </div>

            <!-- Notifications & System Status -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Notifications -->
                <div class="lg:col-span-2 bg-white rounded-xl shadow-sm p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">Notifications</h3>
                        <button class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                            Mark all as read
                        </button>
                    </div>
                    <div v-if="notifications.length > 0" class="space-y-3 max-h-48 overflow-y-auto">
                        <div 
                            v-for="notification in notifications.slice(0, 4)" 
                            :key="notification.id"
                            class="flex items-start space-x-3 p-3 rounded-lg hover:bg-gray-50 transition-colors"
                        >
                            <div 
                                class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0"
                                :class="{
                                    'bg-blue-100 text-blue-600': notification.type === 'info',
                                    'bg-yellow-100 text-yellow-600': notification.type === 'warning',
                                    'bg-green-100 text-green-600': notification.type === 'success',
                                    'bg-red-100 text-red-600': notification.type === 'error'
                                }"
                            >
                                <i :class="['fa-solid', notification.icon]"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-800">{{ notification.title }}</p>
                                <p class="text-xs text-gray-500">{{ notification.message }}</p>
                                <p class="text-xs text-gray-400 mt-1">{{ notification.time }}</p>
                            </div>
                        </div>
                    </div>
                    <div v-else class="text-center py-8 text-gray-400">
                        <i class="fa-solid fa-bell-slash text-3xl mb-2"></i>
                        <p>No notifications</p>
                    </div>
                </div>

                <!-- System Status -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">System Status</h3>
                    <div class="space-y-3">
                        <div 
                            v-for="(item, key) in systemStatus" 
                            :key="key"
                            class="flex items-center justify-between"
                        >
                            <span class="text-sm text-gray-600 capitalize">{{ key.replace('_', ' ') }}</span>
                            <div class="flex items-center space-x-2">
                                <span 
                                    class="w-2 h-2 rounded-full"
                                    :class="{
                                        'bg-green-500': item.color === 'green',
                                        'bg-yellow-500': item.color === 'yellow',
                                        'bg-red-500': item.color === 'red'
                                    }"
                                ></span>
                                <span 
                                    class="text-sm font-medium"
                                    :class="{
                                        'text-green-600': item.color === 'green',
                                        'text-yellow-600': item.color === 'yellow',
                                        'text-red-600': item.color === 'red'
                                    }"
                                >
                                    {{ typeof item === 'object' ? item.status : item }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-600">Uptime</span>
                            <span class="font-medium text-gray-800">{{ systemStatus.uptime || '99.98%' }}</span>
                        </div>
                        <div class="mt-1 w-full bg-gray-200 rounded-full h-1.5">
                            <div 
                                class="bg-green-500 h-1.5 rounded-full"
                                style="width: 99.9%"
                            ></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Class-wise Student Distribution -->
            <div v-if="classWiseCount && classWiseCount.length > 0" class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Class-wise Student Distribution</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                    <div 
                        v-for="classItem in classWiseCount" 
                        :key="classItem.name"
                        class="text-center p-4 rounded-lg border border-gray-100 hover:border-blue-300 transition-colors"
                    >
                        <div 
                            class="w-12 h-12 rounded-full flex items-center justify-center mx-auto text-white font-bold text-lg"
                            :style="{ backgroundColor: classItem.color || '#3b82f6' }"
                        >
                            {{ classItem.count }}
                        </div>
                        <p class="text-sm font-medium text-gray-800 mt-2">{{ classItem.name }}</p>
                    </div>
                </div>
            </div>

            <!-- Top Performers -->
            <div v-if="topPerformers && topPerformers.length > 0" class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Top Performers</h3>
                    <button class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                        View All
                    </button>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                    <div 
                        v-for="(student, index) in topPerformers" 
                        :key="index"
                        class="text-center p-4 rounded-lg border border-gray-100 hover:border-green-300 transition-colors"
                    >
                        <div 
                            class="w-14 h-14 rounded-full flex items-center justify-center mx-auto font-bold text-xl"
                            :class="{
                                'bg-yellow-100 text-yellow-600': index === 0,
                                'bg-gray-100 text-gray-600': index === 1,
                                'bg-orange-100 text-orange-600': index === 2,
                                'bg-blue-100 text-blue-600': index > 2
                            }"
                        >
                            #{{ index + 1 }}
                        </div>
                        <p class="text-sm font-medium text-gray-800 mt-2">{{ student.student_name }}</p>
                        <p class="text-xs text-gray-500">{{ student.exam_name }}</p>
                        <div class="mt-1">
                            <span class="text-lg font-bold text-green-600">{{ student.gpa }}</span>
                            <span class="text-xs text-gray-500 ml-1">GPA</span>
                        </div>
                        <span class="inline-block mt-1 px-2 py-0.5 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                            {{ student.grade }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </InstituteLayout>
</template>

<style scoped>
/* Custom scrollbar */
.max-h-64::-webkit-scrollbar,
.max-h-48::-webkit-scrollbar {
    width: 4px;
}

.max-h-64::-webkit-scrollbar-track,
.max-h-48::-webkit-scrollbar-track {
    background: transparent;
}

.max-h-64::-webkit-scrollbar-thumb,
.max-h-48::-webkit-scrollbar-thumb {
    background: #e5e7eb;
    border-radius: 2px;
}

.max-h-64::-webkit-scrollbar-thumb:hover,
.max-h-48::-webkit-scrollbar-thumb:hover {
    background: #d1d5db;
}

/* Animation for numbers */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.bg-white {
    animation: fadeInUp 0.5s ease-out;
}

/* Progress bar animation */
.bg-blue-500, .bg-green-500, .bg-yellow-500, .bg-red-500 {
    transition: width 1.5s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Chart bar animation */
.rounded-t-lg {
    transform-origin: bottom;
    animation: growBar 1s ease-out;
}

@keyframes growBar {
    from {
        transform: scaleY(0);
    }
    to {
        transform: scaleY(1);
    }
}

/* Hover effects for stat cards */
.hover\\:shadow-md:hover {
    transition: all 0.3s ease;
}
</style>