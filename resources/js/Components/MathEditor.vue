<template>
  <div class="editor-container">
    <div class="toolbar">
      <!-- Formatting Buttons -->
      <button @click="formatText('bold')" title="Bold"><b>B</b></button>
      <button @click="formatText('italic')" title="Italic"><i>I</i></button>
      <button @click="formatText('underline')" title="Underline"><u>U</u></button>
      <button @click="formatText('strikeThrough')" title="Strikethrough"><s>S</s></button>
      
      <!-- Text Alignment -->
      <select @change="formatText('justifyLeft', $event.target.value)" title="Alignment">
        <option value="">Align</option>
        <option value="justifyLeft">Left</option>
        <option value="justifyCenter">Center</option>
        <option value="justifyRight">Right</option>
        <option value="justifyFull">Justify</option>
      </select>
      
      <!-- Lists -->
      <button @click="formatText('insertUnorderedList')" title="Bullet List">•</button>
      <button @click="formatText('insertOrderedList')" title="Numbered List">1.</button>
      
      <!-- Headings -->
      <select @change="formatHeading($event.target.value)" title="Heading">
        <option value="">Paragraph</option>
        <option value="h1">Heading 1</option>
        <option value="h2">Heading 2</option>
        <option value="h3">Heading 3</option>
      </select>
      
      <!-- Insert Tools -->
      <button @click="insertLink" title="Insert Link">🔗</button>
      <button @click="insertImage" title="Insert Image">🖼️</button>
      <button @click="showTableDialog = true" title="Insert Table">📊</button>
      <button @click="showMathDialog = true" title="Insert Math">Σ</button>
      <button @click="insertHorizontalRule" title="Horizontal Line">―</button>
      
      <!-- Text Color -->
      <input type="color" @input="formatText('foreColor', $event.target.value)" title="Text Color">
      
      <!-- Clear Formatting -->
      <button @click="clearFormatting" title="Clear Formatting">✖</button>
    </div>
    
    <div 
      ref="editor" 
      contenteditable="true" 
      class="editor-content"
      @input="updateContent"
      @paste="handlePaste"
      @keydown="handleKeyDown"
      @mouseup="saveSelection"
      @focus="saveSelection"
    ></div>
    
    <!-- Math Dialog -->
    <div v-if="showMathDialog" class="dialog-overlay" @click.self="showMathDialog = false">
      <div class="dialog-content">
        <h3>Insert Math Formula</h3>
        <textarea 
          v-model="mathExpression" 
          placeholder="E.g. x = \frac{-b \pm \sqrt{b^2-4ac}}{2a}" 
          ref="mathInput"
          @keydown.enter.prevent="insertMathExpression"
        ></textarea>
        
        <div class="preview" v-html="mathPreview"></div>
        
        <div class="math-shortcuts">
          <button v-for="(symbol, index) in mathSymbols" :key="index" @click="insertMathShortcut(symbol)">
            {{ symbol.symbol }}
          </button>
        </div>
        
        <div class="dialog-actions">
          <button @click="showMathDialog = false" class="btn-cancel">Cancel</button>
          <button @click="insertMathExpression" class="btn-insert">Insert</button>
        </div>
      </div>
    </div>

    <!-- Table Dialog -->
    <div v-if="showTableDialog" class="dialog-overlay" @click.self="showTableDialog = false">
      <div class="dialog-content">
        <h3>Insert Table</h3>
        <div class="form-group">
          <label>Rows:</label>
          <input type="number" v-model.number="tableRows" min="1" max="20" class="form-input">
        </div>
        <div class="form-group">
          <label>Columns:</label>
          <input type="number" v-model.number="tableCols" min="1" max="10" class="form-input">
        </div>
        <div class="form-group">
          <label>Border:</label>
          <input type="number" v-model.number="tableBorder" min="0" max="5" class="form-input">
        </div>
        
        <div class="dialog-actions">
          <button @click="showTableDialog = false" class="btn-cancel">Cancel</button>
          <button @click="insertTable" class="btn-insert">Insert</button>
        </div>
      </div>
    </div>

    <!-- Link Dialog -->
    <div v-if="showLinkDialog" class="dialog-overlay" @click.self="showLinkDialog = false">
      <div class="dialog-content">
        <h3>Insert Link</h3>
        <div class="form-group">
          <label>URL:</label>
          <input type="url" v-model="linkUrl" placeholder="https://example.com" class="form-input">
        </div>
        <div class="form-group">
          <label>Text (optional):</label>
          <input type="text" v-model="linkText" placeholder="Link text" class="form-input">
        </div>
        
        <div class="dialog-actions">
          <button @click="showLinkDialog = false" class="btn-cancel">Cancel</button>
          <button @click="insertLinkElement" class="btn-insert">Insert</button>
        </div>
      </div>
    </div>

    <!-- Image Dialog -->
    <div v-if="showImageDialog" class="dialog-overlay" @click.self="showImageDialog = false">
      <div class="dialog-content">
        <h3>Insert Image</h3>
        <div class="form-group">
          <label>Image URL:</label>
          <input type="url" v-model="imageUrl" placeholder="https://example.com/image.jpg" class="form-input">
        </div>
        <div class="form-group">
          <label>Alt Text:</label>
          <input type="text" v-model="imageAlt" placeholder="Description" class="form-input">
        </div>
        <div class="form-group">
          <label>Width (px):</label>
          <input type="number" v-model.number="imageWidth" min="50" max="1000" class="form-input">
        </div>
        
        <div class="dialog-actions">
          <button @click="showImageDialog = false" class="btn-cancel">Cancel</button>
          <button @click="insertImageElement" class="btn-insert">Insert</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, nextTick, onMounted } from 'vue';
