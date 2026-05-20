<template>
    <Head title="User Dashboard" />

    <AuthenticatedLayout>
        <!-- Loading Overlay -->
        <div v-if="loading" class="fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center z-50">
            <div class="bg-white p-4 rounded-lg shadow-lg">
                Loading questions...
            </div>
        </div>

        <div class="bg-white min-h-screen">
            <!-- Main Content -->
            <div class="w-full max-w-3xl p-4">
                <!-- Bottom Action Buttons -->
                <div class="flex items-center justify-between mt-4">
                    <div class="py-1 flex gap-1 items-center">
                        <p class="bg-gray-100 px-2 py-1 rounded">
                            Selected: {{ selectedQuestions.length }}/{{ questions.length }}
                        </p>
                        <button @click="toggleSelectAll" class="bg-gray-200 hover:bg-gray-300 px-2 py-1 rounded">
                            {{ isSelectAll ? 'Deselect All' : 'Select All' }}
                        </button>
                    </div>

                    <div class="flex items-center gap-2">
                        <button @click="saveQuestions"
                    class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded">
                    <span class="flex items-center gap-1">
                        <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 448 512"
                            height="1em" width="1em" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M433.941 129.941l-83.882-83.882A48 48 0 0 0 316.118 32H48C21.49 32 0 53.49 0 80v352c0 26.51 21.49 48 48 48h352c26.51 0 48-21.49 48-48V163.882a48 48 0 0 0-14.059-33.941zM224 416c-35.346 0-64-28.654-64-64 0-35.346 28.654-64 64-64s64 28.654 64 64c0 35.346-28.654 64-64 64zm96-304.52V212c0 6.627-5.373 12-12 12H76c-6.627 0-12-5.373-12-12V108c0-6.627 5.373-12 12-12h228.52c3.183 0 6.235 1.264 8.485 3.515l3.48 3.48A11.996 11.996 0 0 1 320 111.48z">
                            </path>
                        </svg>
                        ব্লগ সংরক্ষণ করুন
                    </span>
                </button>
                    </div>
                </div>

                <!-- Notice Banner -->
                <div class="text-center bg-yellow-50 p-2 mb-4">
                    <div class="flex flex-wrap gap-1 justify-center mb-1">
                    </div>
                    <p>এই লিস্টে আপনার জন্য বাছাই করা <span class="text-green-600 font-bold">{{ filters.count }}টি ইউনিক
                            প্রশ্ন</span> তৈরি করে দেওয়া হয়েছে। আপনি চাইল এই প্রশ্নগুলো সিলেক্ট করে সেভ করতে পারেন। অথবা
                        অ্যাডভান্স ফিল্টার করেও সেভ করতে পারেন।</p>
                </div>

                <!-- Questions Container - Grouped by Board -->
                <div class="space-y-6">
                    <div v-for="(boardQuestions, boardName) in groupedQuestions" :key="boardName"
                        class="bg-white rounded-lg border">
                        <!-- Questions for this board -->
                        <div class="divide-y">
                            <div v-for="(question, index) in boardQuestions" :key="question.id" 
                                class="p-5 cursor-pointer"
                                :class="{ 'border-green-500 border-2 rounded': selectedQuestions.includes(question.id) }"
                                @click="toggleQuestionSelection(question.id)">
                                <div class="mb-2 flex justify-between items-center">
                                    <div class="flex items-center gap-2">
                                        <input type="checkbox" 
                                            :checked="selectedQuestions.includes(question.id)"
                                            @change="toggleQuestionSelection(question.id)"
                                            @click.stop
                                            class="h-4 w-4 text-blue-600 rounded">
                                        <div class="text-xs flex items-center gap-2">
                                            <mark><i>
                                                <span class="mr-1">
                                                    <span class="ml-1">{{ question.board?.name || 'Unknown' }}</span>
                                                    <span>'{{ question.board?.year || '----' }}</span>
                                                </span>
                                            </i></mark>
                                            <span class="text-blue-600 bg-blue-50 px-1.5 py-0.5 rounded text-xs">
                                                {{ getChapterName(question.chapter_id) }}
                                            </span>
                                        </div>
                                        <button @click="toggleExplanation(question.id)"
                                            class="shrink-0 bg-green-600 hover:bg-green-500 text-white rounded-full text-xs px-1.5 py-0.5">
                                            ব্যাখা দেখুন
                                        </button>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <div title="প্রশ্নে ভুল পেলে রিপোর্ট করুন"
                                            class="border p-1 rounded text-gray-500 hover:border-green-500 hover:text-green-500">
                                            <svg stroke="currentColor" fill="currentColor" stroke-width="0"
                                                viewBox="0 0 512 512" height="1em" width="1em"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M448 0H64C28.7 0 0 28.7 0 64v288c0 35.3 28.7 64 64 64h96v84c0 9.8 11.2 15.5 19.1 9.7L304 416h144c35.3 0 64-28.7 64-64V64c0-35.3-28.7-64-64-64z">
                                                </path>
                                            </svg>
                                        </div>
                                        <div class="flex flex-wrap gap-1">
                                            <span v-if="question.topic" class="text-xs p-1 border rounded-full px-1.5 py-0.5">
                                                {{ question.topic.name }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex items-baseline">
                                    <span class="mr-1">{{ index + 1 }}.</span>
                                    <div class="flex-1">
                                        <div class="mb-2">
                                            <LatexRenderer :content="question.question_text" />
                                        </div>
                                    </div>
                                </div>

                                <!-- MCQ Options -->
                                <div v-if="question.format === 'mcq' && question.options"
                                    class="lg:grid grid-cols-2 gap-2">
                                    <div v-for="option in question.options" :key="option.id"
                                        :class="{ 'bg-green-100 border-green-300': option.is_correct }"
                                        class="bg-gray-100 my-2 lg:m-0 rounded-lg p-2 flex gap-1 items-center">
                                        <div :class="{ 'bg-green-600 text-white': option.is_correct }"
                                            class="flex items-center justify-center h-5 w-5 border border-gray-400 rounded-full p-0.5">
                                            {{ bengaliChars[question.options.indexOf(option)] }}
                                        </div>
                                        <div>
                                            <LatexRenderer :content="option.option_text" />
                                        </div>
                                    </div>
                                </div>

                                <!-- CQ Options -->
                                <div v-if="question.format === 'cq' && question.cqoptions"
                                    class="lg:grid grid-cols-1 gap-2">
                                    <div v-for="cqoption in question.cqoptions" :key="cqoption.id"
                                        class="bg-gray-100 my-2 lg:m-0 rounded-lg p-2 flex gap-1 items-center">
                                        <div class="flex items-center justify-center h-5 w-5 border border-gray-400 rounded-full p-0.5">
                                            {{ bengaliChars[question.cqoptions.indexOf(cqoption)] }}
                                        </div>
                                        <div>
                                            <LatexRenderer :content="cqoption.cq_text" />
                                        </div>
                                    </div>
                                </div>

                                <!-- Explanation -->
                                <div v-if="question.explanation && question.showExplanation"
                                    class="mt-1.5 bg-green-50 py-2 px-3 text-green-700 rounded transition-all duration-300">
                                    <div>
                                        <LatexRenderer :content="question.explanation" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bottom Action Buttons -->
                <div class="flex items-center justify-between mt-4">
                    <div class="py-1 flex gap-1 items-center">
                        <p class="bg-gray-100 px-2 py-1 rounded">Selected: {{ selectedQuestions.length }}/{{ questions.length }}</p>
                        <button @click="toggleSelectAll" class="bg-gray-200 hover:bg-gray-300 px-2 py-1 rounded">
                            {{ isSelectAll ? 'Deselect All' : 'Select All' }}
                        </button>
                    </div>
                    <button class="bg-gray-200 hover:bg-gray-300 px-2.5 py-1 rounded flex gap-1 items-center">
                        Top
                        <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 384 512"
                            class="text-gray-600" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M214.6 41.4c-12.5-12.5-32.8-12.5-45.3 0l-160 160c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L160 141.2V448c0 17.7 14.3 32 32 32s32-14.3 32-32V141.2L329.4 246.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3l-160-160z">
                            </path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Sidebar (hidden on mobile by default) -->
            <div
                class="hidden lg:block fixed top-20 inset-y-0 right-0 w-80 bg-white overflow-y-auto border-l z-10 font-9">
                <div class="p-4">
                    <div
                        class="border rounded-t text-lg font-bold py-2 text-center flex justify-center items-center gap-2">
                        এডভান্সড ফিল্টার মেনু
                    </div>

                    <!-- Search Box -->
                    <!-- <div class="my-4">
                        <div class="flex gap-2">
                            <input type="text" placeholder="কিওয়ার্ড সার্চ করুন"
                                class="flex-1 border rounded px-1 py-1">
                            <button class="rounded bg-gray-200 hover:bg-gray-300 px-1 py-1">সার্চ করুন</button>
                        </div>
                    </div> -->

                    <!-- Unique Question Section -->
                    <!-- <div class="my-4 rounded-lg border border-green-400 bg-gradient-to-r from-green-50 to-white p-4">
                        <div class="mb-2 flex flex-col items-center justify-center">
                            <p class="text-center font-bold mt-3 text-lg text-green-700">পূর্বের তৈরী প্রশ্ন বাদ দিন</p>
                            <p class="text-center my-2 text-gray-600">পূর্বের তৈরী করা প্রশ্ন বাদ দিয়ে নতুন প্রশ্ন তৈরী
                                করতে পারবেন।</p>
                            <p>ইউনিক প্রশ্ন তৈরিতে গুরুত্বপূর্ণ</p>
                        </div>
                        <div class="bg-white max-h-40 overflow-auto">
                            <div
                                class="p-1.5 border rounded mb-0.5 flex items-center gap-2 justify-between cursor-pointer hover:border-red-500">
                                <span class="truncate text-gray-800">xxsdd</span>
                                <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 512 512"
                                    class="shrink-0 text-gray-500" height="1em" width="1em"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M256 8C119 8 8 119 8 256s111 248 248 248 248-111 248-248S393 8 256 8zM124 296c-6.6 0-12-5.4-12-12v-56c0-6.6 5.4-12 12-12h264c6.6 0 12 5.4 12 12v56c0 6.6-5.4 12-12 12H124z">
                                    </path>
                                </svg>
                            </div>
                            <div
                                class="p-1.5 border rounded mb-0.5 flex items-center gap-2 justify-between cursor-pointer hover:border-red-500">
                                <span class="truncate text-gray-800">jhhkh</span>
                                <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 512 512"
                                    class="shrink-0 text-gray-500" height="1em" width="1em"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M256 8C119 8 8 119 8 256s111 248 248 248 248-111 248-248S393 8 256 8zM124 296c-6.6 0-12-5.4-12-12v-56c0-6.6 5.4-12 12-12h264c6.6 0 12 5.4 12 12v56c0 6.6-5.4 12-12 12H124z">
                                    </path>
                                </svg>
                            </div>
                        </div>
                    </div> -->

                    <!-- Question Type Filter -->
                    <div class="bg-white border rounded-lg my-4">
                        <div class="p-2 bg-gray-100 flex justify-between items-center">
                            <p><span class="font-bold">প্রশ্নের ধরণ</span></p>
                            <button v-if="selectedQuestionTypes.length > 0" @click="clearQuestionTypeFilters"
                                class="text-xs bg-gray-200 hover:bg-gray-300 px-2 py-1 rounded">
                                Clear All
                            </button>
                        </div>
                        <div class="border-t text-gray-700">
                            <div class="p-2">
                                <button v-if="quetype.length > 0 && selectedQuestionTypes.length < quetype.length"
                                    @click="selectAllQuestionTypes"
                                    class="text-xs bg-gray-200 hover:bg-gray-300 px-2 py-1 rounded mb-2">
                                    Select All Types
                                </button>
                                <div v-for="(typeItem, index) in quetype" :key="index" class="mb-1">
                                    <div @click="toggleQuestionType(typeItem.id)" :class="{
                                        'bg-blue-100 border-blue-300': selectedQuestionTypes.includes(typeItem.id),
                                        'hover:bg-gray-100': !selectedQuestionTypes.includes(typeItem.id)
                                    }"
                                        class="px-2 py-1.5 flex items-center cursor-pointer rounded border transition-colors">
                                        <input type="checkbox" :checked="selectedQuestionTypes.includes(typeItem.id)"
                                            class="mr-2 rounded cursor-pointer" @click.stop>
                                        <span class="w-full">{{ typeItem.name }}</span>
                                        <span v-if="selectedQuestionTypes.includes(typeItem.id)"
                                            class="ml-2 text-blue-600">
                                            ✓
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Question Categories Filter -->
                    <div v-if="filters.type === 'mix'" class="bg-white border rounded-lg my-4">
                        <div class="p-2 bg-gray-100 flex justify-between items-center">
                            <p><span class="font-bold">প্রশ্ন কেটেগোরিস</span></p>
                            <button v-if="selectedLevels.length > 0" @click="clearLevelFilters"
                                class="text-xs bg-gray-200 hover:bg-gray-300 px-2 py-1 rounded">
                                Clear All
                            </button>
                        </div>
                        <div class="border-t text-gray-700">
                            <div class="p-2">
                                <button v-if="level.length > 0 && selectedLevels.length < level.length"
                                    @click="selectAllLevels"
                                    class="text-xs bg-gray-200 hover:bg-gray-300 px-2 py-1 rounded mb-2">
                                    Select All Types
                                </button>
                                <div v-for="(levelItem, index) in level" :key="index" class="mb-1">
                                    <div @click="toggleLevel(levelItem.id)" :class="{
                                        'bg-blue-100 border-blue-300': selectedLevels.includes(levelItem.id),
                                        'hover:bg-gray-100': !selectedLevels.includes(levelItem.id)
                                    }"
                                        class="px-2 py-1.5 flex items-center cursor-pointer rounded border transition-colors">
                                        <input type="checkbox" :checked="selectedLevels.includes(levelItem.id)"
                                            class="mr-2 rounded cursor-pointer" @click.stop>
                                        <span class="w-full">{{ levelItem.name }}</span>
                                        <span v-if="selectedLevels.includes(levelItem.id)" class="ml-2 text-blue-600">
                                            ✓
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Topic Filter for Multiple Chapters -->
                    <div class="bg-white border rounded-lg my-4">
                        <div class="p-2 bg-gray-100 flex justify-between items-center">
                            <p><span class="font-bold">অধ্যায়:</span> {{ chapters.length }} অধ্যায় সিলেক্টেড</p>
                            <button v-if="selectedTopics.length > 0" @click="clearTopicFilters"
                                class="text-xs bg-gray-200 hover:bg-gray-300 px-2 py-1 rounded">
                                Clear All
                            </button>
                        </div>
                        <div class="border-t text-gray-700">
                            <div class="p-2 max-h-60 overflow-y-auto">
                                <!-- Show topics grouped by chapter -->
                                <div v-for="chapter in chapters" :key="chapter.id" class="mb-4">
                                    <div class="font-semibold mb-2 text-blue-600 border-b pb-1">
                                        {{ chapter.name }}
                                    </div>
                                    <div class="space-y-1 ml-2">
                                        <div v-if="chapter.topic?.length > 0">
                                            <div v-for="topic in chapter.topic" :key="topic.id" class="mb-1">
                                                <div @click="toggleTopic(topic.id)" :class="{
                                                    'bg-blue-100 border-blue-300': selectedTopics.includes(topic.id),
                                                    'hover:bg-gray-100': !selectedTopics.includes(topic.id)
                                                }"
                                                    class="px-2 py-1.5 flex items-center cursor-pointer rounded border transition-colors">
                                                    <input type="checkbox" :checked="selectedTopics.includes(topic.id)"
                                                        class="mr-2 rounded cursor-pointer" @click.stop>
                                                    <span class="w-full">{{ topic.name }}</span>
                                                    <span v-if="selectedTopics.includes(topic.id)" class="ml-2 text-blue-600">
                                                        ✓
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div v-else class="text-gray-500 text-sm italic pl-2">
                                            No topics in this chapter
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Select All Topics Button -->
                                <div class="mt-4 pt-4 border-t">
                                    <button 
                                        @click="selectAllTopics"
                                        class="text-xs bg-blue-100 hover:bg-blue-200 px-3 py-2 rounded w-full">
                                        Select All Topics ({{ totalTopicsCount }})
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Board and Year Filter Section -->
                    <div class="bg-white border rounded-lg my-4">
                        <div class="bg-gray-100 p-2 flex justify-between items-center">
                            <p class="font-bold">বোর্ড</p>
                            <span>
                                <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 448 512"
                                    height="1em" width="1em" xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M201.4 137.4c12.5-12.5 32.8-12.5 45.3 0l160 160c12.5 12.5 12.5 32.8 0 45.3s-32.8 12.5-45.3 0L224 205.3 86.6 342.6c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3l160-160z">
                                    </path>
                                </svg>
                            </span>
                        </div>
                        <div class="border-t text-gray-700">
                            <div class="p-2">
                                <!-- Year Filter -->
                                <div class="p-2">
                                    <label class="block mb-1 text-sm font-medium">Year</label>
                                    <div class="flex flex-wrap gap-2">
                                        <button v-for="year in availableYears" :key="year" @click="toggleYear(year)"
                                            :class="{
                                                'bg-blue-500 text-white': selectedYears.includes(year),
                                                'bg-gray-200': !selectedYears.includes(year)
                                            }" class="px-3 py-1 rounded text-sm">
                                            {{ year }}
                                        </button>
                                    </div>
                                </div>

                                <!-- Board Filter -->
                                <div class="mt-2">
                                    <div v-for="board in availableBoards" :key="board.id" class="mb-1">
                                        <label @click="toggleBoard(board.id)" :class="{
                                            'bg-blue-100 border-blue-300': selectedBoards.includes(board.id),
                                            'hover:bg-gray-100': !selectedBoards.includes(board.id)
                                        }"
                                            class="px-2 py-1.5 flex items-center cursor-pointer rounded border transition-colors">
                                            <input type="checkbox" :checked="selectedBoards.includes(board.id)"
                                                class="mr-2 rounded cursor-pointer" @click.stop>
                                            <span class="w-full">{{ board.name }} ({{ board.year }})</span>
                                        </label>
                                    </div>
                                </div>

                                 <!-- Blog Filter Section -->
                    <div class="bg-white border rounded-lg my-4">
                        <div class="bg-gray-100 p-2">
                            <p class="font-bold">ব্লগ তথ্য</p>
                        </div>
                        <div class="border-t p-4">
                            <div class="space-y-4">
                                <!-- Title Input -->
                                <div>
                                    <label for="blog_title" class="block text-sm font-medium text-gray-700 mb-1">
                                        শিরোনাম <span class="text-red-500">*</span>
                                    </label>
                                    <input 
                                        type="text" 
                                        id="blog_title"
                                        v-model="blogTitle"
                                        placeholder="ব্লগের শিরোনাম লিখুন..."
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                        :class="{ 'border-red-500': titleError }"
                                    >
                                    <p v-if="titleError" class="text-red-500 text-xs mt-1">শিরোনাম প্রদান করা আবশ্যক</p>
                                </div>

                                <!-- Description Textarea -->
                                <div>
                                    <label for="blog_description" class="block text-sm font-medium text-gray-700 mb-1">
                                        বিবরণ <span class="text-red-500">*</span>
                                    </label>
                                    <textarea 
                                        id="blog_description"
                                        v-model="blogDescription"
                                        rows="4"
                                        placeholder="ব্লগের বিবরণ লিখুন..."
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-y"
                                        :class="{ 'border-red-500': descriptionError }"
                                    ></textarea>
                                    <p v-if="descriptionError" class="text-red-500 text-xs mt-1">বিবরণ প্রদান করা আবশ্যক</p>
                                </div>

                                  <!-- Category Selection -->
                                    <div>
                                    <label for="blog_category" class="block text-sm font-medium text-gray-700 mb-1">
                                        ক্যাটাগরি <span class="text-red-500">*</span>
                                    </label>
                                    <select 
                                        id="blog_category"
                                        v-model="blogCategory"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                        :class="{ 'border-red-500': categoryError }"
                                    >
                                        <option value="">ক্যাটাগরি নির্বাচন করুন...</option>
                                        <option v-for="category in categories" :key="category.id" :value="category.id">
                                            {{ category.name }}
                                        </option>
                                    </select>
                                    <p v-if="categoryError" class="text-red-500 text-xs mt-1">ক্যাটাগরি নির্বাচন করা আবশ্যক</p>
                                </div>
                                    </div>
                                </div>
                            </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
