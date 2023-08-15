<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tour extends Model
{
    use HasFactory, HasUuids;
    protected $fillable = [
        "travel_id",
        "name",
        "starting_date",
        "ending_date",
        "price"
    ];


    // Price Accessor:: 
    // TIP: best practice is not NEVER store floats into Database but instead
    // format them during retrieval
    public function price(): Attribute
    {
        return Attribute::make(
            // retrieve computed price from Database
            get: fn($value) => $value / 100, // floats

            // set computed price in Database
            set: fn($value) => $value * 100 // intengers
        );
    }
}
