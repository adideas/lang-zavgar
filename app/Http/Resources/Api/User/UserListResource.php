<?php

namespace App\Http\Resources\Api\User;

use App\Models\File;
use App\Models\Language;
use App\User;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Cache;

class UserListResource extends JsonResource
{
    private function isRoot(): bool
    {
        try {
            return $this->getAccess()["root"];
        } catch (\Exception $e) {
            return false;
        }
    }

    private function access()
    {
        $access = $this->getAccess();
        $access = array_merge(...(array_map(fn($x) => $access[$x], ["store", "show", "update", "destroy"])));

        return array_map(
            fn($x) => [File::class => "CRUD FILES", User::class => "CRUD USERS", Language::class => "CRUD LANG",][$x],
            array_unique($access)
        );
    }

    public function toArray($request)
    {
        return [
            "id"            => $this->id,
            "email"         => $this->email,
            "name"          => $this->name,
            "last_login_at" => $this->last_login_at,
            "root"          => $this->isRoot(),
            "access"        => $this->access(),
        ];
    }
}
