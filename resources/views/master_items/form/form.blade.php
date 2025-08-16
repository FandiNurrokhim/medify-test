@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST"
    action="{{ $method == 'edit' ? route('master_items.update', $item->id) : route('master_items.store') }}"
    enctype="multipart/form-data">
    @csrf
    @if ($method == 'edit')
        @method('PUT')
        <div class="form-group">
            <label>Kode Barang</label>
            <input type="text" class="form-control" name="kode_barang" required readonly value="{{ $item->kode ?? '' }}">
        </div>
    @endif

    <div class="form-group">
        <label>Nama</label>
        <input type="text" class="form-control" name="nama" required value="{{ $item->nama ?? '' }}">
    </div>

    <div class="form-group">
        <label>Foto</label>
        <input type="file" class="form-control" name="photo" accept="image/*">
        @if ($method == 'edit' && !empty($item->photo))
            <div class="mt-2">
                <img src="{{ asset('storage/' . $item->photo) }}" alt="Foto Barang" style="max-width: 150px;">
            </div>
        @endif
    </div>

    <div class="form-group">
        <label>Harga Beli</label>
        <input type="number" class="form-control" name="harga_beli" required value="{{ $item->harga_beli ?? '' }}">
    </div>

    <div class="form-group">
        <label>Laba (dalam persen)</label>
        <input type="number" class="form-control" name="laba" required value="{{ $item->laba ?? '' }}"
            min="0" max="100">
    </div>

    @php $selected = $item->supplier ?? ''; @endphp
    <div class="form-group">
        <label>Supplier</label>
        <select class="form-control" required name="supplier">
            <option @if ($selected == '') selected @endif value="">--Pilih--</option>
            <option @if ($selected == 'Tokopaedi') selected @endif>Tokopaedi</option>
            <option @if ($selected == 'Bukulapuk') selected @endif>Bukulapuk</option>
            <option @if ($selected == 'TokoBagas') selected @endif>TokoBagas</option>
            <option @if ($selected == 'E Commurz') selected @endif>E Commurz</option>
            <optio @if ($selected == 'Blublu') selected @endif>Blublu</option>
        </select>
    </div>

    @php $selected = $item->jenis ?? ''; @endphp
    <div class="form-group">
        <label>Jenis</label>
        <select class="form-control" required name="jenis">
            <option @if ($selected == '') selected @endif value="">--Pilih--</option>
            <option @if ($selected == 'Obat') selected @endif>Obat</option>
            <option @if ($selected == 'Alkes') selected @endif>Alkes</option>
            <option @if ($selected == 'Matkes') selected @endif>Matkes</option>
            <optio @if ($selected == 'Umum') selected @endif>Umum</option>
                <optio @if ($selected == 'ATK') selected @endif>ATK</option>
        </select>
    </div>

    @php
        if (isset($item) && is_object($item) && method_exists($item, 'categories')) {
            $selectedCategory = $item->categories->pluck('id')->toArray();
        } else {
            $selectedCategory = [];
        }
    @endphp
    <div class="form-group">
        <label>Kategori</label>
        <select class="form-control" required name="category[]" multiple>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @if (isset($selectedCategory) && is_array($selectedCategory) && in_array($category->id, $selectedCategory)) selected @endif>
                    {{ $category->nama }}
                </option>
            @endforeach
        </select>
    </div>

    <button class="btn btn-primary mt-3">Submit</button>

</form>
