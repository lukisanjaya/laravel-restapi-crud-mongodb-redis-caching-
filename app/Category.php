<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model;

class Category extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'slug'
    ];

    /* public function setSlugAttribute($name)
    {
        $this->attributes['slug'] = Str::slug($name);
    } */
}
