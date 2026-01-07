<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class ZKTecoDeviceRequestValidation extends FormRequest
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
        $device = $this->route('device_sn') ? $this->route('device_sn')->id : null;
        $rules =  [
            "device_sn" => [
                "required",
                "string",
                Rule::unique("zk_teco_devices", "device_sn")->ignore($device),
            ],
            "device_model" => [
                "required",
                "string"
            ],
            "device_loc" => [
                "required",
                "string"
            ],
            "device_name" => [
                "required",
                "string"
            ],
            "device_ip" => [
                "required",
                "string",
                Rule::unique("zk_teco_devices", "device_ip")->ignore($device),
            ],
            "device_port" => [
                "required",
                "string"
            ],
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
