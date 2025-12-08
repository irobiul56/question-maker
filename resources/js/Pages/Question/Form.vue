<script setup>
import { ref, computed, watch } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";

const props = defineProps({
  question: Object,
  classes: Array,
  subjects: Array,
  chapters: Array,
  topics: Array,
  types: Array,
  levels: Array,
  boards: Array
});

const form = useForm({
  id: props.question?.id || null,
  class_id: props.question?.academic_classes_id ? Number(props.question.academic_classes_id) : null,
  subject_id: props.question?.subject_id ? Number(props.question.subject_id) : null,
  chapter_id: props.question?.chapter_id ? Number(props.question.chapter_id) : null,
  topic_id: props.question?.topic_id ? Number(props.question.topic_id) : null,
  type_id: props.question?.type?.map(t => Number(t.id)) || [],
  level_id: props.question?.level_id ? Number(props.question.level_id) : null,
  board_id: props.question?.board_id ? Number(props.question.board_id) : null,
  format: props.question?.format || 'mcq',
  question_text: props.question?.question_text || '',
  explanation: props.question?.explanation || '',
  mark: props.question?.mark || '',

  // Options with proper boolean handling for database values (1/0 or true/false)
  options: props.question?.options?.map(opt => ({
    id: opt.id || null, // Include if you need to track existing options
    option_text: opt.option_text || '',
    is_correct: opt.is_correct === true || opt.is_correct === 1 || opt.is_correct === '1'
  })) || Array(4).fill().map(() => ({
    option_text: '',
    is_correct: false
  })),

  // CQ questions
  cq: props.question?.cqoptions?.map(cq => ({
    id: cq.id || null, // Include if you need to track existing CQs
    cq_text: cq.cq_text || '',
    mark: cq.mark || ''
  })) || Array(4).fill().map(() => ({
    cq_text: '',
    mark: ''
  }))
});

// Watch for debugging (remove in production)
watch(() => form.options, (newOptions) => {
  console.log('Options updated:', newOptions.map(o => ({
    text: o.option_text,
    is_correct: o.is_correct,
    type: typeof o.is_correct
  })));
}, { deep: true });


// Filtered hierarchical options
const filteredSubjects = computed(() => {
  return form.class_id ? 
    props.subjects.filter(s => s.academic_classes_id == form.class_id) : 
    [];
});

const filteredChapters = computed(() => {
  return form.subject_id ? 
    props.chapters.filter(c => c.subject_id == form.subject_id) : 
    [];
});

const filteredTopics = computed(() => {
  return form.chapter_id ? 
    props.topics.filter(t => t.chapter_id == form.chapter_id) : 
    [];
});

// Watchers to clear downstream selections
watch(() => form.class_id, (newVal) => {
  if (!newVal) {
    form.subject_id = '';
    form.chapter_id = '';
    form.topic_id = '';
  } else {
    form.subject_id = '';
  }
});

watch(() => form.subject_id, (newVal) => {
  if (!newVal) {
    form.chapter_id = '';
    form.topic_id = '';
  } else {
    form.chapter_id = '';
  }
});

watch(() => form.chapter_id, (newVal) => {
  if (!newVal) {
    form.topic_id = '';
  }
});

// Form submission
const submitQuestion = () => {
  const url = form.id ? 
    route('question.update', form.id) : 
    route('question.store');

  form[form.id ? 'put' : 'post'](url, {
    preserveScroll: true,
    onSuccess: () => {
      router.visit(route('question.index'));
    }
  });
};

// Helper methods
const addOption = () => form.options.push({ option_text: '', is_correct: false });
const removeOption = (index) => form.options.splice(index, 1);
const addCq = () => form.cq.push({ cq_text: '', mark: '' });
const removeCq = (index) => form.cq.splice(index, 1);
</script>

