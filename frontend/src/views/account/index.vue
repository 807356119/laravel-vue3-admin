<template>
  <div class="account-container">
    <el-card class="page-card" shadow="never">
      <template #header>
        <div class="card-header">
          <div class="header-left">
            <el-icon class="page-icon"><Wallet /></el-icon>
            <span class="page-title">账户管理</span>
          </div>
          <el-button type="primary" @click="handleAdd">
            <el-icon><Plus /></el-icon>
            新增账户
          </el-button>
        </div>
      </template>

      <!-- 筛选栏 -->
      <div class="filter-bar">
        <el-form :inline="true" :model="filterForm" class="filter-form">
          <el-form-item>
            <el-input
              v-model="filterForm.keyword"
              placeholder="搜索标题/账号"
              clearable
              prefix-icon="Search"
              style="width: 200px"
            />
          </el-form-item>
          <el-form-item>
            <el-select v-model="filterForm.type" placeholder="类型" clearable style="width: 120px">
              <el-option label="银行卡" value="bank" />
              <el-option label="邮箱" value="email" />
              <el-option label="社交账号" value="social" />
              <el-option label="网站" value="website" />
              <el-option label="其他" value="other" />
            </el-select>
          </el-form-item>
          <el-form-item>
            <el-checkbox v-model="filterForm.is_favorite" label="仅显示收藏" />
          </el-form-item>
          <el-form-item>
            <el-button type="primary" @click="loadAccounts">
              <el-icon><Search /></el-icon>
              查询
            </el-button>
            <el-button @click="resetFilter">
              <el-icon><Refresh /></el-icon>
              重置
            </el-button>
          </el-form-item>
        </el-form>
      </div>

      <!-- 卡片列表 -->
      <div v-loading="loading" class="account-grid">
        <div
          v-for="account in accounts"
          :key="account.id"
          class="account-card"
          @click="handleViewDetail(account)"
        >
          <div class="card-header">
            <div class="card-title">
              <el-icon class="type-icon" :style="{ color: getTypeColor(account.type) }">
                <component :is="getTypeIcon(account.type)" />
              </el-icon>
              <span>{{ account.title }}</span>
            </div>
            <el-icon
              class="favorite-icon"
              :class="{ active: account.is_favorite }"
              @click.stop="handleToggleFavorite(account)"
            >
              <StarFilled v-if="account.is_favorite" />
              <Star v-else />
            </el-icon>
          </div>

          <div class="card-body">
            <div class="info-item" v-if="account.account_name">
              <span class="label">账户名：</span>
              <span class="value">{{ account.account_name }}</span>
            </div>
            <div class="info-item" v-if="account.account_number">
              <span class="label">账号：</span>
              <span class="value">{{ maskAccount(account.account_number) }}</span>
            </div>
            <div class="info-item" v-if="account.bank_name">
              <span class="label">银行：</span>
              <span class="value">{{ account.bank_name }}</span>
            </div>
            <div class="info-item" v-if="account.website">
              <span class="label">网站：</span>
              <span class="value text-link">{{ account.website }}</span>
            </div>
          </div>

          <div class="card-footer">
            <el-tag size="small" :type="getTypeTagType(account.type)">
              {{ account.type_text }}
            </el-tag>
            <div class="actions">
              <el-button size="small" text @click.stop="handleEdit(account)">
                <el-icon><Edit /></el-icon>
              </el-button>
              <el-button size="small" text type="danger" @click.stop="handleDelete(account)">
                <el-icon><Delete /></el-icon>
              </el-button>
            </div>
          </div>
        </div>
      </div>

      <!-- 分页 -->
      <el-pagination
        v-model:current-page="pagination.page"
        v-model:page-size="pagination.size"
        :total="pagination.total"
        :page-sizes="[12, 24, 48]"
        layout="total, sizes, prev, pager, next"
        class="pagination"
        @size-change="loadAccounts"
        @current-change="loadAccounts"
      />
    </el-card>

    <!-- 新增/编辑对话框 -->
    <el-dialog
      v-model="dialogVisible"
      :title="dialogType === 'add' ? '新增账户' : '编辑账户'"
      width="600px"
      :close-on-click-modal="false"
    >
      <el-form :model="form" :rules="rules" ref="formRef" label-width="90px">
        <el-form-item label="标题" prop="title">
          <el-input v-model="form.title" placeholder="请输入标题" />
        </el-form-item>

        <el-form-item label="类型" prop="type">
          <el-select v-model="form.type" placeholder="请选择类型" style="width: 100%">
            <el-option label="银行卡" value="bank" />
            <el-option label="邮箱" value="email" />
            <el-option label="社交账号" value="social" />
            <el-option label="网站" value="website" />
            <el-option label="其他" value="other" />
          </el-select>
        </el-form-item>

        <el-form-item label="账户名" prop="account_name">
          <el-input v-model="form.account_name" placeholder="持卡人/用户名" />
        </el-form-item>

        <el-form-item label="账号/卡号" prop="account_number">
          <el-input v-model="form.account_number" placeholder="账号或卡号" />
        </el-form-item>

        <el-form-item label="密码" prop="password">
          <el-input
            v-model="form.password"
            type="password"
            placeholder="密码"
            show-password
          />
        </el-form-item>

        <el-form-item label="银行名称" prop="bank_name" v-if="form.type === 'bank'">
          <el-input v-model="form.bank_name" placeholder="银行名称" />
        </el-form-item>

        <el-form-item label="网站地址" prop="website" v-if="form.type === 'website'">
          <el-input v-model="form.website" placeholder="https://" />
        </el-form-item>

        <el-form-item label="备注" prop="description">
          <el-input
            v-model="form.description"
            type="textarea"
            :rows="3"
            placeholder="备注说明"
          />
        </el-form-item>

        <el-form-item label="图片附件">
          <el-upload
            :action="uploadAction"
            :headers="uploadHeaders"
            :on-success="handleUploadSuccess"
            :on-remove="handleRemoveImage"
            :before-upload="beforeUpload"
            list-type="picture-card"
            v-model:file-list="fileList"
          >
            <el-icon><Plus /></el-icon>
          </el-upload>
        </el-form-item>

        <el-form-item label="收藏">
          <el-switch v-model="form.is_favorite" />
        </el-form-item>
      </el-form>

      <template #footer>
        <el-button @click="dialogVisible = false">取消</el-button>
        <el-button type="primary" @click="handleSubmit" :loading="submitting">
          确定
        </el-button>
      </template>
    </el-dialog>

    <!-- 详情对话框 -->
    <el-dialog
      v-model="detailVisible"
      title="账户详情"
      width="500px"
    >
      <el-descriptions :column="1" border v-if="currentAccount">
        <el-descriptions-item label="标题">{{ currentAccount.title }}</el-descriptions-item>
        <el-descriptions-item label="类型">
          <el-tag :type="getTypeTagType(currentAccount.type)">
            {{ currentAccount.type_text }}
          </el-tag>
        </el-descriptions-item>
        <el-descriptions-item label="账户名" v-if="currentAccount.account_name">
          {{ currentAccount.account_name }}
        </el-descriptions-item>
        <el-descriptions-item label="账号" v-if="currentAccount.account_number">
          {{ currentAccount.account_number }}
        </el-descriptions-item>
        <el-descriptions-item label="密码">
          <div class="password-field">
            <span v-if="!showPassword">••••••••</span>
            <span v-else>{{ decryptedPassword }}</span>
            <el-button
              size="small"
              text
              @click="handleShowPassword(currentAccount.id)"
              :loading="passwordLoading"
            >
              <el-icon><View /></el-icon>
              {{ showPassword ? '隐藏' : '查看' }}
            </el-button>
          </div>
        </el-descriptions-item>
        <el-descriptions-item label="银行" v-if="currentAccount.bank_name">
          {{ currentAccount.bank_name }}
        </el-descriptions-item>
        <el-descriptions-item label="网站" v-if="currentAccount.website">
          <el-link :href="currentAccount.website" target="_blank">
            {{ currentAccount.website }}
          </el-link>
        </el-descriptions-item>
        <el-descriptions-item label="备注" v-if="currentAccount.description">
          {{ currentAccount.description }}
        </el-descriptions-item>
        <el-descriptions-item label="创建时间">
          {{ currentAccount.created_at }}
        </el-descriptions-item>
      </el-descriptions>

      <!-- 图片预览 -->
      <div v-if="currentAccount?.images?.length" class="image-gallery">
        <el-image
          v-for="(img, index) in currentAccount.images"
          :key="index"
          :src="getImageUrl(img)"
          :preview-src-list="currentAccount.images.map(i => getImageUrl(i))"
          fit="cover"
          class="preview-image"
        />
      </div>
    </el-dialog>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import {
  getAccountList,
  createAccount,
  updateAccount,
  deleteAccount,
  toggleFavorite,
  getPassword
} from '@/api/account'
import { ElMessage, ElMessageBox } from 'element-plus'