import katex from 'katex';
import 'katex/dist/katex.min.css';

const props = defineProps({
  modelValue: {
    type: String,
    default: ''
  }
});

const emit = defineEmits(['update:modelValue']);

// Editor Refs
const editor = ref(null);
let savedSelection = null;

// Dialog States
const showMathDialog = ref(false);
const showTableDialog = ref(false);
const showLinkDialog = ref(false);
const showImageDialog = ref(false);

// Math Dialog
const mathExpression = ref('');
const mathInput = ref(null);
const mathSymbols = ref([
  { symbol: 'α', latex: '\\alpha' },
  { symbol: 'β', latex: '\\beta' },
  { symbol: 'γ', latex: '\\gamma' },
  { symbol: '∑', latex: '\\sum' },
  { symbol: '∫', latex: '\\int' },
  { symbol: '√', latex: '\\sqrt{}', cursorPos: 6 },
  { symbol: '½', latex: '\\frac{}{}', cursorPos: 5 },
  { symbol: 'x²', latex: '^{}', cursorPos: 2 },
  { symbol: 'xₙ', latex: '_{}', cursorPos: 2 },
  { symbol: '≠', latex: '\\neq' },
  { symbol: '≈', latex: '\\approx' },
  { symbol: '∞', latex: '\\infty' }
]);

// Table Dialog
const tableRows = ref(3);
const tableCols = ref(3);
const tableBorder = ref(1);

// Link Dialog
const linkUrl = ref('');
const linkText = ref('');

// Image Dialog
const imageUrl = ref('');
const imageAlt = ref('');
const imageWidth = ref(300);

// Initialize editor content
onMounted(() => {
  if (props.modelValue) {
    editor.value.innerHTML = props.modelValue;
  }
  editor.value.focus();
});

// Save current selection
const saveSelection = () => {
  const selection = window.getSelection();
  if (selection.rangeCount > 0) {
    savedSelection = selection.getRangeAt(0);
  }
};

// Restore saved selection
const restoreSelection = () => {
  if (savedSelection) {
    const selection = window.getSelection();
    selection.removeAllRanges();
    selection.addRange(savedSelection);
  }
};

// Math Preview
const mathPreview = computed(() => {
  if (!mathExpression.value) return '<div class="placeholder">Enter LaTeX to see preview</div>';
  
  try {
    return katex.renderToString(mathExpression.value, {
      throwOnError: false,
      displayMode: true
    });
  } catch (e) {
    return `<div class="error">Invalid LaTeX: ${e.message}</div>`;
  }
});