<template>
  <AuthenticatedLayout>
    <Head :title="form.id ? 'Edit Question' : 'Create Question'" />

    <div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 py-10 px-4 sm:px-6 lg:px-8">
      <div class="max-w-5xl mx-auto">
        <!-- Card Container -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
          <!-- Header with Gradient Background -->
          <div class="bg-gradient-to-r from-indigo-600 to-blue-500 p-6 text-white">
            <h1 class="text-2xl font-bold">
              {{ form.id ? 'Edit Question' : 'Create New Question' }}
            </h1>
            <p class="text-blue-100 mt-1">
              {{ form.id ? 'Update your question details' : 'Fill in the form to create a new question' }}
            </p>
          </div>

          <!-- Form Content -->
          <div class="p-6 md:p-8">
            <form @submit.prevent="submitQuestion" class="space-y-6">
              <!-- Hierarchy Selection Cards -->
              <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Class Card -->
                <div class="bg-white rounded-lg border border-gray-200 p-4 shadow-sm hover:shadow-md transition-shadow">
                  <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <span class="text-indigo-600">1.</span> Select Class
                  </label>
                  <el-select
                    v-model="form.class_id"
                    placeholder="Choose Class"
                    class="w-full"
                    clearable
                    filterable
		    :loading="isLoading"
                  >
                    <el-option
                      v-for="cls in props.classes"
                      :key="cls.id"
                      :label="cls.name"
                      :value="Number(cls.id)"
                    />
                  </el-select>
                  <div v-if="form.errors.class_id" class="text-red-500 text-xs mt-1">
                    {{ form.errors.class_id }}
                  </div>
                </div>

                <!-- Subject Card -->
                <div 
                  class="bg-white rounded-lg border border-gray-200 p-4 shadow-sm hover:shadow-md transition-shadow"
                  :class="{ 'opacity-70': !form.class_id }"
                >
                  <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <span class="text-indigo-600">2.</span> Select Subject
                  </label>
                  <el-select
                    v-model="form.subject_id"
                    placeholder="Choose Subject"
                    class="w-full"
                    :disabled="!form.class_id"
                    clearable
                    filterable
		    :loading="isLoading"
                  >
                    <el-option
                      v-for="subject in filteredSubjects"
                      :key="subject.id"
                      :label="subject.name"
                      :value="Number(subject.id)"
                    />
                  </el-select>
                  <div v-if="form.errors.subject_id" class="text-red-500 text-xs mt-1">
                    {{ form.errors.subject_id }}
                  </div>
                </div>

                <!-- Chapter Card -->
                <div 
                  class="bg-white rounded-lg border border-gray-200 p-4 shadow-sm hover:shadow-md transition-shadow"
                  :class="{ 'opacity-70': !form.subject_id }"
                >
                  <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <span class="text-indigo-600">3.</span> Select Chapter
                  </label>
                  <el-select
                    v-model="form.chapter_id"
                    placeholder="Choose Chapter"
                    class="w-full"
                    :disabled="!form.subject_id"
                    clearable
                    filterable
		    :loading="isLoading"
                  >
                    <el-option
                      v-for="chapter in filteredChapters"
                      :key="chapter.id"
                      :label="chapter.name"
                      :value="Number(chapter.id)"
                    />
                  </el-select>
                  <div v-if="form.errors.chapter_id" class="text-red-500 text-xs mt-1">
                    {{ form.errors.chapter_id }}
                  </div>
                </div>

                <!-- Topic Card -->
                <div 
                  class="bg-white rounded-lg border border-gray-200 p-4 shadow-sm hover:shadow-md transition-shadow"
                  :class="{ 'opacity-70': !form.chapter_id }"
                >
                  <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <span class="text-indigo-600">4.</span> Select Topic
                  </label>
                  <el-select
                    v-model="form.topic_id"
                    placeholder="Choose Topic"
                    class="w-full"
                    :disabled="!form.chapter_id"
                    clearable
                    filterable
                  >
                    <el-option
                      v-for="topic in filteredTopics"
                      :key="topic.id"
                      :label="topic.name"
                      :value="Number(topic.id)"
                    />
                  </el-select>
                  <div v-if="form.errors.topic_id" class="text-red-500 text-xs mt-1">
                    {{ form.errors.topic_id }}
                  </div>
                </div>
              </div>

              <!-- Question Details Card -->
              <div class="bg-white rounded-lg border border-gray-200 p-6 shadow-sm">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                  <span class="bg-indigo-100 text-indigo-800 rounded-full w-6 h-6 flex items-center justify-center mr-2">5</span>
                  Question Details
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <!-- Question Type -->
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Question Type</label>
                    <el-select
                      v-model="form.type_id"
                      placeholder="Select Type"
                      class="w-full"
                      multiple
                      filterable
                    >
                      <el-option
                        v-for="type in props.types"
                        :key="type.id"
                        :label="type.name"
                        :value="Number(type.id)"
                      />
                    </el-select>
                    <div v-if="form.errors.type_id" class="text-red-500 text-xs mt-1">
                      {{ form.errors.type_id }}
                    </div>
                  </div>

                  <!-- Difficulty Level -->
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Difficulty Level</label>
                    <el-select
                      v-model="form.level_id"
                      placeholder="Select Level"
                      class="w-full"
                    >
                      <el-option
                        v-for="level in props.levels"
                        :key="level.id"
                        :label="level.name"
                        :value="Number(level.id)"
                      />
                    </el-select>
                    <div v-if="form.errors.level_id" class="text-red-500 text-xs mt-1">
                      {{ form.errors.level_id }}
                    </div>
                  </div>

                  <!-- Board -->
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Board</label>
                    <el-select
                      v-model="form.board_id"
                      placeholder="Select Board"
                      class="w-full"
                    >
                      <el-option
                        v-for="board in props.boards"
                        :key="board.id"
                        :label="board.name"
                        :value="Number(board.id)"
                      />
                    </el-select>
                    <div v-if="form.errors.board_id" class="text-red-500 text-xs mt-1">
                      {{ form.errors.board_id }}
                    </div>
                  </div>

                  <!-- Format -->
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Format</label>
                    <el-radio-group v-model="form.format" class="w-full">
                      <el-radio-button label="mcq" class="flex-1">MCQ</el-radio-button>
                      <el-radio-button label="cq" class="flex-1">CQ</el-radio-button>
                      <el-radio-button label="mix" class="flex-1">Mixed</el-radio-button>
                    </el-radio-group>
                    <div v-if="form.errors.format" class="text-red-500 text-xs mt-1">
                      {{ form.errors.format }}
                    </div>
                  </div>
                </div>
              </div>

              <!-- Question Content Card -->
