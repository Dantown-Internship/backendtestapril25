<?php

namespace App\Http\Resources\V1\Shared\Authentication;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OnboardingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $token = $this->createToken('Access Token')->plainTextToken;

        $accessCredentials = [
            'token' => $token,
            'type' => 'bearer'
        ];

        return [
            'id' => $this->id,
            'company' => [
                'id' => $this->company->id,
                'name' => $this->company->name,
                'email' => $this->company->email
            ],
            'user' => [
                'id' => $this->id,
                'name' => $this->name,
                'email' => $this->email,
                'role' => $this->role,
            ],
            'access_credentials' => $accessCredentials,
        ];
    }
}
