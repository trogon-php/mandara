<?php

namespace App\Http\Resources\User;

use App\Enums\Role;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class AppUserProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'id'         => $this->id,
            'name'       => $this->name,
            'age'        => Carbon::parse($this->getMetaField('date_of_birth'))->age,
            'phone'      => $this->phone,
            'country_code' => $this->country_code,
            'email'      => $this->email,
            'role_id'    => $this->role_id,
            'role'       => Role::from($this->role_id)->label(), 
            'profile_picture' => $this->profile_picture_url,
            'meta'       => $this->getMetaFields(), // Include user meta data
        ];
    }
}
