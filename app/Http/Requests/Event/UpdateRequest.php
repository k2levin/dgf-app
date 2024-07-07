<?php

namespace App\Http\Requests\Event;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id' => 'required|numeric|exists:App\Models\Event,id',
            'name' => 'sometimes|required|max:255',
            'description' => 'sometimes|required|max:255',
            'ticket_total_quantity' => 'sometimes|required|numeric',
        ];
    }
}
