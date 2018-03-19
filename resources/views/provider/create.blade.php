@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Provider erstellen</div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('provider.store') }}">
                            {{ csrf_field() }}
                            <div class="form-group">
                                <label>Name</label>
                                <input type="text" class="form-control" name="name">
                            </div>
                            <div class="form-group">
                                <label>Target</label>
                                <input type="text" class="form-control" name="target" value="HetznerTarget">
                            </div>
                            <div class="form-group">
                                <label>Color</label>
                                <input type="text" class="form-control" name="color" value="#ff0000">
                            </div>
                            <div class="form-group">
                                <label for="api_key">Api Key</label>
                                <input type="password" class="form-control" name="api_key"
                                       placeholder="Password">
                            </div>
                            <button type="submit" class="btn btn-primary">Erstellen</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