<div class="bg-white rounded-xl border border-gray-200 shadow-lg overflow-hidden">
  <!-- Card Header with Gradient Background -->
  <div class="bg-gradient-to-r from-indigo-500 to-blue-600 px-6 py-4">
    <div class="flex items-center">
      <span class="bg-white text-indigo-600 rounded-full w-8 h-8 flex items-center justify-center mr-3 font-bold shadow-sm">6</span>
      <h3 class="text-xl font-bold text-white">Question Content</h3>
    </div>
  </div>

  <!-- Card Body -->
  <div class="p-6 space-y-6">
    <!-- Marks Input (for Mix format) -->
    <div v-if="['mix'].includes(form.format)" class="bg-blue-50 rounded-lg p-4 border border-blue-100">
      <label class="block text-sm font-semibold text-blue-800 mb-2">Total Marks</label>
      <div class="relative">
        <el-input
          v-model="form.mark"
          type="number"
          placeholder="Enter total marks"
          class="w-full"
          :class="{ 'border-red-300': form.errors.mark }"
        >
          <template #prefix>
            <span class="text-gray-400">📝</span>
          </template>
        </el-input>
        <div v-if="form.errors.mark" class="absolute -bottom-5 left-0 text-red-500 text-xs">
          {{ form.errors.mark }}
        </div>
      </div>
    </div>

    <!-- Question Text -->
    <div class="bg-gray-50 rounded-lg p-4 border border-gray-100 hover:border-blue-200 transition-colors">
      <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
        <span class="bg-blue-100 text-blue-800 rounded-full w-5 h-5 flex items-center justify-center mr-2 text-xs">Q</span>
        Question Text
      </label>
      <div class="relative">
        <el-input
          v-model="form.question_text"
          type="textarea"
          :rows="4"
          placeholder="Enter your question here..."
          class="w-full hover:shadow-sm transition-shadow"
          :class="{ 'border-red-300': form.errors.question_text }"
        />
        <div v-if="form.errors.question_text" class="absolute -bottom-5 left-0 text-red-500 text-xs">
          {{ form.errors.question_text }}
        </div>
      </div>
    </div>

    <!-- Explanation -->
    <div class="bg-gray-50 rounded-lg p-4 border border-gray-100 hover:border-blue-200 transition-colors">
      <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
        <span class="bg-purple-100 text-purple-800 rounded-full w-5 h-5 flex items-center justify-center mr-2 text-xs">💡</span>
        Explanation (Optional)
      </label>
      <el-input
        v-model="form.explanation"
        type="textarea"
        :rows="3"
        placeholder="Add explanation if needed..."
        class="w-full hover:shadow-sm transition-shadow"
      />
    </div>

    <!-- MCQ Options -->
    <div v-if="['mcq'].includes(form.format)" class="bg-blue-50 rounded-lg p-4 border border-blue-100">
      <div class="flex justify-between items-center mb-4">
        <label class="block text-sm font-semibold text-blue-800 flex items-center">
          <span class="bg-white text-blue-600 rounded-full w-5 h-5 flex items-center justify-center mr-2 text-xs">A</span>
          MCQ Options
        </label>
        <el-button 
          @click="addOption" 
          size="small" 
          type="primary" 
          class="shadow-sm hover:shadow-md transition-all"
        >
          <span class="flex items-center">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Add Option
          </span>
        </el-button>
      </div>
      
      <div v-for="(option, index) in form.options" :key="index" class="flex items-center gap-3 mb-3 group">
        <div class="flex-1 relative flex items-center">
          <span class="absolute left-3 text-gray-500 font-medium">
            {{ String.fromCharCode(65 + index) }}.
          </span>
          <el-input
            v-model="option.option_text"
            placeholder="Option text"
            class="w-full pl-8 hover:shadow-sm transition-shadow"
            :class="{ 'border-red-300': form.errors.options && form.errors.options[index] }"
          />
        </div>
       <el-checkbox 
  v-model="option.is_correct"
  class="ml-2"
  :class="{ 
    'ring-2 ring-green-500': option.is_correct,
    'border-gray-300': !option.is_correct
  }"
