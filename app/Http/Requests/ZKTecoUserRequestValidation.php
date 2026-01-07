<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;


class ZKTecoUserRequestValidation extends FormRequest
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
        $device = $this->route('device_sn') ?? null;
        $rules = [
            "device_sn" =>
            $device ?
                [
                    "nullable"
                ] :
                [
                    "required",
                    Rule::exists("zk_teco_devices", "device_sn"),
                    "string"
                ],

            "device_user_id" => [
                "required",
                "string",
                Rule::unique("zk_teco_users", "device_user_id")

            ],
            "device_user_name" => ["required", "string", "max:23"],
            "device_user_uuid" => [
                "required",
                "numeric",
                Rule::unique("zk_teco_users", "device_user_uuid")
            ]
        ];
        return $rules;
    }
    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'message' => 'Validation errors',
            'data'    => $validator->errors()->all(),
        ], 422));
    }
}