const loading = ref(false)
const submitting = ref(false)
const dialogVisible = ref(false)
const detailVisible = ref(false)
const dialogType = ref('add')
const formRef = ref(null)
const showPassword = ref(false)
const passwordLoading = ref(false)
const decryptedPassword = ref('')
const currentAccount = ref(null)

const accounts = ref([])
const fileList = ref([])

const filterForm = reactive({
  keyword: '',
  type: '',
  is_favorite: false
})

const pagination = reactive({
  page: 1,
  size: 12,
  total: 0
})

const form = reactive({
  id: null,
  title: '',
  type: 'other',
  account_name: '',
  account_number: '',
  password: '',
  bank_name: '',
  website: '',
  description: '',
  images: [],
  is_favorite: false
})

const rules = {
  title: [{ required: true, message: '请输入标题', trigger: 'blur' }],
  type: [{ required: true, message: '请选择类型', trigger: 'change' }]
}

const uploadAction = 'http://localhost:8000/api/accounts/upload-image'
const uploadHeaders = {
  Authorization: `Bearer ${localStorage.getItem('token')}`
}

const loadAccounts = async () => {
  loading.value = true
  try {
    const res = await getAccountList({
      page: pagination.page,
      size: pagination.size,
      ...filterForm
    })
    accounts.value = res.data.list
    pagination.total = res.data.pagination.total
  } catch (error) {
    ElMessage.error('加载失败')
  } finally {
    loading.value = false
  }
}

