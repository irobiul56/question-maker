<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Left column - Tree Structure -->
                <div class="lg:col-span-1 bg-white rounded-xl shadow-sm overflow-hidden">
                    <div class="p-4 border-b border-gray-200 flex justify-between items-center">
                        <h3 class="text-lg font-medium text-gray-900">Curriculum Structure</h3>
                        <div class="flex space-x-2">
                            <el-button @click="toggleExpandAll(true)" size="small" plain>
                                Expand All
                            </el-button>
                            <el-button @click="toggleExpandAll(false)" size="small" plain>
                                Collapse All
                            </el-button>
                        </div>
                    </div>
                    <div class="overflow-y-auto" style="max-height: calc(100vh - 300px)">
                        <ul class="divide-y divide-gray-200">
                            <li v-for="item in flattenedData" :key="item.id" 
                                class="px-4 py-3 hover:bg-gray-50 transition-colors duration-150"
                                :class="{
                                    'bg-blue-50': item.type === 'class',
                                    'bg-green-50': item.type === 'subject',
                                    'bg-purple-50': item.type === 'chapter',
                                    'bg-white': item.type === 'topic'
                                }">
                                <div class="flex items-center" :style="{ 'padding-left': `${item.indentLevel * 1.5}rem` }">
                                    <button v-if="['class', 'subject', 'chapter'].includes(item.type)" 
                                        @click="toggleExpand(item)"
                                        class="mr-2 text-gray-500 hover:text-gray-700">
                                        <svg class="w-4 h-4 transition-transform duration-200" 
                                            :class="{ 'transform rotate-90': item.isExpanded }" 
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </button>
                                    <span class="font-medium">
                                        {{ item.name }}
                                        <span v-if="item.type === 'topic'" 
                                            class="ml-2 text-xs text-gray-500">
                                            ({{ questions.filter(q => q.topic_id === item.id).length }} questions)
                                        </span>
                                    </span>
                                    <div v-if="item.type === 'topic'" class="ml-auto flex space-x-2">
                                        <button @click.stop="openQuestionModal(item.id)"
                                            class="text-blue-600 hover:text-blue-800 p-1 rounded-full hover:bg-blue-100">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                            </svg>
                                        </button>
                                        <button @click.stop="editTopic(item)"
                                            class="text-yellow-600 hover:text-yellow-800 p-1 rounded-full hover:bg-yellow-100">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </button>
                                        <button @click.stop="deleteTopic(item.id)"
                                            class="text-red-600 hover:text-red-800 p-1 rounded-full hover:bg-red-100">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Right column - Questions List -->
                <div class="lg:col-span-2 bg-white rounded-xl shadow-sm overflow-hidden">
                    <div class="p-4 border-b border-gray-200 flex justify-between items-center">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">Questions</h3>
                            <p class="text-sm text-gray-500 mt-1">
                                Showing {{ filteredQuestions.length }} questions for selected filters
                            </p>
                        </div>
                        <div class="flex items-center space-x-4">
                            <span class="text-sm text-gray-500">
                                Page {{ currentPage }} of {{ totalQuestionPages }}
                            </span>
                            <div class="flex space-x-2">
                                <el-button @click="prevPage" :disabled="currentPage <= 1" size="small">
                                    Previous
                                </el-button>
                                <el-button @click="nextPage" :disabled="currentPage >= totalQuestionPages" size="small">
                                    Next
                                </el-button>
                            </div>
                        </div>
                    </div>
                    <div class="overflow-y-auto" style="max-height: calc(100vh - 300px)">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        #
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Question
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Type
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <template v-if="paginatedQuestions.length === 0">
                                    <tr>
                                        <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">
                                            No questions found matching your criteria.
                                        </td>
                                    </tr>
                                </template>
                                <template v-else>
                                    <tr v-for="(question, index) in paginatedQuestions" :key="question.id" class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ (currentPage - 1) * itemsPerPage + index + 1 }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900">
                                            <div class="font-medium">{{ question.question_text }}</div>
                                            <div v-if="question.explanation" class="text-xs text-gray-500 mt-1">
                                                Explanation: {{ question.explanation }}
                                            </div>
                                            <div v-if="question.options && question.options.length > 0" class="mt-2">
                                                <div v-for="(option, optIndex) in question.options" :key="optIndex" 
                                                    class="text-xs flex items-center">
                                                    <span class="mr-2">{{ String.fromCharCode(97 + optIndex) }}.</span>
                                                    <span :class="{ 'font-semibold text-green-600': option.is_correct }">
                                                        {{ option.option_text }}
                                                    </span>
                                                    <span v-if="option.is_correct" class="ml-1 text-green-500">
                                                        ✓
                                                    </span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <span :class="{
                                                'px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800': question.format === 'cq',
                                                'px-2 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800': question.format === 'mcq',
                                                'px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800': question.format === 'mix'
                                            }">
                                                {{ question.format.toUpperCase() }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex items-center justify-end space-x-2">
                                                <button @click="editQuestion(question)"
                                                    class="text-blue-600 hover:text-blue-900 p-1 rounded-full hover:bg-blue-50 transition duration-200"
                                                    title="Edit">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                        </path>
                                                    </svg>
                                                </button>
                                                <button @click="deleteQuestion(question.id)"
                                                    class="text-red-600 hover:text-red-900 p-1 rounded-full hover:bg-red-50 transition duration-200"
                                                    title="Delete">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                        </path>
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>