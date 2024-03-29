@extends('layouts.app')

@section('title', __('Missing'))

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">Products</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                        <missing-table></missing-table>
                        <br />

                </div>
            </div>
        </div>
    </div>
</div>
@endsection

