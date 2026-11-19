<form action="{{ $action }}" method="POST" class="inline" data-confirm-delete>
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-danger btn-sm">{{ $label ?? 'Hapus' }}</button>
</form>
