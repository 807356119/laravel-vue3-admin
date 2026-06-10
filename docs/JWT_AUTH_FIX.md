# 🔧 JWT认证问题排查

## 问题："Unauthenticated"

这个错误表示JWT Token验证失败。

---

## ✅ 解决方案

### 1. 检查JWT密钥
```bash
cd backend
php artisan jwt:secret
php artisan config:cache
```

### 2. 前端正确传递Token

**Axios拦截器配置：**

编辑 `frontend/src/api/request.js`（或axios配置文件）：

```javascript
import axios from 'axios'

const service = axios.create({
  baseURL: 'http://localhost:8000/api',
  timeout: 10000
})

// 请求拦截器 - 自动添加Token
service.interceptors.request.use(
  config => {
    const token = localStorage.getItem('token')
    if (token) {
      config.headers['Authorization'] = `Bearer ${token}`
    }
    return config
  },
  error => {
    return Promise.reject(error)
  }
)

// 响应拦截器 - 处理401错误
service.interceptors.response.use(
  response => response.data,
  error => {
    if (error.response?.status === 401) {
      // Token过期或无效
      localStorage.removeItem('token')
      localStorage.removeItem('user')
      window.location.href = '/login'
    }
    return Promise.reject(error)
  }
)

export default service
```

### 3. 登录后保存Token

**前端stores/user.js：**

```javascript
async login(credentials) {
  const response = await api.post('/auth/login', credentials)
  
  // 保存Token
  localStorage.setItem('token', response.data.access_token)
  localStorage.setItem('user', JSON.stringify(response.data.user))
  localStorage.setItem('permissions', JSON.stringify(response.data.permissions))
  
  this.token = response.data.access_token
  this.userInfo = response.data.user
  this.permissions = response.data.permissions
  
  return response
}
```

---

## 🧪 测试步骤

### 1. 登录获取Token
```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@example.com","password":"123456"}'
```

**复制返回的 `access_token`**

### 2. 使用Token访问接口
```bash
curl -X GET http://localhost:8000/api/auth/me \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

---

## 📝 常见问题

### 问题1: Token格式错误
**错误：** Authorization头格式不对

**解决：** 必须是 `Bearer TOKEN`，注意Bearer后有空格

### 问题2: Token过期
**错误：** Token已过期

**解决：** 调用 `/api/auth/refresh` 刷新Token

### 问题3: JWT密钥未配置
**错误：** JWT_SECRET未设置

**解决：**
```bash
cd backend
php artisan jwt:secret
php artisan config:cache
```

---

## 🔍 调试方法

1. **查看请求头**
打开浏览器开发者工具 → Network → 查看请求头是否包含：
```
Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGci...
```

2. **查看Token是否保存**
```javascript
console.log(localStorage.getItem('token'))
```

3. **测试Token是否有效**
```bash
# 使用实际Token测试
curl -X GET http://localhost:8000/api/auth/me \
  -H "Authorization: Bearer YOUR_ACTUAL_TOKEN"
```

---

**配置完成后重新登录即可！** 🔐
