<script setup>
import { onMounted, onUpdated, ref, watch } from 'vue';

const props = defineProps({
  content: String
});

const container = ref(null);
const mathjaxReady = ref(false);

// Check if MathJax is loaded and ready
const checkMathJax = () => {
  if (window.MathJax && window.MathJaxLoaded) {
    mathjaxReady.value = true;
    return true;
  }
  return false;
};

// Render function
const renderMathJax = () => {
  if (!container.value || !props.content) return;
  
  if (checkMathJax()) {
    // Use TypesetPromise to ensure proper rendering
    window.MathJax.typesetPromise([container.value]).catch(err => {
      console.error('MathJax typeset error:', err);
    });
  }
};

// Initial setup
onMounted(() => {
  if (checkMathJax()) {
    renderMathJax();
  } else {
    // Listen for MathJax loaded event if not ready yet
    document.addEventListener('mathjax-loaded', () => {
      mathjaxReady.value = true;
      renderMathJax();
    });
  }
});

// Update when content changes
watch(() => props.content, renderMathJax);

// Also run on updates
onUpdated(renderMathJax);
</script>

<template>
  <div ref="container" v-html="content"></div>
</template>