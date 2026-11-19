@if (session('success'))
    <div class="toast show" data-toast>{{ session('success') }}</div>
@endif

@if (session('error'))
    <div class="toast show" data-toast>{{ session('error') }}</div>
@endif

@if ($errors->any())
    <div class="alert-error">
        <strong>Periksa kembali isian berikut:</strong>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
