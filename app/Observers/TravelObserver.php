<?php

namespace App\Observers;

use App\Models\Travel;


/*
|--------------------------------------------------------------------------
| Observers
|--------------------------------------------------------------------------
|
| lifeCycle Hooks during table manipulation activities. 
| NOTE: every observer MUST be registered inside EventServiceProvider boot(),
|
| TIP: best practice for unique slugs is to use a package e.g. https://github.com/cviebrock/eloquent-sluggable
|
*/

class TravelObserver
{
    /**
     * Handle the Travel "created" event.
     */
    public function creating(Travel $travel): void
    {
        // generate a custom slug.
        $travel->slug = str($travel->name)->slug();
    }
}
