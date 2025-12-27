<div class="form-group">
    <label for="{{ $id ?? $name }}">{{ $label }} {!! !empty($required) ? '<span class="text-danger">*</span>' : '' !!}</label>
    
    {{-- Hidden input to store comma-separated values --}}
    <input type="hidden" 
           name="{{ $name }}" 
           id="{{ $id ?? $name }}" 
           value="{{ old($name, $value ?? '') }}"
           class="tags-hidden-input">
    
    {{-- Visible tags container --}}
    <div class="tags-input-container form-control {{ $className ?? '' }}" 
         id="tags-container-{{ $id ?? $name }}"
         style="min-height: 38px; padding: 5px; display: flex; flex-wrap: wrap; align-items: center; gap: 5px; cursor: text;">
        {{-- Tags will be rendered here --}}
    </div>
    
    {{-- Input for typing new tags --}}
    <input type="text" 
           class="tags-type-input form-control d-none" 
           id="tags-type-{{ $id ?? $name }}"
           placeholder="{{ $placeholder ?? 'Type and press comma or enter to add tags' }}"
           autocomplete="off">
    
    @error($name) <small class="text-danger">{{ $message }}</small> @enderror
    <small class="text-muted d-block mt-1">Press comma or enter to add a tag</small>
</div>

<style>
.tags-input-container {
    background-color: #fff;
    border: 1px solid #ced4da;
    border-radius: 0.25rem;
}

.tags-input-container:focus-within {
    border-color: #86b7fe;
    outline: 0;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

.tag-item {
    display: inline-flex;
    align-items: center;
    background-color: #48755b;
    color: white;
    padding: 4px 8px;
    border-radius: 15px;
    font-size: 0.875rem;
    gap: 5px;
}

.tag-item .tag-remove {
    cursor: pointer;
    font-weight: bold;
    opacity: 0.8;
    padding: 0 2px;
}

.tag-item .tag-remove:hover {
    opacity: 1;
}

.tags-type-input {
    border: none;
    outline: none;
    flex: 1;
    min-width: 120px;
    padding: 5px;
}
</style>

<script>
(function() {
    const fieldId = '{{ $id ?? $name }}';
    const hiddenInput = document.getElementById(fieldId);
    const container = document.getElementById('tags-container-' + fieldId);
    const typeInput = document.getElementById('tags-type-' + fieldId);
    
    if (!hiddenInput || !container || !typeInput) return;
    
    // Parse initial value
    let tags = [];
    const initialValue = hiddenInput.value;
    if (initialValue && initialValue.trim() !== '') {
        tags = initialValue.split(',').map(tag => tag.trim()).filter(tag => tag !== '');
    }
    
    // Render tags
    function renderTags() {
        container.innerHTML = '';
        
        // Render existing tags
        tags.forEach((tag, index) => {
            const tagElement = document.createElement('span');
            tagElement.className = 'tag-item';
            tagElement.innerHTML = `
                <span>${escapeHtml(tag)}</span>
                <span class="tag-remove" data-index="${index}">&times;</span>
            `;
            container.appendChild(tagElement);
        });
        
        // Add input field
        typeInput.classList.remove('d-none');
        container.appendChild(typeInput);
        typeInput.focus();
        
        // Update hidden input
        hiddenInput.value = tags.join(',');
    }
    
    // Escape HTML
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    // Add tag
    function addTag(tagText) {
        const trimmed = tagText.trim();
        if (trimmed && !tags.includes(trimmed)) {
            tags.push(trimmed);
            renderTags();
        }
    }
    
    // Remove tag
    function removeTag(index) {
        tags.splice(index, 1);
        renderTags();
    }
    
    // Handle input events
    typeInput.addEventListener('keydown', function(e) {
        if (e.key === ',' || e.key === 'Enter') {
            e.preventDefault();
            const value = this.value;
            if (value.trim()) {
                addTag(value);
                this.value = '';
            }
        } else if (e.key === 'Backspace' && this.value === '' && tags.length > 0) {
            // Remove last tag if backspace on empty input
            removeTag(tags.length - 1);
        }
    });
    
    // Handle blur - add tag if there's text
    typeInput.addEventListener('blur', function() {
        if (this.value.trim()) {
            addTag(this.value);
            this.value = '';
        }
    });
    
    // Handle tag removal
    container.addEventListener('click', function(e) {
        if (e.target.classList.contains('tag-remove')) {
            const index = parseInt(e.target.getAttribute('data-index'));
            removeTag(index);
        }
    });
    
    // Make container focusable
    container.addEventListener('click', function(e) {
        if (e.target === container || e.target.classList.contains('tag-item')) {
            typeInput.focus();
        }
    });
    
    // Initial render
    renderTags();
})();
</script>