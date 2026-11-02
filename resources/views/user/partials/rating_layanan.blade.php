@if (! $ratingUlasan)
    <aside class="dp-rating-panel" aria-label="Rating dan ulasan layanan">
        <div class="dp-rating-panel__header">
            <h3>Berikan penilaian untuk kami</h3>
            <p>Bantu kami untuk meningkatkan kualitas layanan</p>
        </div>

        <form class="dp-rating-form" action="{{ route('pengajuan.rating.store', ['nomorTiket' => $pengajuan->nomor_tiket]) }}" method="POST">
            @csrf
            <div class="dp-rating-stars" role="radiogroup" aria-label="Pilih rating layanan">
                @for ($i = 1; $i <= 5; $i++)
                    <label class="dp-star-option" data-value="{{ $i }}" data-tooltip="{{ $i }} Bintang" title="{{ $i }} Bintang" aria-label="{{ $i }} Bintang">
                        <input type="radio" name="rating" value="{{ $i }}" {{ old('rating') == (string) $i ? 'checked' : '' }}>
                        <span aria-hidden="true"><i class="bi bi-star-fill"></i></span>
                    </label>
                @endfor
            </div>
            @error('rating')<small class="dp-form-error">{{ $message }}</small>@enderror

            <label class="dp-rating-label" for="rating-komentar">Saran dan Pendapat</label>
            <textarea id="rating-komentar" name="komentar" rows="4" maxlength="2000" placeholder="Tuliskan komentar atau saran Anda...">{{ old('komentar') }}</textarea>
            @error('komentar')<small class="dp-form-error">{{ $message }}</small>@enderror

            <button class="dp-button dp-button--purple" type="submit">Submit</button>
        </form>
    </aside>
@endif
