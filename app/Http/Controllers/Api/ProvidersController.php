<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\ProviderResource;
use App\Models\Check;
use App\Models\Provider;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProvidersController extends Controller
{
    public function index()
    {
        return response()->json(['available_providers' => ProviderResource::collection(Provider::all())]);
    }
}
