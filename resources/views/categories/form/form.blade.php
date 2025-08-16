@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ $method == 'edit' ? route('categories.update', $item->id) : route('categories.store') }}">
    @csrf
    @if($method == 'edit')
    @method('PUT')
    <div class="form-group">
        <label>Kode Kategori</label>
        <input type="text" class="form-control" name="kode_kategori" required readonly value="{{$item->kode ?? ''}}">
    </div>
    @endif

    <div class="form-group">
        <label>Nama</label>
        <input type="text" class="form-control" name="nama" required  value="{{$item->nama ?? ''}}">
    </div>

    <button class="btn btn-primary mt-3">Submit</button>
</form>