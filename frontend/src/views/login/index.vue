<template>
  <div class="login-container">
    <!-- 背景 -->
    <div class="login-bg">
      <div class="grid-lines"></div>
      <div class="glow-orb glow-orb-1"></div>
      <div class="glow-orb glow-orb-2"></div>
      <div class="particles">
        <div v-for="i in 20" :key="i" class="particle" :style="getParticleStyle(i)"></div>
      </div>
    </div>

    <!-- 登录表单 -->
    <div class="login-form-wrapper">
      <div class="login-header">
        <div class="logo">
          <div class="logo-icon">
            <el-icon><DataAnalysis /></el-icon>
          </div>
          <div class="logo-text">
            <h1>Admin Control System</h1>
            <p>智能管理控制中心</p>
          </div>
        </div>
      </div>

      <div class="login-card">
        <h2 class="form-title">系统登录</h2>
        <p class="form-subtitle">欢迎使用管理控制系统</p>

        <el-form
          :model="form"
          :rules="rules"
          ref="loginForm"
          class="login-form"
          @keyup.enter="handleLogin"
        >
          <el-form-item prop="email">
            <div class="form-item-wrapper">
              <el-icon class="input-icon"><User /></el-icon>
              <el-input
                v-model="form.email"
                placeholder="请输入邮箱账号"
                size="large"
                clearable
              />
            </div>
          </el-form-item>

          <el-form-item prop="password">
            <div class="form-item-wrapper">
              <el-icon class="input-icon"><Lock /></el-icon>
              <el-input
                v-model="form.password"
                type="password"
                placeholder="请输入登录密码"
                size="large"
                show-password
                clearable
              />
            </div>
          </el-form-item>

          <el-form-item class="form-actions">
            <el-checkbox v-model="rememberMe">
              <span class="checkbox-label">记住密码</span>
            </el-checkbox>
            <el-link type="primary" :underline="false" class="forgot-link">
              忘记密码?
            </el-link>
          </el-form-item>

          <el-form-item>
            <el-button
              type="primary"
              size="large"
              class="login-button"
              :loading="loading"
              @click="handleLogin"
            >
              <span v-if="!loading">立即登录</span>
              <span v-else>登录中...</span>
            </el-button>
          </el-form-item>
        </el-form>

        <div class="demo-hint">
          <div class="hint-title">
            <el-icon><InfoFilled /></el-icon>
            测试账号
          </div>
          <div class="hint-content">
            <div class="hint-item">
              <span class="hint-label">管理员：</span>
              <span class="hint-value">admin@example.com / 123456</span>
            </div>
            <div class="hint-item">
              <span class="hint-label">普通用户：</span>
              <span class="hint-value">user@example.com / 123456</span>
            </div>
          </div>
        </div>
      </div>

      <div class="login-footer">
        <p>© 2026 Admin Control System. All rights reserved.</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { useRouter } from 'vue-router'
import { useUserStore } from '@/stores/user'
import { ElMessage } from 'element-plus'

const router = useRouter()
const userStore = useUserStore()
const loginForm = ref(null)
const loading = ref(false)
const rememberMe = ref(false)

const form = reactive({
  email: '',
  password: ''
})

const rules = {
  email: [
    { required: true, message: '请输入邮箱', trigger: 'blur' },
    { type: 'email', message: '请输入正确的邮箱格式', trigger: 'blur' }
  ],
  password: [
    { required: true, message: '请输入密码', trigger: 'blur' },
    { min: 6, message: '密码至少6位', trigger: 'blur' }
  ]
}

const handleLogin = async () => {
  if (!loginForm.value) return

  await loginForm.value.validate(async (valid) => {
    if (valid) {
      loading.value = true
      try {
        await userStore.login(form)
        ElMessage.success({
          message: '登录成功！',
          duration: 2000
        })
        setTimeout(() => {
          router.push('/')
        }, 500)
      } catch (error) {
        ElMessage.error('登录失败，请检查账号密码')
      } finally {
        loading.value = false
      }
    }
  })
}

const getParticleStyle = (index) => {
  return {
    left: Math.random() * 100 + '%',
    top: Math.random() * 100 + '%',
    animationDelay: Math.random() * 3 + 's',
    animationDuration: (Math.random() * 3 + 2) + 's'
  }
}
</script>

<style scoped>
.login-container {
  position: relative;
  min-height: 100vh;
  width: 100vw;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
  overflow: hidden;
}

/* 背景 */
.login-bg {
  position: absolute;
  inset: 0;
  z-index: 0;
}

.grid-lines {
  position: absolute;
  width: 100%;
  height: 100%;
  background-image:
    linear-gradient(rgba(255, 255, 255, 0.08) 1px, transparent 1px),
    linear-gradient(90deg, rgba(255, 255, 255, 0.08) 1px, transparent 1px);
  background-size: 50px 50px;
  animation: gridMove 20s linear infinite;
}

@keyframes gridMove {
  0% { transform: translate(0, 0); }
  100% { transform: translate(50px, 50px); }
}

.glow-orb {
  position: absolute;
  border-radius: 50%;
  filter: blur(100px);
  opacity: 0.3;
  animation: float 15s ease-in-out infinite;
}

