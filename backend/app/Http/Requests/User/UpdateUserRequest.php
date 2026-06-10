<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        // 获取路由参数中的用户ID
        $userId = $this->route('id') ?? $this->route('user');

        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'email', 'max:255', 'unique:users,email,' . $userId],
            // password是可选的，如果不传就不验证
            'password' => ['sometimes', 'nullable', 'string', 'min:6', 'max:32'],
            'role_id' => ['sometimes', 'integer', 'exists:roles,id'],
            'status' => ['sometimes', 'in:active,inactive'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'name' => '姓名',
            'email' => '邮箱',
            'password' => '密码',
            'role_id' => '角色',
            'status' => '状态',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'email.unique' => '该邮箱已被使用',
            'password.min' => '密码至少需要6位',
            'role_id.exists' => '选择的角色不存在',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        // 如果密码字段存在但为空，则移除它
        if ($this->has('password') && ($this->password === null || $this->password === '')) {
            $this->request->remove('password');
        }
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     *
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
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
