<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Tag
 *
 * @package App
 */
class Tag extends Model
{
      /**
       * The attributes that are mass assignable.
       *
       * @var array
       */
    protected $fillable = [
        'id', 'name', 'description', 'category',
    ];
}
