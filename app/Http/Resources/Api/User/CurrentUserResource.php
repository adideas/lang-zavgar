<?php

namespace App\Http\Resources\Api\User;

use App\Models\Translate;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CurrentUserResource extends JsonResource
{
    /**
     * Тут current user
     *
     * @param $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'     => $this->id,
            'email'  => $this->email,
            'name'   => $this->name,
            'access' => auth()->user()->getAccess()
        ];
    }
}