.glow-orb-1 {
  width: 500px;
  height: 500px;
  background: radial-gradient(circle, rgba(255, 255, 255, 0.15), transparent);
  top: -150px;
  right: -150px;
}

.glow-orb-2 {
  width: 400px;
  height: 400px;
  background: radial-gradient(circle, rgba(255, 255, 255, 0.1), transparent);
  bottom: -100px;
  left: -100px;
  animation-delay: -7s;
}

@keyframes float {
  0%, 100% { transform: translate(0, 0); }
  50% { transform: translate(30px, -30px); }
}

.particles {
  position: absolute;
  width: 100%;
  height: 100%;
}

.particle {
  position: absolute;
  width: 4px;
  height: 4px;
  background: rgba(255, 255, 255, 0.8);
  border-radius: 50%;
  animation: particleFloat ease-in-out infinite;
  box-shadow: 0 0 10px rgba(255, 255, 255, 0.5);
}

@keyframes particleFloat {
  0%, 100% { transform: translateY(0); opacity: 0; }
  50% { opacity: 1; }
}

/* 登录表单 */
.login-form-wrapper {
  position: relative;
  z-index: 1;
  width: 480px;
  animation: slideUp 0.6s ease-out;
}

@keyframes slideUp {
  from {
    opacity: 0;
    transform: translateY(50px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.login-header {
  text-align: center;
  margin-bottom: 32px;
}

.logo {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 16px;
}

.logo-icon {
  width: 80px;
  height: 80px;
  background: rgba(255, 255, 255, 0.95);
  border-radius: 20px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 40px;
  color: #667eea;
  box-shadow: 0 8px 32px rgba(255, 255, 255, 0.3);
  animation: pulse 2s infinite;
}

@keyframes pulse {
  0%, 100% { transform: scale(1); }
  50% { transform: scale(1.05); }
}

.logo-text h1 {
  font-size: 28px;
  font-weight: 700;
  color: #fff;
  margin: 0 0 4px 0;
  letter-spacing: 1px;
}

.logo-text p {
  font-size: 14px;
  color: #64748b;
  margin: 0;
}

.login-card {
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(30px);
  border: 1px solid rgba(255, 255, 255, 0.3);
  border-radius: 16px;
  padding: 40px;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
}

.form-title {
  font-size: 24px;
  font-weight: 600;
  color: #333;
  text-align: center;
  margin: 0 0 8px 0;
}

.form-subtitle {
  font-size: 14px;
  color: #666;
  text-align: center;
  margin: 0 0 32px 0;
}

/* 表单样式 */
.login-form :deep(.el-form-item) {
  margin-bottom: 24px;
}

.form-item-wrapper {
  position: relative;
  display: flex;
  align-items: center;
}

.input-icon {
  position: absolute;
  left: 16px;
  font-size: 18px;
  color: #999;
  z-index: 1;
}

.form-item-wrapper :deep(.el-input__wrapper) {
  padding-left: 48px;
  background: rgba(10, 14, 39, 0.6);
  border: 1px solid rgba(14, 165, 233, 0.2);
  border-radius: 8px;
  box-shadow: none;
  transition: all 0.3s;
}

.form-item-wrapper :deep(.el-input__wrapper:hover) {
  border-color: rgba(14, 165, 233, 0.4);
}

.form-item-wrapper :deep(.el-input__wrapper.is-focus) {
  border-color: #0ea5e9;
  box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.1);
}

.form-item-wrapper :deep(.el-input__inner) {
  color: #fff;
}

.form-item-wrapper :deep(.el-input__inner::placeholder) {
  color: #475569;
}

.form-actions {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 32px !important;
}

.checkbox-label {
  color: #666;
  font-size: 14px;
}

.forgot-link {
  color: #667eea;
  font-size: 14px;
}

.login-button {
  width: 100%;
  height: 48px;
  background: linear-gradient(135deg, #667eea, #764ba2);
  border: none;
  border-radius: 8px;
  font-size: 16px;
  font-weight: 600;
  transition: all 0.3s;
}

.login-button:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
}

/* 测试账号提示 */
.demo-hint {
  margin-top: 24px;
  padding: 16px;
  background: rgba(102, 126, 234, 0.1);
  border: 1px solid rgba(102, 126, 234, 0.2);
  border-radius: 8px;
}

.hint-title {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 14px;
  font-weight: 500;
  color: #667eea;
  margin-bottom: 12px;
}

.hint-content {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.hint-item {
  font-size: 13px;
  color: #666;
  line-height: 1.6;
}

.hint-label {
  color: #999;
}

.hint-value {
  color: #333;
  font-family: 'Courier New', monospace;
}

.login-footer {
  text-align: center;
  margin-top: 24px;
  font-size: 13px;
  color: #475569;
}

/* 响应式 */
@media (max-width: 768px) {
  .login-form-wrapper {
    width: 90%;
    margin: 20px;
  }

  .login-card {
    padding: 32px 24px;
  }

  .logo-icon {
    width: 64px;
    height: 64px;
    font-size: 32px;
  }

  .logo-text h1 {
    font-size: 24px;
  }
}
</style>
