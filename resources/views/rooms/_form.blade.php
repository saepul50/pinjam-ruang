@csrf

<div class="form-grid">
<div class="field">
<label for="room_category_id">Kategori</label>
<select class="select" name="room_category_id" id="room_category_id">
    <option value="">-- Pilih Kategori --</option>
    @foreach ($categories as $category)
        <option value="{{ $category->id }}" @selected(old('room_category_id', $room->room_category_id ?? '') == $category->id)>
            {{ $category->nama }}
        </option>
    @endforeach
</select>
@error('room_category_id') <div class="error">{{ $message }}</div> @enderror
</div>

<div class="field">
<label for="kode">Kode</label>
<input class="input" type="text" name="kode" id="kode" value="{{ old('kode', $room->kode ?? '') }}">
@error('kode') <div class="error">{{ $message }}</div> @enderror
</div>

<div class="field">
<label for="nama">Nama</label>
<input class="input" type="text" name="nama" id="nama" value="{{ old('nama', $room->nama ?? '') }}">
@error('nama') <div class="error">{{ $message }}</div> @enderror
</div>

<div class="field">
<label for="kapasitas">Kapasitas</label>
<input class="input" type="number" min="1" name="kapasitas" id="kapasitas" value="{{ old('kapasitas', $room->kapasitas ?? 1) }}">
@error('kapasitas') <div class="error">{{ $message }}</div> @enderror
</div>

<div class="field">
<label for="lokasi">Lokasi</label>
<input class="input" type="text" name="lokasi" id="lokasi" value="{{ old('lokasi', $room->lokasi ?? '') }}">
@error('lokasi') <div class="error">{{ $message }}</div> @enderror
</div>

<div class="field">
<label for="gedung">Gedung</label>
<input class="input" type="text" name="gedung" id="gedung" required value="{{ old('gedung', $room->gedung ?? 'Gedung A') }}">
@error('gedung') <div class="error">{{ $message }}</div> @enderror
</div>

<div class="field">
<label for="lantai">Lantai</label>
<input class="input" type="number" min="1" max="20" name="lantai" id="lantai" required value="{{ old('lantai', $room->lantai ?? 1) }}">
@error('lantai') <div class="error">{{ $message }}</div> @enderror
</div>

<div class="field">
<label for="posisi_denah">Posisi Denah</label>
<select class="select" name="posisi_denah" id="posisi_denah">
    <option value="">Otomatis</option>
    @foreach (range(1, 7) as $slot)
        <option value="{{ $slot }}" @selected((string) old('posisi_denah', $room->posisi_denah ?? '') === (string) $slot)>Slot {{ $slot }}</option>
    @endforeach
</select>
@error('posisi_denah') <div class="error">{{ $message }}</div> @enderror
</div>

<div class="field">
<label for="foto_url">URL Foto</label>
<input class="input" type="url" name="foto_url" id="foto_url" value="{{ old('foto_url', $room->foto_url ?? '') }}">
@error('foto_url') <div class="error">{{ $message }}</div> @enderror
</div>

<div class="field">
<label for="fasilitas">Fasilitas</label>
<textarea class="textarea" name="fasilitas" id="fasilitas" rows="3">{{ old('fasilitas', $room->fasilitas ?? '') }}</textarea>
@error('fasilitas') <div class="error">{{ $message }}</div> @enderror
</div>

<div class="field">
<label for="deskripsi">Deskripsi</label>
<textarea class="textarea" name="deskripsi" id="deskripsi" rows="3">{{ old('deskripsi', $room->deskripsi ?? '') }}</textarea>
@error('deskripsi') <div class="error">{{ $message }}</div> @enderror
</div>

<div class="field">
<label for="status">Status</label>
<select class="select" name="status" id="status">
    @foreach (['tersedia', 'perbaikan', 'nonaktif'] as $status)
        <option value="{{ $status }}" @selected(old('status', $room->status ?? 'tersedia') === $status)>
            {{ ucfirst($status) }}
        </option>
    @endforeach
</select>
@error('status') <div class="error">{{ $message }}</div> @enderror
</div>
</div>

<button type="submit" class="btn btn-primary">Simpan</button>
