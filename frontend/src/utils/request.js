import axios from 'axios'
import { ElMessage } from 'element-plus'

// 创建axios实例
const service = axios.create({
  baseURL: 'http://localhost:8000/api',
  timeout: 10000
})

// 请求拦截器 - 自动添加Token
service.interceptors.request.use(
  config => {
    // 从localStorage获取token
    const token = localStorage.getItem('token')
    if (token) {
      config.headers['Authorization'] = `Bearer ${token}`
    }
    return config
  },
  error => {
    console.error('请求错误:', error)
    return Promise.reject(error)
  }
)

// 响应拦截器 - 处理响应和错误
service.interceptors.response.use(
  response => {
    // 直接返回data
    return response.data
  },
  error => {
    console.error('响应错误:', error)

    // 处理401未授权错误
    if (error.response?.status === 401) {
      ElMessage.error('登录已过期，请重新登录')
      localStorage.removeItem('token')
      localStorage.removeItem('user')
      localStorage.removeItem('permissions')
      setTimeout(() => {
        window.location.href = '/login'
      }, 1000)
      return Promise.reject(new Error('未授权'))
    }

    // 处理403权限不足
    if (error.response?.status === 403) {
      const message = error.response.data?.message || '权限不足'
      ElMessage.error(message)
      return Promise.reject(new Error(message))
    }

    // 处理422验证错误
    if (error.response?.status === 422) {
      const errors = error.response.data?.errors
      if (errors) {
        const firstError = Object.values(errors)[0][0]
        ElMessage.error(firstError)
      }
      return Promise.reject(error.response.data)
    }

    // 其他错误
    const message = error.response?.data?.message || error.message || '请求失败'
    ElMessage.error(message)
    return Promise.reject(new Error(message))
  }
)

export default service
