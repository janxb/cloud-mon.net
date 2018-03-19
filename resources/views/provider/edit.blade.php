@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Provider bearbeiten</div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('provider.update',$provider) }}">
                            {{ csrf_field() }}
                            {{ method_field('PUT') }}
                            <div class="form-group">
                                <label>Name</label>
                                <input type="text" class="form-control" name="name" value="{{ $provider->name }}">
                            </div>
                            <div class="form-group">
                                <label>Target</label>
                                <input type="text" class="form-control" name="target" value="{{ $provider->target }}">
                            </div>
                            <div class="form-group">
                                <label>Color</label>
                                <input type="text" class="form-control" name="color" value="{{ $provider->color }}">
                            </div>
                            <button type="submit" class="btn btn-primary">Speichern</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
