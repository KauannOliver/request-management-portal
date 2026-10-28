@props(['label', 'name', 'type' => 'text', 'value' => '', 'required' => false])

<div class="form-group {{ $errors->has($name) ? 'has-error' : '' }}">
    <label for="{{ $name }}" class="form-label">
        {{ $label }}
        @if($required) <span class="required-indicator" style="color: var(--color-atrasado);">*</span> @endif
    </label>
    
    <input 
        type="{{ $type }}" 
        name="{{ $name }}" 
        id="{{ $name }}" 
        value="{{ old($name, $value) }}"
        {{ $required ? 'required' : '' }}
        {{ $attributes->merge(['class' => 'form-control']) }}
    >
    
    @error($name)
        <span class="field-error-message" style="color: var(--color-atrasado); font-size: 0.85rem; margin-top: 0.25rem; display: block;">
            {{ $message }}
        </span>
    @enderror
</div>
