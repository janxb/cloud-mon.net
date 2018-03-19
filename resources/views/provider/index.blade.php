@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Provider</div>

                    <div class="card-body">
                        <a href="{{ route('provider.create') }}">Erstellen</a>
                        <table class="table">
                            <thead>
                            <th>Name</th>
                            <th>Aktionen</th>
                            </thead>
                            <tbody>
                            @foreach(\App\Models\Provider::all() as $provider)
                                <tr>
                                    <td>{{ $provider->name }}</td>
                                    <td><a href="{{ route('provider.edit',$provider) }}">Bearbeiten</a></td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
