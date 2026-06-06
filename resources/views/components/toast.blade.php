@php $toast = session('flash') @endphp

@if ($toast)
    <div class="toast-wrap">
        <div class="toast" key="{{ md5($toast) }}">
            <span class="toast-icon" style="color: #4ade80">&#10003;</span>
            {{ $toast }}
        </div>
    </div>
@endif
