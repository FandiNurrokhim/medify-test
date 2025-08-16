@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="form-group mb-2">
                    <a href="{{ url('categories') }}" class="btn btn-secondary">Kembali ke Daftar Kategori</a>
                </div>
                <div class="card">
                    <div class="card-header">Kategori</div>

                    <div class="card-body">
                        <table>
                            <tr>
                                <th>Nama</th>
                                <td>:</td>
                                <td>{{ $data->nama }}</td>
                            </tr>
                            @if ($data->masterItems && $data->masterItems->count())
                                <h5 class="mt-4">Daftar Item pada Kategori ini:</h5>
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Kode Item</th>
                                            <th>Nama Item</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($data->masterItems as $item)
                                            <tr>
                                                <td>{{ $item->kode }}</td>
                                                <td>{{ $item->nama }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <p class="mt-4">Tidak ada item pada kategori ini.</p>
                            @endif
                        </table>
                        <a class="btn btn-info" href="{{ url('categories/form/edit') }}/{{ $data->id }}">Edit</a>
                        <a class="btn btn-danger" href="{{ url('categories/delete') }}/{{ $data->id }}"
                            onclick="return confirm('Are you sure you want to delete this item?');">Delete</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
@endsection
