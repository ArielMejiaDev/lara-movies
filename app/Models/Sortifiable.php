<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

trait Sortifiable
{
//    public function scopeSortBy(Builder $query, $sortString)
//    {
//        if(is_null($sortString)) {
//            return;
//        }
//
//        abort_unless(
//            property_exists($this, 'allowedSortFields'),
//            500,
//    'Sortifiable trait requires to add the public property $allowedSortFields to the model ' .get_class($this)
//        );
//
//        abort_unless(
//            collect($this->allowedSortFields)->contains(Str::of($sortString)->replace('-', '')),
//            400,
//            "Invalid query param, {$sortString} is not allowed"
//        );
//
//        $sortFields = Str::of($sortString)->explode(',');
//
//        $sortFields->each(function($sortfield) use($query) {
//            $order = 'asc';
//
//            if (Str::of($sortfield)->startsWith('-')) {
//                $order = 'desc';
//                $sortfield = Str::of($sortfield)->replace('-', '');
//            }
//
//            return $query->orderBy($sortfield, $order);
//
//        });
//    }
}
