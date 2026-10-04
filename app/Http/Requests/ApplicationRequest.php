<?php

namespace App\Http\Requests;

use App\Models\Application;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ApplicationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'company' => ['required', 'string', 'max:150'],
            'position' => ['required', 'string', 'max:150'],
            'location' => ['required', 'string', 'max:150'],
            'job_type' => ['required', 'string', Rule::in(Application::JOB_TYPES)],
            'salary' => ['nullable', 'numeric', 'min:0'],
            'applied_date' => ['required', 'date'],
            'follow_up_date' => ['nullable', 'date'],
            'status' => ['required', 'string', Rule::in(Application::STATUSES)],
            'job_url' => ['nullable', 'url', 'max:2048'],
            'notes' => ['nullable', 'string', 'max:5000'],
        ];
    }

    /**
     * Custom user-friendly validation error messages.
     */
    public function messages(): array
    {
        return [
            'company.required' => 'The company name is required.',
            'position.required' => 'The position/job title is required.',
            'location.required' => 'The job location is required.',
            'job_type.required' => 'Please select a valid job type.',
            'job_type.in' => 'Selected job type is invalid.',
            'salary.numeric' => 'The salary must be a valid number.',
            'applied_date.required' => 'The date applied is required.',
            'applied_date.date' => 'Please provide a valid applied date.',
            'follow_up_date.date' => 'Please provide a valid follow-up date.',
            'status.required' => 'Please select an application status.',
            'status.in' => 'Selected status is invalid.',
            'job_url.url' => 'Please provide a valid website URL (starting with http:// or https://).',
        ];
    }
}
