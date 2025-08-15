@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="form-group mb-2">
                    <a href="{{ url('master-items/form/new') }}" class="btn btn-secondary">+ Master Items Baru</a>
                </div>
                <div class="card">
                    <div class="card-header">Daftar Master Items</div>

                    <div class="card-body">
                        @include('master_items.index.filter')
                        @include('master_items.index.table')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    @include('master_items.index.js')
@endsection