/* Add this to your styles */
.cursor-pointer {
    cursor: pointer;
    transition: border-color 0.2s ease;
}

/* Optional: Add hover effect */
.question-item:hover {
    border-color: #e5e7eb; /* gray-200 */
}

</style>

<script setup>
import LatexRenderer from '@/Components/LatexRenderer.vue';
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue"
import { Head } from '@inertiajs/vue3';
import { router, usePage } from '@inertiajs/vue3';
import { ref, watch, computed } from 'vue';

const props = defineProps({
    questions: Array,
    groupedQuestions: Object,
    availableYears: Array,
    chapters: Array, // Changed from chapter to chapters (array)
    filters: Object,
    boards: Array,
    quetype: Array,
    level: Array,
    categories: Array,
});

const examName = computed(() => {
    return usePage().props.exam_name || '';
});

const questions = ref((props.questions || []).map(q => ({
    ...q,
    showExplanation: false
})));
const groupedQuestions = ref(props.groupedQuestions || {});
const chapters = ref(props.chapters || []); // Changed from chapter to chapters
const filters = ref(props.filters || {});
const selectedTopics = ref(props.filters?.topic_id || []);
const selectedQuestionTypes = ref(props.filters?.question_types || []);
const selectedLevels = ref(props.filters?.levels || []);
const selectedBoards = ref([]);
const selectedYears = ref([]);
const loading = ref(false);
const selectedQuestions = ref([]);
const isSelectAll = ref(false);

