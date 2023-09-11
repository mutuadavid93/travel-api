<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Resources\TravelResource;
use App\Models\Travel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\TravelRequest;

class TravelController extends Controller
{
    public function store(TravelRequest $request)
    {
        $travel = Travel::create($request->validated());

        // Re-use the resource from public travels
        return new TravelResource($travel);
    }
}
