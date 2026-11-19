@csrf

<label for="nama">Nama</label>
<input type="text" name="nama" id="nama" value="{{ old('nama', $category->nama ?? '') }}">
@error('nama')
    <div class="error">{{ $message }}</div>
@enderror

<label for="deskripsi">Deskripsi</label>
<textarea name="deskripsi" id="deskripsi" rows="4">{{ old('deskripsi', $category->deskripsi ?? '') }}</textarea>
@error('deskripsi')
    <div class="error">{{ $message }}</div>
@enderror

<button type="submit" class="btn btn-primary">Simpan</button>
