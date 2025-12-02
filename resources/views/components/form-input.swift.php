<div class="form-group">
    <label for="{{ $name }}">{{ $label }}</label>
    <input 
        type="{{ $type ?? 'text' }}" 
        id="{{ $name }}" 
        name="{{ $name }}" 
        value="{{ $value ?? '' }}"
        @if($required ?? false) required @endif
        @if($placeholder ?? false) placeholder="{{ $placeholder }}" @endif
    >
    @if($error ?? false)
        <small style="color: #dc2626; font-size: 0.875rem;">{{ $error }}</small>
    @endif
</div>