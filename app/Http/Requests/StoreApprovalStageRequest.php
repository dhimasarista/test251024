<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreApprovalStageRequest extends FormRequest
{
    public function rules()
    {
        return [
            'approver_id' => 'required|exists:approvers,id|unique:approval_stages,approver_id',
        ];
    }
    public function messages()
    {
        return [
        ];
    }
}