// Update content when modelValue changes
watch(() => props.modelValue, (newValue) => {
  if (editor.value && editor.value.innerHTML !== newValue) {
    editor.value.innerHTML = newValue;
  }
});

const updateContent = () => {
  emit('update:modelValue', editor.value.innerHTML);
};

// Formatting functions
const formatText = (command, value = null) => {
  restoreSelection();
  document.execCommand(command, false, value);
  editor.value.focus();
  updateContent();
};

const formatHeading = (heading) => {
  if (!heading) {
    formatText('formatBlock', '<p>');
  } else {
    formatText('formatBlock', `<${heading}>`);
  }
};

const clearFormatting = () => {
  formatText('removeFormat');
  formatText('unlink');
};

// Math functions
const insertMath = () => {
  showMathDialog.value = true;
  mathExpression.value = '';
  
  nextTick(() => {
    mathInput.value.focus();
  });
};

const insertMathShortcut = (symbol) => {
  if (symbol.cursorPos) {
    const cursorPos = mathInput.value.selectionStart;
    const before = mathExpression.value.substring(0, cursorPos);
    const after = mathExpression.value.substring(cursorPos);
    mathExpression.value = before + symbol.latex + after;
    
    nextTick(() => {
      mathInput.value.setSelectionRange(cursorPos + symbol.cursorPos, cursorPos + symbol.cursorPos);
    });
  } else {
    mathExpression.value += symbol.latex;
  }
};

const insertMathExpression = () => {
  if (!mathExpression.value.trim()) {
    showMathDialog.value = false;
    return;
  }

  try {
    restoreSelection();
    
    const mathContainer = document.createElement('span');
    mathContainer.className = 'math-container';
    mathContainer.contentEditable = 'false';
    mathContainer.innerHTML = katex.renderToString(mathExpression.value);
    
    // Insert at current position
    if (savedSelection) {
      savedSelection.deleteContents();
      savedSelection.insertNode(mathContainer);
      
      // Add space after for better editing
      const space = document.createTextNode(' ');
      savedSelection.insertNode(space);
      
      // Move cursor after the space
      savedSelection.setStartAfter(space);
      savedSelection.collapse(true);
    }
    
    showMathDialog.value = false;
    updateContent();
    editor.value.focus();
  } catch (error) {
    console.error('Error rendering math:', error);
  }
};

// Table functions
const insertTable = () => {
  restoreSelection();
  
  const table = document.createElement('table');
  table.className = 'editor-table';
  table.style.border = `${tableBorder.value}px solid #ddd`;
  table.style.borderCollapse = 'collapse';
  table.style.width = '100%';
  table.style.margin = '12px 0';

  for (let i = 0; i < tableRows.value; i++) {
    const row = table.insertRow();
    for (let j = 0; j < tableCols.value; j++) {
      const cell = row.insertCell();
      cell.contentEditable = 'true';
      cell.style.border = `${tableBorder.value}px solid #ddd`;
      cell.style.padding = '8px';
      cell.style.minWidth = '30px';
      
      // Add empty paragraph to make cell editable
      const p = document.createElement('p');
      p.innerHTML = '<br>';
      cell.appendChild(p);
    }
  }

  if (savedSelection) {
    savedSelection.deleteContents();
    savedSelection.insertNode(table);
    
    // Focus first cell
    const firstCell = table.rows[0].cells[0];
    const range = document.createRange();
    range.selectNodeContents(firstCell);
    range.collapse(true);
    
    const selection = window.getSelection();
    selection.removeAllRanges();
    selection.addRange(range);
  }

  showTableDialog.value = false;
  updateContent();
  editor.value.focus();
};

// Link functions
const insertLink = () => {
  showLinkDialog.value = true;
  linkUrl.value = '';
  linkText.value = '';
  
  // Get selected text if any
  const selection = window.getSelection();
  if (selection.toString().trim()) {
    linkText.value = selection.toString();
  }
};

