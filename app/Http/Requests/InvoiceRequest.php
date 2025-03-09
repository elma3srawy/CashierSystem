<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InvoiceRequest extends FormRequest
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
        $rules =  $this->onStore();
        if ($this->status == 'pending') {
            $rules['date_of_receipt'] = ['required', 'date', 'date_format:Y-m-d'];
            $rules['return_date'] = ['required', 'date', 'date_format:Y-m-d','after:' . $this->date_of_receipt];
        }
        return $rules;
    }

    public function onStore(): array
    {
        return [
            'status' => 'required|in:pending,inactive',
            'name' => 'required|string|min:2|max:255',
            'phone' => 'required|string|size:11|regex:/^01[0125]\d{8}$/',
            'city' => 'nullable|string|min:2|max:255|required_without:address',
            'address' => 'nullable|string|min:2|max:255|required_without:city',
            'list-product' => 'nullable|required_without:list-product-1',
            'list-product-1' => 'nullable|required_without:list-product',
            'list-product.*.product_id' => ['required','exists:products,id'],
            'list-product.*.price' => 'required|numeric|min:0|max:1000000|gte:list-product.*.payment',
            'list-product.*.payment' => 'required|numeric|min:0|max:1000000',
            'list-product-1.*.title' => 'required|string|max:255|regex:/^(?!\d+$).+$/',
            'list-product-1.*.data' => 'nullable|string|max:255',
            'list-product-1.*.price' => 'required|numeric|min:0|max:1000000|gte:list-product-1.*.payment',
            'list-product-1.*.payment' => 'required|numeric|min:0|max:1000000',
        ];
    }
    public function onUpdate(): array
    {
        return [
            'invoice_id' => 'required|exists:invoices,id',
            'client_id' => 'required|exists:clients,id',
            'status' => 'required|in:pending,inactive',
            'name' => 'required|string|min:2|max:255',
            'phone' => 'required|string|size:11|regex:/^01[0125]\d{8}$/',
            'address' => 'required|string|min:2|max:255',
            'list-product' => 'nullable|required_without:list-product-1',
            'list-product-1' => 'nullable|required_without:list-product',
            'list-product.*.product_id' => ['required','exists:products,id'],
            'list-product.*.price' => 'required|numeric|min:0|max:1000000|gte:list-product.*.payment',
            'list-product.*.payment' => 'required|numeric|min:0|max:1000000',
            'list-product-1.*.title' => 'required|string|max:255|regex:/^(?!\d+$).+$/',
            'list-product-1.*.data' => 'nullable|string|max:255',
            'list-product-1.*.price' => 'required|numeric|min:0|max:1000000|gte:list-product-1.*.payment',
            'list-product-1.*.payment' => 'required|numeric|min:0|max:1000000',
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => 'حالة الفاتورة مطلوبة.',
            'status.in' => 'حالة الفاتورة يجب أن تكون إما "بيع" أو "ايجار".',
            'name.required' => 'اسم العميل مطلوب.',
            'name.string' => 'اسم العميل يجب أن يكون نصًا.',
            'name.min' => 'اسم العميل يجب أن يحتوي على حرفين على الأقل.',
            'name.max' => 'اسم العميل يجب أن لا يتجاوز 255 حرفًا.',
            'phone.required' => 'رقم الهاتف مطلوب.',
            'phone.size' => 'رقم الهاتف يجب أن يكون مكون من 11 رقمًا.',
            'phone.regex' => 'رقم الهاتف يجب أن يبدأ بـ 015 او 010 او 011 او 012.',
            'city.required_without' => 'المدينة أو العنوان يجب تحديد واحد منهما.',
            'address.required_without' => 'العنوان أو المدينة يجب تحديد واحد منهما.',
            'list-product.required_without' => 'يجب تحديد قائمة المنتجات أو المنتجات البديلة.',
            // 'list-product-1.required_without' => 'يجب تحديد قائمة المنتجات أو المنتجات البديلة.',
            'list-product.*.product_id.required' => 'معرف المنتج مطلوب.',
            'list-product.*.product_id.exists' => 'المنتج المحدد غير موجود.',
            'list-product.*.price.required' => 'السعر مطلوب.',
            'list-product.*.price.numeric' => 'السعر يجب أن يكون عددًا.',
            'list-product.*.price.max' => 'السعر يجب أن يكون اصغر من أو يساوي 1000000.',
            'list-product.*.price.min' => 'السعر يجب أن يكون أكبر من أو يساوي 0.',
            'list-product.*.payment.required' => 'الدفع مطلوب.',
            'list-product.*.payment.numeric' => 'الدفع يجب أن يكون عددًا.',
            'list-product.*.payment.max' => 'السعر يجب أن يكون اصغر من أو يساوي 1000000.',
            'list-product.*.payment.min' => 'الدفع يجب أن يكون أكبر من أو يساوي 0.',
            'list-product.*.payment.gte' => 'المدفوع يجب أن يكون أكبر من أو يساوي المبلغ المدفوع.',
            'list-product.*.price.gte' => 'السعر يجب أن يكون أكبر من أو يساوي المبلغ المدفوع.',
            'list-product-1.*.payment.max' => 'السعر يجب أن يكون اصغر من أو يساوي 1000000.',
            'list-product-1.*.title.required' => 'عنوان المنتج البديل مطلوب.',
            'list-product-1.*.title.regex' => 'عنوان المنتج البديل يجب أن يحتوي على نص غير مكون من أرقام فقط.',
            'list-product-1.*.price.max' => 'السعر يجب أن يكون اصغر من أو يساوي 1000000.',
            'list-product-1.*.price.required' => 'السعر مطلوب.',
            'list-product-1.*.price.numeric' => 'السعر يجب أن يكون عددًا.',
            'list-product-1.*.price.min' => 'السعر يجب أن يكون أكبر من أو يساوي 0.',
            'list-product-1.*.price.gte' => 'السعر يجب أن يكون أكبر من أو يساوي المبلغ المدفوع.',
            'list-product-1.*.payment.gte' => 'المدفوع يجب أن يكون أكبر من أو يساوي المبلغ المدفوع.',
            'list-product-1.*.payment.required' => 'الدفع مطلوب.',
            'list-product-1.*.payment.numeric' => 'الدفع يجب أن يكون عددًا.',
            'list-product-1.*.payment.min' => 'الدفع يجب أن يكون أكبر من أو يساوي 0.',
            'date_of_receipt.required' =>'تاريخ الاستلام مطلوب.',
            'date_of_receipt.date' => 'تاريخ الاستلام يجب أن يكون تاريخًا صحيحًا.',
            'date_of_receipt.date_format' => 'تاريخ الاستلام يجب أن يكون بصيغة YYYY-MM-DD.',
            'return_date.required' => 'تاريخ الإرجاع مطلوب.',
            'return_date.date' => 'تاريخ الإرجاع يجب أن يكون تاريخًا صحيحًا.',
            'return_date.date_format' => 'تاريخ الإرجاع يجب أن يكون بصيغة YYYY-MM-DD.',
            'return_date.after' => 'تاريخ الإرجاع يجب أن يكون بعد تاريخ الاستلام.',
        ];
    }
}
