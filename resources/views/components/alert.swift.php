@if($type === 'success')
    <div class="alert alert-success">
        {{ $message }}
    </div>
@elseif($type === 'error')
    <div class="alert alert-error">
        {{ $message }}
    </div>
@else
    <div class="alert" style="background: #dbeafe; color: #1e40af; border: 1px solid #93c5fd;">
        {{ $message }}
    </div>
@endif