<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class UpdateAccountRequest extends FormRequest
{
    public function authorize()
    {
        return Auth::check();
    }

    public function rules()
    {
    $userId = Auth::id() ?? null;
        return [
            'name' => 'required|string|max:255',
            'phone' => [
                'nullable',
                'string',
                'max:20',
                'unique:users,phone,' . $userId,
            ],
            'address' => 'nullable|string|max:500',
        ];
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(Validator $validator)
    {
        // Log validation errors and incoming data to help debugging
        Log::warning('UpdateAccountRequest validation failed', [
            'user_id' => Auth::id(),
            'input' => $this->all(),
            'errors' => $validator->errors()->all(),
        ]);

        throw new HttpResponseException(
            redirect()->back()->withErrors($validator)->withInput()
        );
    }
}
