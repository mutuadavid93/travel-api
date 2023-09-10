<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TravelRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // HINT: you can validate only admin users can perform this request here
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            "is_public" => "boolean",
            // unique name for travels i.e. no dups
            "name" => ["required", "unique:travels"],
            "description" => ["required"],
            "number_of_days" => ["required", "integer"]
        ];
    }
}