// Get Bengali character for option index
const bengaliChars = ['ক', 'খ', 'গ', 'ঘ', 'ঙ', 'চ', 'ছ', 'জ', 'ঝ', 'ঞ', 'ট', 'ঠ', 'ড', 'ঢ', 'ণ', 'ত', 'থ', 'দ', 'ধ', 'ন', 'প', 'ফ', 'ব', 'ভ', 'ম', 'য', 'র', 'ল', 'শ', 'ষ', 'স', 'হ', 'ড়', 'ঢ়', 'য়'];

// Computed property to get all topics from all chapters
const allTopics = computed(() => {
    return chapters.value.flatMap(chapter => 
        chapter.topic ? chapter.topic.map(topic => ({
            ...topic,
            chapter_name: chapter.name
        })) : []
    );
});

// Computed property to get total topics count
const totalTopicsCount = computed(() => {
    return allTopics.value.length;
});

// Helper to get chapter name by ID
const getChapterName = (chapterId) => {
    const chapter = chapters.value.find(c => c.id === chapterId);
    return chapter ? chapter.name : 'Unknown Chapter';
};

// Extract available boards from props or from questions
const availableYears = computed(() => {
    const years = new Set();
    props.boards?.forEach(board => {
        if (board.year) years.add(board.year);
    });
    return Array.from(years).sort((a, b) => b - a); // Newest first
});