const resetFilter = () => {
  filterForm.keyword = ''
  filterForm.type = ''
  filterForm.is_favorite = false
  pagination.page = 1
  loadAccounts()
}

const handleAdd = () => {
  dialogType.value = 'add'
  dialogVisible.value = true
  resetForm()
}

const handleEdit = (row) => {
  dialogType.value = 'edit'
  dialogVisible.value = true

  // 复制数据
  form.id = row.id
  form.title = row.title
  form.type = row.type
  form.account_name = row.account_name
  form.account_number = row.account_number
  form.bank_name = row.bank_name
  form.website = row.website
  form.description = row.description
  form.is_favorite = row.is_favorite
  form.password = ''

  // 复制图片数据
  form.images = row.images ? [...row.images] : []

  // 构建fileList用于显示
  fileList.value = (row.images || []).map((img, index) => ({
    uid: index,
    name: `image-${index}`,
    url: getImageUrl(img),
    response: { data: { path: img } }
  }))
}

const handleSubmit = async () => {
  if (!formRef.value) return

  await formRef.value.validate(async (valid) => {
    if (valid) {
      submitting.value = true
      try {
        const submitData = { ...form }
        if (dialogType.value === 'add') {
          await createAccount(submitData)
          ElMessage.success('创建成功')
        } else {
          if (!submitData.password) {
            delete submitData.password
          }
          await updateAccount(form.id, submitData)
          ElMessage.success('更新成功')
        }
        dialogVisible.value = false
        loadAccounts()
      } catch (error) {
        ElMessage.error('操作失败')
      } finally {
        submitting.value = false
      }
    }
  })
}

const handleDelete = (row) => {
  ElMessageBox.confirm('确定要删除该账户吗？', '删除确认', {
    confirmButtonText: '确定',
    cancelButtonText: '取消',
    type: 'warning'
  }).then(async () => {
    try {
      await deleteAccount(row.id)
      ElMessage.success('删除成功')
      loadAccounts()
    } catch (error) {
      ElMessage.error('删除失败')
    }
  })
}

const handleToggleFavorite = async (row) => {
  try {
    await toggleFavorite(row.id)
    row.is_favorite = !row.is_favorite
    ElMessage.success('操作成功')
  } catch (error) {
    ElMessage.error('操作失败')
  }
}

const handleViewDetail = (row) => {
  currentAccount.value = row
  showPassword.value = false
  decryptedPassword.value = ''
  detailVisible.value = true
}

const handleShowPassword = async (id) => {
  if (showPassword.value) {
    showPassword.value = false
    return
  }

  passwordLoading.value = true
  try {
    const res = await getPassword(id)
    decryptedPassword.value = res.data.password
    showPassword.value = true
  } catch (error) {
    ElMessage.error('获取密码失败')
  } finally {
    passwordLoading.value = false
  }
}

const handleUploadSuccess = (response, file, fileListData) => {
  if (response.code === 200) {
    // 添加到form.images
    form.images.push(response.data.path)
    ElMessage.success('上传成功')
  } else {
    ElMessage.error('上传失败')
    // 移除上传失败的文件
    const index = fileListData.findIndex(f => f.uid === file.uid)
    if (index > -1) {
      fileListData.splice(index, 1)
    }
  }
}

