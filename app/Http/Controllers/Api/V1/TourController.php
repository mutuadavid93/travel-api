<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Models\{Travel, Tour};
use App\Http\Controllers\Controller;
use App\Http\Resources\TourResource;

class TourController extends Controller
{
    // The argument defaults into /api/v1/travels/[travel.id]/tours but if changed 
    // to use a different column, at the endpoint, it changes 
    // into e.g. /api/v1/travels/[travel.slug]/tours when using a slug column.
    public function index(Travel $travel)
    {
        $tours = $travel->tours()
            ->orderBy("starting_date")
            ->paginate();

        return TourResource::collection($tours);
    }
}
