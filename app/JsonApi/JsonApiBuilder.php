<?php

namespace App\JsonApi;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class JsonApiBuilder
{
    public function jsonPaginate()
    {
        return function () {
            /** @var Builder $this */
            return $this->paginate(
                $perPage = \request('page.size'),
                $columns = ['*'],
                $pageName = 'page[number]',
                $page = \request('page.number')
            )->appends(request()->except('page.number'));
        };
    }

    public function applyApiSort()
    {
        return function() {
            /** @var Builder $this */

            $sortString = request()->get('sort');

            if(is_null($sortString)) {
                return $this;
            }

            abort_unless(
                property_exists($this->getModel(), 'allowedSortFields'),
                500,
                'Sortifiable trait requires to add the public property $allowedSortFields to the model ' .get_class($this->getModel())
            );

            abort_unless(
                collect($this->getModel()->allowedSortFields)->contains(Str::of($sortString)->replace('-', '')),
                400,
                "Invalid query param, {$sortString} is not allowed"
            );

            $sortFields = Str::of($sortString)->explode(',');

            $sortFields->each(function($sortfield) {
                $order = 'asc';

                if (Str::of($sortfield)->startsWith('-')) {
                    $order = 'desc';
                    $sortfield = Str::of($sortfield)->replace('-', '');
                }

                $this->orderBy($sortfield, $order);

            });

            return $this;
        };
    }

    public function applyApiFilter()
    {
        return function() {

            /** @var Builder $this */

            foreach (request('filter', []) as $filterKey => $filterValue) {
                abort_unless(
                    $this->hasNamedScope($filterKey),
                    400,
                    'The method scope'. ucfirst($filterKey) .' does not exists in ' . get_class($this->getModel())
                );

                $this->{$filterKey}($filterValue);
            }

            return $this;
        };
    }
}