>
  <span class="text-sm text-gray-600">Correct</span>
</el-checkbox>

        <el-button
          @click="removeOption(index)"
          type="danger"
          plain
          size="small"
          :disabled="form.options.length <= 2"
          class="opacity-0 group-hover:opacity-100 transition-opacity"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
          </svg>
        </el-button>
      </div>
      <div v-if="form.errors.options" class="text-red-500 text-xs mt-1">
        {{ form.errors.options }}
      </div>
    </div>

    <!-- CQ Questions -->
    <div v-if="['cq'].includes(form.format)" class="bg-green-50 rounded-lg p-4 border border-green-100">
      <div class="flex justify-between items-center mb-4">
        <label class="block text-sm font-semibold text-green-800 flex items-center">
          <span class="bg-white text-green-600 rounded-full w-5 h-5 flex items-center justify-center mr-2 text-xs">Q</span>
          Constructed Questions
        </label>
        <el-button 
          @click="addCq" 
          size="small" 
          type="success" 
          class="shadow-sm hover:shadow-md transition-all"
        >
          <span class="flex items-center">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Add Question
          </span>
        </el-button>
      </div>
      
      <div v-for="(cq, index) in form.cq" :key="index" class="flex items-center gap-3 mb-3 group">
        <div class="flex-1 relative flex items-center">
          <span class="absolute left-3 text-gray-500 font-medium">
            Q{{ index + 1 }}.
          </span>
          <el-input
            v-model="cq.cq_text"
            placeholder="Question text"
            class="w-full pl-8 hover:shadow-sm transition-shadow"
            :class="{ 'border-red-300': form.errors.cq && form.errors.cq[index] }"
          />
        </div>
        <div class="relative w-24">
          <el-input
            v-model="cq.mark"
            placeholder="Marks"
            type="number"
            class="hover:shadow-sm transition-shadow"
          />
          <span class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-xs">pts</span>
        </div>
        <el-button
          @click="removeCq(index)"
          type="danger"
          plain
          size="small"
          :disabled="form.cq.length <= 2"
          class="opacity-0 group-hover:opacity-100 transition-opacity"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
          </svg>
        </el-button>
      </div>
      <div v-if="form.errors.cq" class="text-red-500 text-xs mt-1">
        {{ form.errors.cq }}
      </div>
    </div>
  </div>

  <!-- Card Footer with Actions -->
  <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex justify-end gap-4">
    <el-button 
      @click="router.visit(route('questions.index'))" 
      :disabled="form.processing"
      class="px-6 py-2 border border-gray-300 rounded-lg shadow-sm hover:bg-gray-100 transition-colors hover:-translate-y-0.5"
    >
      Cancel
    </el-button>
    <el-button 
      type="primary" 
      native-type="submit"
      :loading="form.processing"
      class="px-6 py-2 bg-gradient-to-r from-indigo-600 to-blue-500 hover:from-indigo-700 hover:to-blue-600 rounded-lg shadow-md text-white transition-all hover:-translate-y-0.5 hover:shadow-lg"
    >
      <span class="font-medium">
        {{ form.id ? 'Update Question' : 'Create Question' }}
      </span>
      <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
      </svg>
    </el-button>
  </div>