const availableBoards = computed(() => {
    if (!selectedYears.value.length) {
        return props.boards || [];
    }
    return (props.boards || []).filter(board =>
        selectedYears.value.includes(board.year)
    );
});

// Watch for filter changes and update questions
watch([selectedTopics, selectedBoards, selectedYears, selectedQuestionTypes, selectedLevels],
    ([newTopics, newBoards, newYears, newQuestionTypes, newLevels]) => {
        loading.value = true;

        router.get(route('sltquestionblog'), {
            chapter_id: filters.value.chapter_id,
            topic_id: newTopics,
            type: filters.value.type,
            count: filters.value.count,
            board_ids: newBoards,
            years: newYears,
            question_types: newQuestionTypes,
            levels: newLevels
        }, {
            preserveState: true,
            replace: true,
            only: ['questions', 'groupedQuestions', 'chapters', 'filters'],
            onSuccess: () => {
                loading.value = false;
            },
            onError: () => {
                loading.value = false;
            }
        });
    }, { deep: true });

// Helper functions
const toggleQuestionType = (typeId) => {
    const index = selectedQuestionTypes.value.indexOf(typeId);
    if (index > -1) {
        selectedQuestionTypes.value.splice(index, 1);
    } else {
        selectedQuestionTypes.value.push(typeId);
    }
};

