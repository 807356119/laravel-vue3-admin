<?php

namespace App\Http\Requests\Account;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreAccountRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:bank,email,social,website,other'],
            'account_name' => ['nullable', 'string', 'max:255'],
            'account_number' => ['nullable', 'string', 'max:255'],
            'password' => ['nullable', 'string'],
            'bank_name' => ['nullable', 'string', 'max:255'],
            'website' => ['nullable', 'url', 'max:255'],
            'description' => ['nullable', 'string'],
            'images' => ['nullable', 'array'],
            'images.*' => ['string'],
            'extra_fields' => ['nullable', 'array'],
            'category' => ['nullable', 'string', 'max:50'],
            'is_favorite' => ['nullable', 'boolean'],
            'expires_at' => ['nullable', 'date'],
        ];
    }

    public function attributes()
    {
        return [
            'title' => '标题',
            'type' => '类型',
            'account_name' => '账户名',
            'account_number' => '账号',
            'password' => '密码',
            'bank_name' => '银行名称',
            'website' => '网站',
            'description' => '备注',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'code' => 422,
                'message' => '数据验证失败',
                'errors' => $validator->errors(),
                'timestamp' => now()->toIso8601String(),
            ], 422)
        );
    }
}
