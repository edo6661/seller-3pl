<?php

namespace App\Collection;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Request;

class UserCollection extends ResourceCollection
{
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection,
            'meta' => [
                'total' => $this->collection->count(),
            ],
        ];
    }
}
