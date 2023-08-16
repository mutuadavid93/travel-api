<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\TravelResource;
use App\Models\Travel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TravelController extends Controller
{
    //
    public function index()
    {
        // Return all public travels. No auth required.

        // TIP: paginate() adds pagination into returned payload
        $travels = Travel::where("is_public", true)->paginate();

        // Resource with subset of the fields needed
        return TravelResource::collection($travels);
    }
}