const selectAllQuestionTypes = () => {
    selectedQuestionTypes.value = props.quetype.map(type => type.id);
};

const clearQuestionTypeFilters = () => {
    selectedQuestionTypes.value = [];
};

const toggleTopic = (topicId) => {
    const index = selectedTopics.value.indexOf(topicId);
    if (index > -1) {
        selectedTopics.value.splice(index, 1);
    } else {
        selectedTopics.value.push(topicId);
    }
};

const toggleBoard = (boardId) => {
    const index = selectedBoards.value.indexOf(boardId);
    if (index > -1) {
        selectedBoards.value.splice(index, 1);
    } else {
        selectedBoards.value.push(boardId);
    }
};

const toggleYear = (year) => {
    const index = selectedYears.value.indexOf(year);
    if (index > -1) {
        selectedYears.value.splice(index, 1);
    } else {
        selectedYears.value.push(year);
    }

    // Clear board selections when changing years
    selectedBoards.value = [];
};

const selectAllTopics = () => {
    selectedTopics.value = allTopics.value.map(topic => topic.id);
};

const clearTopicFilters = () => {
    selectedTopics.value = [];
};

const toggleExplanation = (questionId) => {
    const question = questions.value.find(q => q.id === questionId);
    if (question) {
        question.showExplanation = !question.showExplanation;
    }
};

