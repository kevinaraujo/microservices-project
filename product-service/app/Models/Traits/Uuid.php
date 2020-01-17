<?php
/**
 * Created by PhpStorm.
 * User: kevin.araujo
 * Date: 17/01/20
 * Time: 18:11
 */

namespace App\Models\Traits;


trait Uuid
{
    public static function boot()
    {
        parent::boot();
        static::creating(function ($obj) {
            if (!$obj->id) {
                $obj->id = \Ramsey\Uuid\Uuid::uuid4();
            }
        });
    }
}