const insertLinkElement = () => {
  if (!linkUrl.value.trim()) {
    showLinkDialog.value = false;
    return;
  }

  restoreSelection();
  
  const a = document.createElement('a');
  a.href = linkUrl.value;
  a.textContent = linkText.value || linkUrl.value;
  a.target = '_blank';
  a.rel = 'noopener noreferrer';
  
  if (savedSelection) {
    if (!savedSelection.collapsed) {
      // Wrap selected text in link
      const fragment = savedSelection.extractContents();
      a.appendChild(fragment);
      savedSelection.insertNode(a);
    } else {
      // Insert new link at cursor
      savedSelection.insertNode(a);
      
      // Move cursor after the link
      const range = document.createRange();
      range.setStartAfter(a);
      range.collapse(true);
      
      const selection = window.getSelection();
      selection.removeAllRanges();
      selection.addRange(range);
    }
  }
  
  showLinkDialog.value = false;
  updateContent();
  editor.value.focus();
};

// Image functions
const insertImage = () => {
  showImageDialog.value = true;
  imageUrl.value = '';
  imageAlt.value = '';
  imageWidth.value = 300;
};

const insertImageElement = () => {
  if (!imageUrl.value.trim()) {
    showImageDialog.value = false;
    return;
  }

  restoreSelection();
  
  const img = document.createElement('img');
  img.src = imageUrl.value;
  img.alt = imageAlt.value || 'Image';
  img.style.maxWidth = '100%';
  img.style.height = 'auto';
  img.style.display = 'block';
  img.style.margin = '12px auto';
  
  if (imageWidth.value) {
    img.width = imageWidth.value;
  }
  
  if (savedSelection) {
    savedSelection.deleteContents();
    savedSelection.insertNode(img);
    
    // Add line breaks before and after for better spacing
    const brBefore = document.createElement('br');
    const brAfter = document.createElement('br');
    savedSelection.insertNode(brBefore);
    savedSelection.insertNode(img);
    savedSelection.insertNode(brAfter);
    
    // Move cursor after the image
    savedSelection.setStartAfter(brAfter);
    savedSelection.collapse(true);
  }
  
  showImageDialog.value = false;
  updateContent();
  editor.value.focus();
};

// Other functions
const insertHorizontalRule = () => {
  restoreSelection();
  document.execCommand('insertHorizontalRule', false, null);
  updateContent();
};

const handlePaste = (e) => {
  e.preventDefault();
  const text = e.clipboardData.getData('text/plain');
  document.execCommand('insertText', false, text);
  updateContent();
};

const handleKeyDown = (e) => {
  // Handle Enter key to prevent div creation
  if (e.key === 'Enter') {
    if (e.shiftKey) {
      // Shift+Enter inserts line break
      document.execCommand('insertLineBreak');
    } else {
      // Enter creates new paragraph
      document.execCommand('formatBlock', false, '<p>');
    }
    e.preventDefault();
    updateContent();
  }
  
  // Handle Tab in tables
  if (e.key === 'Tab' && e.target.closest('td, th')) {
    e.preventDefault();
    const tableCell = e.target.closest('td, th');
    const table = tableCell.closest('table');
    const cells = Array.from(table.querySelectorAll('td, th'));
    const currentIndex = cells.indexOf(tableCell);
    
    if (e.shiftKey) {
      // Shift+Tab - previous cell
      if (currentIndex > 0) {
        cells[currentIndex - 1].focus();
      }
    } else {
      // Tab - next cell
      if (currentIndex < cells.length - 1) {
        cells[currentIndex + 1].focus();
      } else {
        // Add new row if at last cell
        const newRow = table.insertRow();
        for (let i = 0; i < table.rows[0].cells.length; i++) {
          const cell = newRow.insertCell();
          cell.contentEditable = 'true';
          cell.style.border = '1px solid #ddd';
          cell.style.padding = '8px';
          
          const p = document.createElement('p');
          p.innerHTML = '<br>';
          cell.appendChild(p);
        }
        newRow.cells[0].focus();
      }
    }
  }
};
</script>

<style scoped>
.editor-container {
  border: 1px solid #ddd;
  border-radius: 6px;
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
  background: white;
}

.toolbar {
  padding: 8px 12px;
  background: #f5f5f5;
  border-bottom: 1px solid #ddd;
  display: flex;
  gap: 6px;
  flex-wrap: wrap;
  align-items: center;
  border-radius: 6px 6px 0 0;
}