</div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<style>
/* Custom select styling */
.el-select {
  --el-select-input-focus-border-color: theme('colors.indigo.500');
  --el-select-border-color-hover: theme('colors.indigo.400');
}

.el-select .el-input__inner {
  @apply py-2 pl-3 pr-8 border-gray-300 rounded-lg shadow-sm;
}

.el-select .el-input__wrapper {
  @apply shadow-sm hover:shadow-md transition-shadow;
}

/* Custom checkbox styling */
.el-checkbox {
  @apply mr-0;
}

.el-checkbox__input.is-checked .el-checkbox__inner {
  @apply bg-indigo-600 border-indigo-600;
}

.el-checkbox__label {
  @apply text-sm text-gray-600;
}

/* Custom radio button styling */
.el-radio-button__inner {
  @apply px-4 py-2;
}

.el-radio-button__orig-radio:checked + .el-radio-button__inner {
  @apply bg-indigo-600 border-indigo-600 text-white shadow-md;
}

/* Custom textarea styling */
.el-textarea__inner {
  @apply py-2 px-3 border-gray-300 rounded-lg shadow-sm hover:shadow-md transition-shadow;
}

/* Custom button styling */
.el-button {
  @apply transition-all duration-200;
}

.el-button--primary {
  @apply bg-gradient-to-r from-indigo-600 to-blue-500 border-transparent hover:from-indigo-700 hover:to-blue-600;
}

.el-button--danger {
  @apply bg-red-100 text-red-600 border-red-200 hover:bg-red-200;
}
</style>