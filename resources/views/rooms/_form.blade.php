@csrf

<label for="room_category_id">Kategori</label>
<select name="room_category_id" id="room_category_id">
    <option value="">-- Pilih Kategori --</option>
    @foreach ($categories as $category)
        <option value="{{ $category->id }}" @selected(old('room_category_id', $room->room_category_id ?? '') == $category->id)>
            {{ $category->nama }}
        </option>
    @endforeach
</select>
@error('room_category_id') <div class="error">{{ $message }}</div> @enderror

<label for="kode">Kode</label>
<input type="text" name="kode" id="kode" value="{{ old('kode', $room->kode ?? '') }}">
@error('kode') <div class="error">{{ $message }}</div> @enderror

<label for="nama">Nama</label>
<input type="text" name="nama" id="nama" value="{{ old('nama', $room->nama ?? '') }}">
@error('nama') <div class="error">{{ $message }}</div> @enderror

<label for="kapasitas">Kapasitas</label>
<input type="number" min="1" name="kapasitas" id="kapasitas" value="{{ old('kapasitas', $room->kapasitas ?? 1 }}">
@error('kapasitas') <div class="error">{{ $message }}</div> @enderror

<label for="lokasi">Lokasi</label>
<input type="text" name="lokasi" id="lokasi" value="{{ old('lokasi', $room->lokasi ?? '') }}">
@error('lokasi') <div class="error">{{ $message }}</div> @enderror

<label for="fasilitas">Fasilitas</label>
<textarea name="fasilitas" id="fasilitas" rows="3">{{ old('fasilitas', $room->fasilitas ?? '') }}</textarea>
@error('fasilitas') <div class="error">{{ $message }}</div> @enderror

<label for="deskripsi">Deskripsi</label>
<textarea name="deskripsi" id="deskripsi" rows="3">{{ old('deskripsi', $room->deskripsi ?? '') }}</textarea>
@error('deskripsi') <div class="error">{{ $message }}</div> @enderror

<label for="status">Status</label>
<select name="status" id="status">
    @foreach (['tersedia', 'perbaikan', 'nonaktif'] as $status)
        <option value="{{ $status }}" @selected(old('status', $room->status ?? 'tersedia') === $status)>
            {{ ucfirst($status) }}
        </option>
    @endforeach
</select>
@error('status') <div class="error">{{ $message }}</div> @enderror

<button type="submit" class="btn btn-primary">Simpan</button>
