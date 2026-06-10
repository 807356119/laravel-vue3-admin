# ✅ 表单验证问题已修复！

## 🐛 问题原因

1. **邮箱唯一性验证**
   - 更新时没有排除当前用户的邮箱
   - 导致"该邮箱已被使用"错误

2. **密码字段验证**
   - 更新用户时，前端可能不传password字段
   - 但后端仍然验证password，导致"must be a string"错误

## ✅ 已修复

### UpdateUserRequest.php

1. **修复路由参数获取**
```php
$userId = $this->route('id') ?? $this->route('user');
```

2. **password字段改为可选**
```php
'password' => ['sometimes', 'nullable', 'string', 'min:6', 'max:32']
```

3. **添加prepareForValidation方法**
```php
protected function prepareForValidation()
{
    // 如果密码为空，移除该字段
    if ($this->has('password') && ($this->password === null || $this->password === '')) {
        $this->request->remove('password');
    }
}
```

---

## 🎯 现在的验证规则

### 更新用户时

- ✅ `name` - 可选，字符串
- ✅ `email` - 可选，邮箱格式，唯一（排除自己）
- ✅ `password` - **可选**，为空时自动移除，不更新密码
- ✅ `role_id` - 可选，必须存在
- ✅ `status` - 可选，active/inactive

---

## 🧪 测试步骤

1. **编辑用户（不修改密码）**
   - 只修改姓名或邮箱
   - 不填写密码字段
   - 应该能成功更新

2. **编辑用户（修改密码）**
   - 填写新密码（至少6位）
   - 应该能成功更新密码

3. **重置密码**
   - 使用专门的"重置密码"按钮
   - 单独更新密码

---

## ✅ 完成清单

- ✅ 修复路由参数获取
- ✅ password字段改为nullable
- ✅ 空密码自动移除
- ✅ 邮箱唯一性验证排除自己
- ✅ role_id类型改为integer

**现在可以正常编辑用户了！** 🎉
