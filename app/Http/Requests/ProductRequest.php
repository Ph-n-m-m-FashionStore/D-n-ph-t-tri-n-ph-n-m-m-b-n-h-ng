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
        return auth()->check() && auth()->user()->role === 'admin';
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $rules = [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'product_type' => 'required|string|in:clothing,accessories,shoes',
            'is_active' => 'boolean'
        ];

        // Nếu là tạo mới, bắt buộc phải có ảnh
        if ($this->isMethod('post')) {
            $rules['image'] = 'required|image|mimes:png,jpg,jpeg|max:2048';
        } else {
            // Nếu là cập nhật, ảnh là tùy chọn
            $rules['image'] = 'nullable|image|mimes:png,jpg,jpeg|max:2048';
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Tên sản phẩm là bắt buộc.',
            'name.max' => 'Tên sản phẩm không được vượt quá 255 ký tự.',
            'description.required' => 'Mô tả sản phẩm là bắt buộc.',
            'price.required' => 'Giá sản phẩm là bắt buộc.',
            'price.numeric' => 'Giá sản phẩm phải là số.',
            'price.min' => 'Giá sản phẩm phải lớn hơn hoặc bằng 0.',
            'product_type.required' => 'Loại sản phẩm là bắt buộc.',
            'product_type.in' => 'Loại sản phẩm không hợp lệ.',
            'image.required' => 'Ảnh sản phẩm là bắt buộc.',
            'image.image' => 'File phải là ảnh.',
            'image.mimes' => 'Ảnh phải có định dạng PNG, JPG hoặc JPEG.',
            'image.max' => 'Kích thước ảnh không được vượt quá 2MB.',
        ];
    }
}
