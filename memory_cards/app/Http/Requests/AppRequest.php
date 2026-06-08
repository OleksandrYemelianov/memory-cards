<?php

namespace App\Http\Requests;

use App\Traits\AppResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;

class AppRequest extends FormRequest
{
    use AppResponse;

    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Build the response returned when validation fails.
     */
    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            $this->responseJson($validator->errors(), 422)
        );
    }
}
