<?php

namespace App\Http\Requests;

use App\Enums\LeadSource;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLeadRequest extends FormRequest
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
            'source' => ['required', 'string', Rule::in(LeadSource::values())],
            'name' => ['required', 'string', 'max:120'],
            'phone' => ['required', 'string', 'max:32'],
            'comment' => ['nullable', 'string', 'max:2000'],
            'consent' => ['accepted'],
            'quiz_answers' => ['nullable', 'array'],
            'quiz_answers.device' => ['nullable', 'string', 'max:32'],
            'quiz_answers.device_label' => ['nullable', 'string', 'max:64'],
            'quiz_answers.problems' => ['nullable', 'array'],
            'quiz_answers.problems.*' => ['string', 'max:255'],
            'quiz_answers.problem_custom' => ['nullable', 'string', 'max:500'],
            'quiz_answers.brand' => ['nullable', 'string', 'max:500'],
            'utm_source' => ['nullable', 'string', 'max:255'],
            'utm_medium' => ['nullable', 'string', 'max:255'],
            'utm_campaign' => ['nullable', 'string', 'max:255'],
            'utm_content' => ['nullable', 'string', 'max:255'],
            'utm_term' => ['nullable', 'string', 'max:255'],
            'gclid' => ['nullable', 'string', 'max:255'],
            'yclid' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'consent.accepted' => 'Необходимо согласие с политикой конфиденциальности.',
            'source.in' => 'Некорректный источник заявки.',
        ];
    }
}
