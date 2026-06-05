{{--
    Partial: admin/profil/_rekening_row.blade.php
    Variabel: $i (index), $rek (array: bank, no_rek, atas_nama)
--}}
<div class="rekening-row flex gap-2 items-start bg-gray-50 rounded-xl p-3 border border-gray-200" id="rekRow{{ $i }}">
    <div class="flex-1 grid grid-cols-1 sm:grid-cols-3 gap-2">
        <div>
            <label class="text-xs text-gray-500 mb-0.5 block">Nama Bank</label>
            <input type="text" name="rekening[{{ $i }}][bank]"
                   value="{{ $rek['bank'] ?? '' }}"
                   class="w-full rounded-lg border border-gray-300 px-2 py-1.5 text-sm
                          focus:ring-2 focus:ring-blue-400 outline-none"
                   placeholder="BCA, BRI, BNI ...">
        </div>
        <div>
            <label class="text-xs text-gray-500 mb-0.5 block">No. Rekening</label>
            <input type="text" name="rekening[{{ $i }}][no_rek]"
                   value="{{ $rek['no_rek'] ?? '' }}"
                   class="w-full rounded-lg border border-gray-300 px-2 py-1.5 text-sm
                          focus:ring-2 focus:ring-blue-400 outline-none"
                   placeholder="12345678">
        </div>
        <div>
            <label class="text-xs text-gray-500 mb-0.5 block">Atas Nama</label>
            <input type="text" name="rekening[{{ $i }}][atas_nama]"
                   value="{{ $rek['atas_nama'] ?? '' }}"
                   class="w-full rounded-lg border border-gray-300 px-2 py-1.5 text-sm
                          focus:ring-2 focus:ring-blue-400 outline-none"
                   placeholder="Nama pemilik rekening">
        </div>
    </div>
    <button type="button" onclick="hapusRekening({{ $i }})"
            class="mt-5 text-red-400 hover:text-red-600 text-sm transition flex-shrink-0">
        <i class="fa fa-times-circle fa-lg"></i>
    </button>
</div>