const handleRemoveImage = (file, fileListData) => {
  // 从form.images中移除
  if (file.response?.data?.path) {
    const index = form.images.indexOf(file.response.data.path)
    if (index > -1) {
      form.images.splice(index, 1)
    }
  }
}

const beforeUpload = (file) => {
  const isImage = file.type.startsWith('image/')
  const isLt5M = file.size / 1024 / 1024 < 5

  if (!isImage) {
    ElMessage.error('只能上传图片文件')
    return false
  }
  if (!isLt5M) {
    ElMessage.error('图片大小不能超过 5MB')
    return false
  }
  return true
}

const resetForm = () => {
  form.id = null
  form.title = ''
  form.type = 'other'
  form.account_name = ''
  form.account_number = ''
  form.password = ''
  form.bank_name = ''
  form.website = ''
  form.description = ''
  form.images = []
  form.is_favorite = false
  fileList.value = []
  if (formRef.value) {
    formRef.value.resetFields()
  }
}

const maskAccount = (account) => {
  if (!account) return ''
  if (account.length <= 8) return account
  return account.substring(0, 4) + '****' + account.substring(account.length - 4)
}

const getTypeIcon = (type) => {
  const icons = {
    bank: 'CreditCard',
    email: 'Message',
    social: 'ChatDotRound',
    website: 'Link',
    other: 'Document'
  }
  return icons[type] || 'Document'
}

const getTypeColor = (type) => {
  const colors = {
    bank: '#67C23A',
    email: '#409EFF',
    social: '#E6A23C',
    website: '#F56C6C',
    other: '#909399'
  }
  return colors[type] || '#909399'
}

const getTypeTagType = (type) => {
  const types = {
    bank: 'success',
    email: 'primary',
    social: 'warning',
    website: 'danger',
    other: 'info'
  }
  return types[type] || 'info'
}

const getImageUrl = (path) => {
  return `http://localhost:8000/storage/${path}`
}

onMounted(() => {
  loadAccounts()
})
</script>

<style scoped>
.account-container {
  animation: fadeIn 0.5s;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.page-card {
  border: none;
}

.card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.header-left {
  display: flex;
  align-items: center;
  gap: 12px;
}

.page-icon {
  font-size: 24px;
  color: #667eea;
}

.page-title {
  font-size: 18px;
  font-weight: 600;
  color: #303133;
}

/* 筛选栏 */
.filter-bar {
  margin-bottom: 20px;
  padding: 16px;
  background: rgba(102, 126, 234, 0.05);
  border-radius: 8px;
}

.filter-form :deep(.el-form-item) {
  margin-bottom: 0;
}

/* 卡片网格 */
.account-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
  gap: 20px;
  margin-bottom: 20px;
  min-height: 400px;
}

.account-card {
  background: #fff;
  border: 2px solid rgba(102, 126, 234, 0.1);
  border-radius: 12px;
  padding: 16px;
  cursor: pointer;
  transition: all 0.3s;
}

.account-card:hover {
  border-color: rgba(102, 126, 234, 0.4);
  transform: translateY(-4px);
  box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
}

.account-card .card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 12px;
}

.card-title {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 16px;
  font-weight: 600;
  color: #303133;
}

.type-icon {
  font-size: 20px;
}

.favorite-icon {
  font-size: 20px;
  color: #dcdfe6;
  cursor: pointer;
  transition: all 0.3s;
}

.favorite-icon.active {
  color: #f56c6c;
}

.favorite-icon:hover {
  transform: scale(1.2);
}

.card-body {
  margin-bottom: 12px;
}

.info-item {
  margin-bottom: 8px;
  font-size: 14px;
  color: #606266;
}

.info-item .label {
  color: #909399;
  margin-right: 4px;
}

.info-item .value {
  color: #303133;
}

.text-link {
  color: #409eff;
  text-decoration: underline;
}

.card-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding-top: 12px;
  border-top: 1px solid #ebeef5;
}

.actions {
  display: flex;
  gap: 8px;
}

/* 分页 */
.pagination {
  display: flex;
  justify-content: center;
  padding-top: 16px;
}

/* 密码字段 */
.password-field {
  display: flex;
  align-items: center;
  gap: 8px;
}

/* 图片画廊 */
.image-gallery {
  margin-top: 16px;
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
}

.preview-image {
  width: 100px;
  height: 100px;
  border-radius: 8px;
  cursor: pointer;
}
</style>
