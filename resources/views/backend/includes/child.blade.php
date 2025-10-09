@foreach ($catalogues as $catalogue)
    <option value="{{ $catalogue->id }}">{{ $prefix . ' ' . $catalogue->name }}</option>
    @if ($catalogue->children->isNotEmpty())
        @include('backend.includes.child', [
            'catalogues' => $catalogue->children,
            'prefix' => $prefix . '--', // Thêm dấu '--' cho mỗi cấp con
        ])
    @endif
@endforeach
