<?php

namespace App\Http\Resources\Api\User;

use App\Models\Translate;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CurrentUserResource extends JsonResource
{
    private function checkAccess() {
        $access = $this->access;
        if (!count($access)) {
            auth()->user()->token()->revoke();
            //
            return abort(response()->json(['message' => 'Нет доступа'], 401));
        }
        return $access->pluck('name')->unique('name');
    }

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
            'access' => $this->checkAccess()
        ];
    }
}
