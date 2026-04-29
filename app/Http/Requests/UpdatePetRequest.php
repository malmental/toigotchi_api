<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePetRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:50',
            'species' => 'sometimes|string|in:blobcat,foxkid,draggle',
        ];
    }
}
