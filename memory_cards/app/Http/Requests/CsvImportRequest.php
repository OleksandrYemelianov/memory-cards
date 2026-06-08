<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class CsvImportRequest extends AppRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'csv_file'  => ['required', 'file', 'mimes:csv,txt', 'max:2048'],
            'group_app' => [
                'required',
                'integer',
                // The group must exist AND belong to the current user, so a
                // user cannot inject cards into someone else's group.
                Rule::exists('groups', 'id')->where('user_id', Auth::id()),
            ],
        ];
    }
}
