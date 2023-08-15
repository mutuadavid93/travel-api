<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Travel extends Model
{
    use HasFactory, Sluggable, HasUuids;
    protected $table = 'travels';
    protected $fillable = [
        "is_public",
        "slug",
        "name",
        "description",
        "number_of_days"
    ];

    // TIP: best practice: always create relationsips before other Attributes e.g. numberOfNights
    public function tours(): HasMany
    {
        // Tour : Travel relationship is one-to-many
        return $this->hasMany(Tour::class);
    }

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }


    // Some columns might be Virtuals i.e. computed from other columns' values
    // - e.g. "number_of_nights" = number_of_days - 1;
    // TIP: use Accessors
    public function numberOfNights(): Attribute
    {
        return Attribute::make(
            // HINT: can have a getter and setter
            get: fn($value, $attributes) => $attributes["number_of_days"] - 1
        );
    }
}