.toolbar button, .toolbar select {
  padding: 6px 10px;
  background: white;
  border: 1px solid #ccc;
  border-radius: 4px;
  cursor: pointer;
  min-width: 32px;
  height: 32px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 14px;
}

.toolbar button:hover {
  background: #e9e9e9;
}

.toolbar select {
  padding: 6px;
  min-width: 100px;
}

.toolbar input[type="color"] {
  width: 32px;
  height: 32px;
  padding: 2px;
  border: 1px solid #ccc;
  border-radius: 4px;
  cursor: pointer;
}

.editor-content {
  min-height: 300px;
  padding: 16px;
  outline: none;
  line-height: 1.6;
}

.editor-content:focus {
  border-color: #4d90fe;
}

.editor-content >>> p {
  margin: 0 0 12px 0;
}

.editor-content >>> h1, 
.editor-content >>> h2, 
.editor-content >>> h3 {
  margin: 24px 0 12px 0;
}

.editor-content >>> ul, 
.editor-content >>> ol {
  padding-left: 24px;
  margin: 12px 0;
}

/* Dialog styles */
.dialog-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.dialog-content {
  background: white;
  padding: 24px;
  border-radius: 8px;
  width: 500px;
  max-width: 90vw;
  max-height: 90vh;
  overflow-y: auto;
}

.dialog-content h3 {
  margin-top: 0;
  margin-bottom: 20px;
  font-size: 18px;
  color: #333;
}

.form-group {
  margin-bottom: 16px;
}

.form-group label {
  display: block;
  margin-bottom: 6px;
  font-weight: 500;
}

.form-input {
  width: 100%;
  padding: 10px;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 14px;
}

textarea.form-input {
  min-height: 100px;
  font-family: monospace;
}

.preview {
  padding: 16px;
  margin: 16px 0;
  background: #f9f9f9;
  border-radius: 4px;
  min-height: 60px;
  overflow-x: auto;
}

.placeholder {
  color: #999;
  font-style: italic;
}

.error {
  color: #d32f2f;
}

.dialog-actions {
  display: flex;
  justify-content: flex-end;
  gap: 8px;
  margin-top: 20px;
}

.btn-cancel {
  background: #f5f5f5;
  color: #333;
  padding: 8px 16px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 14px;
}

.btn-insert {
  background: #1976d2;
  color: white;
  padding: 8px 16px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 14px;
}

.btn-cancel:hover {
  background: #e0e0e0;
}

.btn-insert:hover {
  background: #1565c0;
}

/* Math shortcuts */
.math-shortcuts {
  display: flex;
  flex-wrap: wrap;
  gap: 6px;
  margin: 12px 0;
}

.math-shortcuts button {
  padding: 6px 10px;
  background: #f0f0f0;
  border: 1px solid #ddd;
  border-radius: 4px;
  cursor: pointer;
  font-size: 14px;
}

.math-shortcuts button:hover {
  background: #e0e0e0;
}
</style>

<style>
/* Global styles for editor content */
.math-container {
  display: inline-block;
  margin: 0 2px;
  vertical-align: middle;
}

.math-container .katex {
  font-size: 1.1em !important;
}

.math-container .katex-display {
  margin: 0 !important;
  display: inline-block !important;
}

.math-container .katex-display > .katex {
  display: inline-block;
  white-space: nowrap;
}

.editor-table {
  width: 100%;
  border-collapse: collapse;
  margin: 12px 0;
}

.editor-table td, .editor-table th {
  border: 1px solid #ddd;
  padding: 8px;
  min-width: 30px;
}

.editor-table td:focus, .editor-table th:focus {
  outline: 2px solid #4d90fe;
  outline-offset: -1px;
}

.editor-content hr {
  border: none;
  border-top: 1px solid #ddd;
  margin: 16px 0;
}

.editor-content img {
  max-width: 100%;
  height: auto;
  display: block;
  margin: 12px auto;
}

.editor-content a {
  color: #1976d2;
  text-decoration: underline;
}

.editor-content a:hover {
  color: #0d47a1;
}
</style>