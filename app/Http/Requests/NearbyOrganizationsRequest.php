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
            'lat' => 'required_with:lng,radius|numeric|between:-90,90',
            'lng' => 'required_with:lat,radius|numeric|between:-180,180',
            'radius' => 'nullable|numeric|min:0',
            'ne_lat' => 'required_with:ne_lng,sw_lat,sw_lng|numeric|between:-90,90',
            'ne_lng' => 'required_with:ne_lat,sw_lat,sw_lng|numeric|between:-180,180',
            'sw_lat' => 'required_with:ne_lat,ne_lng,sw_lng|numeric|between:-90,90',
            'sw_lng' => 'required_with:ne_lat,ne_lng,sw_lat|numeric|between:-180,180',
        ];
    }

    /**
     * Дополнительные сообщения об ошибках
     */
    public function messages(): array
    {
        return [
            'lat' => 'Широта (lat) обязательна для поиска',
            'lat.between' => 'Широта должна быть между -90 и 90 градусами',
            'lng' => 'Долгота (lng) обязательна для поиска',
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
            $hasRadiusParams = $this->filled(['lat', 'lng']);
            $hasBoundsParams = $this->filled(['ne_lat', 'ne_lng', 'sw_lat', 'sw_lng']);

            if (!$hasRadiusParams && !$hasBoundsParams) {
                $validator->errors()->add(
                    'parameters',
                    'Необходимо указать либо (lat, lng, [radius]), либо (ne_lat, ne_lng, sw_lat, sw_lng)'
                );
            }

            if ($hasRadiusParams && $hasBoundsParams) {
                $validator->errors()->add(
                    'parameters',
                    'Используйте только один метод поиска: по радиусу ИЛИ по прямоугольной области'
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
