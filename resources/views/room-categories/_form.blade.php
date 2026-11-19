@csrf

<div class="field">
<label for="nama">Nama</label>
<input class="input" type="text" name="nama" id="nama" value="{{ old('nama', $category->nama ?? '') }}">
@error('nama')
    <div class="error">{{ $message }}</div>
@enderror

</div>
<div class="field">
<label for="deskripsi">Deskripsi</label>
<textarea class="textarea" name="deskripsi" id="deskripsi" rows="4">{{ old('deskripsi', $category->deskripsi ?? '') }}</textarea>
@error('deskripsi')
    <div class="error">{{ $message }}</div>
@enderror
</div>

<button type="submit" class="btn btn-primary">Simpan</button>
