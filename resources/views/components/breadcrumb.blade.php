<div class="card w-100 border-0 shadow-sm mb-3">
    <div class="card-body p-3">
        <div class="d-flex align-items-center">
            <i class="fas fa-tachometer-alt text-primary mr-2"></i>
            <a href="{{ route('dashboard.index') }}" class="text-decoration-none text-muted">Dashboard</a>

            @foreach ($items as $item)
                <span class="text-muted mx-2">></span>
                @if ($loop->last)
                    <span class="font-weight-bold text-primary">{{ $item['label'] }}</span>
                @else
                    <a href="{{ $item['url'] ?? '#' }}" class="text-decoration-none text-muted">{{ $item['label'] }}</a>
                @endif
            @endforeach
        </div>
    </div>
</div>
