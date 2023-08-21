<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Travel;
use App\Http\Controllers\Controller;
use App\Http\Resources\TourResource;
use App\Http\Requests\ToursListRequest;

class TourController extends Controller
{
    // The argument defaults into /api/v1/travels/[travel.id]/tours but if changed 
    // to use a different column, at the endpoint, it changes 
    // into e.g. /api/v1/travels/[travel.slug]/tours when using a slug column.
    public function index(Travel $travel, ToursListRequest $request)
    {        
        // see - http://localhost:8000/api/v1/travels/my-custom-travelx-3/tours?priceFrom=123&priceTo=456&dateFrom=2023-06-01&dateTo=2023-07-01
        $tours = $travel->tours()
            // Filter by some columns
            ->when($request->priceFrom, function ($query) use ($request) {
                // multiply by 100 to get it in cents
                $query->where("price", ">=", $request->priceFrom * 100);
            })
            ->when($request->priceTo, function ($query) use ($request) {
                $query->where("price", "<=", $request->priceTo * 100);
            })
            // TIP: Apply the callback if the given "value" is (or resolves to) truthy.
            ->when($request->dateFrom, function ($query) use ($request) {
                // The condition
                $query->where("starting_date", ">=", $request->dateFrom);
            })
            ->when($request->dateTo, function ($query) use ($request) {
                $query->where("starting_date", "<=", $request->dateTo);
            })
            // Alter the orderBy e.g. sortBy="price" and sortOrder="ASC"
            ->when(
                $request->sortBy && $request->sortOrder,
                fn($query) =>
                $query->orderBy($request->sortBy, $request->sortOrder)
            )
            ->orderBy("starting_date")
            ->paginate();

        return TourResource::collection($tours);
    }
}
