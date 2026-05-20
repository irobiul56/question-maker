<script setup>
import { onMounted, ref, watch, nextTick } from 'vue';

const props = defineProps({
  content: String
});

const container = ref(null);
const isReady = ref(false);
const renderedHtml = ref('');

const getRenderedContent = () => {
  if (!container.value) return props.content || '';
  return container.value.innerHTML;
};

const renderMath = async () => {
  if (!container.value || !props.content) return;
  
  if (window.MathJax && window.MathJax.typesetPromise) {
    try {
      await window.MathJax.typesetPromise([container.value]);
      await nextTick();
      // Store the rendered HTML
      renderedHtml.value = container.value.innerHTML;
      isReady.value = true;
    } catch (err) {
      console.error('MathJax error:', err);
      renderedHtml.value = props.content;
      isReady.value = true;
    }
  } else {
    renderedHtml.value = props.content;
    isReady.value = true;
  }
};

defineExpose({ getRenderedContent, renderMath, renderedHtml });

onMounted(async () => {
  if (window.MathJaxLoaded) {
    await renderMath();
  } else {
    window.addEventListener('mathjax-loaded', async () => {
      await renderMath();
    });
  }
});

watch(() => props.content, async () => {
  isReady.value = false;
  await nextTick();
  await renderMath();
});
</script>

<template>
  <div 
    ref="container" 
    v-html="content"
    class="latex-renderer"
  ></div>
</template>