const toggleLevel = (levelId) => {
    const index = selectedLevels.value.indexOf(levelId);
    if (index > -1) {
        selectedLevels.value.splice(index, 1);
    } else {
        selectedLevels.value.push(levelId);
    }
};

const selectAllLevels = () => {
    selectedLevels.value = props.level.map(level => level.id);
};

const clearLevelFilters = () => {
    selectedLevels.value = [];
};

const toggleQuestionSelection = (questionId) => {
    const index = selectedQuestions.value.indexOf(questionId);
    if (index > -1) {
        selectedQuestions.value.splice(index, 1);
    } else {
        selectedQuestions.value.push(questionId);
    }
};

const toggleSelectAll = () => {
    isSelectAll.value = !isSelectAll.value;
    if (isSelectAll.value) {
        selectedQuestions.value = questions.value.map(q => q.id);
    } else {
        selectedQuestions.value = [];
    }
};

// Watch for props changes to update local state
watch(() => props.questions, (newQuestions) => {
    questions.value = (newQuestions || []).map(q => ({
        ...q,
        showExplanation: false
    }));
}, { immediate: true });

watch(() => props.groupedQuestions, (newGrouped) => {
    groupedQuestions.value = newGrouped || {};
}, { immediate: true });

watch(() => props.filters, (newFilters) => {
    filters.value = newFilters || {};
}, { immediate: true });

