<?php

namespace App\Http\Requests\Ticket;

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
            'id' => 'required|numeric|exists:App\Models\Ticket,id',
            'serial_number' => 'sometimes|required|max:255',
            'booked_by_user_id' => 'sometimes|required|numeric',
            'event_id' => 'sometimes|required|numeric',
        ];
    }
}
