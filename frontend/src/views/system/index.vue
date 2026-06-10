<template>
  <div class="system-container">
    <el-card>
      <template #header>
        <span>系统设置</span>
      </template>

      <el-form :model="form" :rules="rules" ref="formRef" label-width="150px">
        <el-divider content-position="left">基本设置</el-divider>

        <el-form-item label="网站名称" prop="site_name">
          <el-input v-model="form.site_name" />
        </el-form-item>

        <el-form-item label="网站Logo" prop="site_logo">
          <el-input v-model="form.site_logo" placeholder="Logo URL" />
        </el-form-item>

        <el-form-item label="联系邮箱" prop="contact_email">
          <el-input v-model="form.contact_email" />
        </el-form-item>

        <el-divider content-position="left">安全设置</el-divider>

        <el-form-item label="密码最小长度" prop="password_min_length">
          <el-input-number v-model="form.password_min_length" :min="6" :max="20" />
        </el-form-item>

        <el-form-item label="登录失败锁定次数" prop="login_max_attempts">
          <el-input-number v-model="form.login_max_attempts" :min="3" :max="10" />
        </el-form-item>

        <el-form-item label="Session超时时间(分钟)" prop="session_timeout">
          <el-input-number v-model="form.session_timeout" :min="10" :max="1440" />
        </el-form-item>

        <el-divider content-position="left">邮件设置</el-divider>

        <el-form-item label="SMTP服务器" prop="smtp_host">
          <el-input v-model="form.smtp_host" />
        </el-form-item>

        <el-form-item label="SMTP端口" prop="smtp_port">
          <el-input-number v-model="form.smtp_port" :min="1" :max="65535" />
        </el-form-item>

        <el-form-item label="SMTP用户名" prop="smtp_username">
          <el-input v-model="form.smtp_username" />
        </el-form-item>

        <el-form-item label="SMTP密码" prop="smtp_password">
          <el-input v-model="form.smtp_password" type="password" show-password />
        </el-form-item>

        <el-form-item label="发件人邮箱" prop="smtp_from_email">
          <el-input v-model="form.smtp_from_email" />
        </el-form-item>

        <el-form-item>
          <el-button type="primary" @click="handleSubmit" :loading="loading">
            保存设置
          </el-button>
          <el-button @click="loadSettings">重置</el-button>
        </el-form-item>
      </el-form>
    </el-card>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { getSystemSettings, updateSystemSettings } from '@/api/system'
import { ElMessage } from 'element-plus'

const loading = ref(false)
const formRef = ref(null)

const form = reactive({
  site_name: '',
  site_logo: '',
  contact_email: '',
  password_min_length: 6,
  login_max_attempts: 5,
  session_timeout: 120,
  smtp_host: '',
  smtp_port: 587,
  smtp_username: '',
  smtp_password: '',
  smtp_from_email: ''
})

const rules = {
  site_name: [{ required: true, message: '请输入网站名称', trigger: 'blur' }],
  contact_email: [
    { required: true, message: '请输入联系邮箱', trigger: 'blur' },
    { type: 'email', message: '请输入正确的邮箱格式', trigger: 'blur' }
  ],
  smtp_from_email: [
    { type: 'email', message: '请输入正确的邮箱格式', trigger: 'blur' }
  ]
}

const loadSettings = async () => {
  loading.value = true
  try {
    const res = await getSystemSettings()
    Object.assign(form, res.data)
  } catch (error) {
    ElMessage.error('加载失败')
  } finally {
    loading.value = false
  }
}

const handleSubmit = async () => {
  if (!formRef.value) return

  await formRef.value.validate(async (valid) => {
    if (valid) {
      loading.value = true
      try {
        await updateSystemSettings(form)
        ElMessage.success('保存成功')
        loadSettings()
      } catch (error) {
        ElMessage.error('保存失败')
      } finally {
        loading.value = false
      }
    }
  })
}

onMounted(() => {
  loadSettings()
})
</script>

<style scoped>
.system-container {
  padding: 20px;
}

.el-form {
  max-width: 600px;
}
</style>