watch(() => props.chapters, (newChapters) => {
    chapters.value = newChapters || [];
}, { immediate: true });


const blogTitle = ref('');
const blogDescription = ref('');
const blogCategory = ref('');
const titleError = ref(false);
const descriptionError = ref(false);

// Helper to get question preview
const getQuestionPreview = (questionId) => {
    const question = questions.value.find(q => q.id === questionId);
    if (question) {
        // Get first 50 characters of question text
        const preview = question.question_text.replace(/<[^>]*>/g, '').substring(0, 50);
        return preview + (preview.length === 50 ? '...' : '');
    }
    return 'Question';
};

const clearSelectedQuestions = () => {
    selectedQuestions.value = [];
    isSelectAll.value = false;
};

const saveQuestions = () => {
    // Validate blog information
    titleError.value = !blogTitle.value.trim();
    descriptionError.value = !blogDescription.value.trim();
    
    if (titleError.value || descriptionError.value) {
        alert('অনুগ্রহ করে ব্লগের শিরোনাম এবং বিবরণ প্রদান করুন');
        return;
    }
    
    if (selectedQuestions.value.length === 0) {
        alert('অনুগ্রহ করে কমপক্ষে একটি প্রশ্ন নির্বাচন করুন');
        return;
    }

    router.post(route('save.questions.blog'), {
        question_ids: selectedQuestions.value,
        exam_name: examName.value,
        blog_title: blogTitle.value,
        blog_description: blogDescription.value,
        blog_category: blogCategory.value,
    }, {
        preserveScroll: true,
        onSuccess: () => {
            // Reset form after successful save
            selectedQuestions.value = [];
            isSelectAll.value = false;
            blogTitle.value = '';
            blogCategory.value = '';
            blogDescription.value = '';
            titleError.value = false;
            descriptionError.value = false;
            categoryError.value = false;
            
            // Optional: Show success message
            alert('ব্লগ সফলভাবে সংরক্ষণ করা হয়েছে!');
        }
    });
};

</script>