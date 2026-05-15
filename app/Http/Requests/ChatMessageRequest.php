<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChatMessageRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'message' => 'required|string|min:1|max:500',
        ];
    }
}
