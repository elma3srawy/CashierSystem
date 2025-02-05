<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
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
    public function rules()
    {
        return $this->method() == "POST" ? $this->onStore() : $this->onUpdate();
    }

    public function onStore(): array
    {
        return [
            'section_id' => ['required', 'integer' ,'exists:sections,id'],
            'color' => ['nullable', 'string', 'min:2', 'max:20', 'required_without_all:model,size'],
            'model' => ['nullable', 'string', 'min:2', 'max:20', 'required_without_all:color,size'],
            'size' => ['nullable', 'integer', 'min:1', 'max:100', 'required_without_all:color,model'],
            'image' => ['nullable' , 'file' , 'image' , 'mimes:jpeg,png,gif,bmp,svg,tiff,webp'],
            'quantity' => ['required' , 'integer' , 'min:1', 'max:1000'],
        ];
    }
    public function onUpdate(): array
    {
        return [
            'color' => ['nullable', 'string', 'min:2', 'max:20', 'required_without_all:model,size'],
            'model' => ['nullable', 'string', 'min:2', 'max:20', 'required_without_all:color,size'],
            'size' => ['nullable', 'integer', 'min:1', 'max:100', 'required_without_all:color,model'],
            'image' => ['nullable' , 'file' , 'image' , 'mimes:jpeg,png,gif,bmp,svg,tiff,webp'],
            'quantity' => ['required' , 'integer' , 'min:1', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'section_id.required' => 'حقل القسم مطلوب.',
            'section_id.integer' => 'القسم يجب أن يكون رقمًا صحيحًا.',
            'section_id.exists' => 'القسم المحدد غير موجود.',

            'color.required_without_all' => 'يجب تحديد اللون إذا لم يتم تحديد الموديل أو الحجم.',
            'color.string' => 'اللون يجب أن يكون نصًا.',
            'color.min' => 'اللون يجب أن يحتوي على حرفين على الأقل.',
            'color.max' => 'اللون يجب أن لا يتجاوز 20 حرفًا.',

            // 'model.required_without_all' => 'يجب تحديد الموديل إذا لم يتم تحديد اللون أو الحجم.',
            'model.string' => 'الموديل يجب أن يكون نصًا.',
            'model.min' => 'الموديل يجب أن يحتوي على حرفين على الأقل.',
            'model.max' => 'الموديل يجب أن لا يتجاوز 20 حرفًا.',

            // 'size.required_without_all' => 'يجب تحديد الحجم إذا لم يتم تحديد اللون أو الموديل.',
            'size.integer' => 'الحجم يجب أن يكون رقمًا صحيحًا.',
            'size.min' => 'الحجم يجب أن يكون على الأقل 1.',
            'size.max' => 'الحجم يجب أن لا يتجاوز 100.',

            'image.file' => 'يجب أن يكون الملف صورة.',
            'image.image' => 'الملف يجب أن يكون صورة.',
            'image.mimes' => 'الصورة يجب أن تكون من نوع jpeg, png, gif, bmp, svg, tiff, webp.',

            'quantity.required' => 'حقل الكمية مطلوب.',
            'quantity.integer' => 'الكمية يجب أن تكون رقمًا صحيحًا.',
            'quantity.min' => 'الكمية يجب أن تكون على الأقل 1.',
            'quantity.max' => 'الكمية يجب أن لا تتجاوز 1000.',
        ];
    }
}
