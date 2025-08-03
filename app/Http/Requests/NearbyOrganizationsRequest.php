<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class NearbyOrganizationsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Или ваша логика авторизации
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'lat' => 'required|numeric|between:-90,90',
            'lng' => 'required|numeric|between:-180,180',
            'radius' => 'nullable|numeric|min:0',
            'ne_lat' => 'nullable|numeric|between:-90,90',
            'ne_lng' => 'nullable|numeric|between:-180,180',
            'sw_lat' => 'nullable|numeric|between:-90,90',
            'sw_lng' => 'nullable|numeric|between:-180,180',
        ];
    }

    /**
     * Дополнительные сообщения об ошибках
     */
    public function messages(): array
    {
        return [
            'lat.required' => 'Широта (lat) обязательна для поиска',
            'lat.between' => 'Широта должна быть между -90 и 90 градусами',
            'lng.required' => 'Долгота (lng) обязательна для поиска',
            'lng.between' => 'Долгота должна быть между -180 и 180 градусами',
            'radius.min' => 'Радиус не может быть отрицательным',
        ];
    }

    /**
     * Валидация взаимосвязанных полей
     */
    public function withValidator(Validator $validator)
    {
        $validator->after(function ($validator) {
            if (
                $this->hasAny(['ne_lat', 'ne_lng', 'sw_lat', 'sw_lng']) &&
                !$this->hasAll(['ne_lat', 'ne_lng', 'sw_lat', 'sw_lng'])
            ) {
                $validator->errors()->add(
                    'bounds',
                    'Для поиска по области необходимо указать все 4 координаты (ne_lat, ne_lng, sw_lat, sw_lng)'
                );
            }
        });
    }

    /**
     * Формат JSON-ответа при ошибках валидации
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Validation errors',
            'errors' => $validator->errors()
        ], 422));
    }
}
