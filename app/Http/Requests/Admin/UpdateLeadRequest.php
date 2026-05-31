<?php

namespace App\Http\Requests\Admin;

use App\Models\Lead;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateLeadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'qualification_status' => ['nullable', Rule::in([Lead::STATUS_YES, Lead::STATUS_NO, ''])],
            'quality_status' => ['nullable', Rule::in([Lead::STATUS_YES, Lead::STATUS_NO, ''])],
            'admin_note' => ['nullable', 'string', 'max:5000'],
        ];
    }
}
