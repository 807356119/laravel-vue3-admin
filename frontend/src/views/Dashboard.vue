<template>
  <div class="dashboard">
    <!-- 统计卡片 -->
    <el-row :gutter="20" class="stats-row">
      <el-col :xs="24" :sm="12" :lg="6" v-for="(stat, index) in stats" :key="index">
        <div class="stat-card" :class="`stat-card-${index + 1}`">
          <div class="stat-content">
            <div class="stat-icon">
              <el-icon>
                <component :is="stat.icon" />
              </el-icon>
            </div>
            <div class="stat-info">
              <div class="stat-label">{{ stat.label }}</div>
              <div class="stat-value">{{ stat.value }}</div>
              <div class="stat-trend" :class="stat.trendClass">
                <el-icon><TrendCharts /></el-icon>
                <span>{{ stat.trend }}</span>
              </div>
            </div>
          </div>
        </div>
      </el-col>
    </el-row>

    <!-- 数据图表和快捷操作 -->
    <el-row :gutter="20" class="content-row">
      <el-col :xs="24" :lg="16">
        <!-- 实时数据 -->
        <el-card class="content-card" shadow="hover">
          <template #header>
            <div class="card-header">
              <span class="card-title">
                <el-icon><DataLine /></el-icon>
                实时数据监控
              </span>
              <el-tag size="small" type="success">实时更新</el-tag>
            </div>
          </template>
          <div class="chart-container">
            <div class="chart-placeholder">
              <el-icon class="chart-icon"><TrendCharts /></el-icon>
              <p>数据图表区域</p>
              <el-button type="primary" text>查看详细数据</el-button>
            </div>
          </div>
        </el-card>

        <!-- 快捷操作 -->
        <el-card class="content-card" shadow="hover" style="margin-top: 20px;">
          <template #header>
            <div class="card-header">
              <span class="card-title">
                <el-icon><Operation /></el-icon>
                快捷操作
              </span>
            </div>
          </template>
          <el-row :gutter="16" class="quick-actions">
            <el-col :xs="12" :sm="8" :md="6" v-for="action in quickActions" :key="action.name">
              <div class="action-item" @click="handleAction(action.path)">
                <div class="action-icon" :style="{ background: action.gradient }">
                  <el-icon>
                    <component :is="action.icon" />
                  </el-icon>
                </div>
                <span class="action-name">{{ action.name }}</span>
              </div>
            </el-col>
          </el-row>
        </el-card>
      </el-col>

      <el-col :xs="24" :lg="8">
        <!-- 系统信息 -->
        <el-card class="content-card" shadow="hover">
          <template #header>
            <div class="card-header">
              <span class="card-title">
                <el-icon><Monitor /></el-icon>
                系统信息
              </span>
            </div>
          </template>
          <el-descriptions :column="1" border>
            <el-descriptions-item label="系统版本">v1.0.0</el-descriptions-item>
            <el-descriptions-item label="Laravel">9.52.21</el-descriptions-item>
            <el-descriptions-item label="Vue.js">3.5.34</el-descriptions-item>
            <el-descriptions-item label="运行环境">
              <el-tag size="small" type="success">Production</el-tag>
            </el-descriptions-item>
            <el-descriptions-item label="服务器时间">{{ currentTime }}</el-descriptions-item>
            <el-descriptions-item label="数据库">MySQL 5.7</el-descriptions-item>
          </el-descriptions>
        </el-card>

        <!-- 系统通知 -->
        <el-card class="content-card" shadow="hover" style="margin-top: 20px;">
          <template #header>
            <div class="card-header">
              <span class="card-title">
                <el-icon><Bell /></el-icon>
                系统通知
              </span>
              <el-badge :value="notifications.length" :max="9" />
            </div>
          </template>
          <div class="notification-list">
            <div
              v-for="(notification, index) in notifications"
              :key="index"
              class="notification-item"
            >
              <div class="notification-icon" :class="`notification-${notification.type}`">
                <el-icon>
                  <component :is="notification.icon" />
                </el-icon>
              </div>
              <div class="notification-content">
                <div class="notification-title">{{ notification.title }}</div>
                <div class="notification-desc">{{ notification.desc }}</div>
                <div class="notification-time">{{ notification.time }}</div>
              </div>
            </div>
          </div>
        </el-card>
      </el-col>
    </el-row>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import { useUserStore } from '@/stores/user'
import { ElMessage } from 'element-plus'

const router = useRouter()
const userStore = useUserStore()
const currentTime = ref('')

const stats = ref([
  {
    label: '用户总数',
    value: 1580,
    trend: '+12.5%',
    trendClass: 'trend-up',
    icon: 'UserFilled'
  },
  {
    label: '角色数量',
    value: 24,
    trend: '+3.2%',
    trendClass: 'trend-up',
    icon: 'Lock'
  },
  {
    label: '在线用户',
    value: 328,
    trend: '+8.7%',
    trendClass: 'trend-up',
    icon: 'Connection'
  },
  {
    label: '今日访问',
    value: 4567,
    trend: '+23.4%',
    trendClass: 'trend-up',
    icon: 'View'
  }
])

const quickActions = ref([
  {
    name: '新增用户',
    icon: 'UserFilled',
    path: '/users',
    gradient: 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)'
  },
  {
    name: '角色管理',
    icon: 'Lock',
    path: '/roles',
    gradient: 'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)'
  },
  {
    name: '系统配置',
    icon: 'Setting',
    path: '/system',
    gradient: 'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)'
  },
  {
    name: '数据统计',
    icon: 'DataAnalysis',
    path: '/dashboard',
    gradient: 'linear-gradient(135deg, #43e97b 0%, #38f9d7 100%)'
  }
])

const notifications = ref([
  {
    title: '系统更新',
    desc: '系统已更新至最新版本 v1.0.0',
    time: '5分钟前',
    type: 'info',
    icon: 'InfoFilled'
  },
  {
    title: '安全提醒',
    desc: '检测到异常登录行为，请及时处理',
    time: '1小时前',
    type: 'warning',
    icon: 'WarningFilled'
  },
  {
    title: '数据备份',
    desc: '数据库备份已完成',
    time: '2小时前',
    type: 'success',
    icon: 'SuccessFilled'
  }
])

const updateTime = () => {
  const now = new Date()
  currentTime.value = now.toLocaleString('zh-CN')
}

let timer = null

onMounted(() => {
  updateTime()
  timer = setInterval(updateTime, 1000)
})

onUnmounted(() => {
  if (timer) clearInterval(timer)
})

const handleAction = (path) => {
  if (path === '/dashboard') {
    ElMessage.info('数据统计功能开发中')
  } else {
    router.push(path)
  }
}
</script>

<style scoped>
.dashboard {
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

/* 统计卡片 */
.stats-row {
  margin-bottom: 20px;
}

.stat-card {
  background: #fff;
  border-radius: 12px;
  padding: 24px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
  cursor: pointer;
  transition: all 0.3s;
  border: 2px solid transparent;
}

.stat-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
}

.stat-card-1 {
  border-color: rgba(102, 126, 234, 0.2);
}

.stat-card-1:hover {
  border-color: rgba(102, 126, 234, 0.5);
}

.stat-card-2 {
  border-color: rgba(240, 147, 251, 0.2);
}

.stat-card-2:hover {
  border-color: rgba(240, 147, 251, 0.5);
}

.stat-card-3 {
  border-color: rgba(79, 172, 254, 0.2);
}

.stat-card-3:hover {
  border-color: rgba(79, 172, 254, 0.5);
}

.stat-card-4 {
  border-color: rgba(67, 233, 123, 0.2);
}

.stat-card-4:hover {
  border-color: rgba(67, 233, 123, 0.5);
}

.stat-content {
  display: flex;
  gap: 20px;
  align-items: center;
}

.stat-icon {
  width: 60px;
  height: 60px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 28px;
  color: #fff;
}

.stat-card-1 .stat-icon {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  box-shadow: 0 8px 16px rgba(102, 126, 234, 0.3);
}

.stat-card-2 .stat-icon {
  background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
  box-shadow: 0 8px 16px rgba(240, 147, 251, 0.3);
}

.stat-card-3 .stat-icon {
  background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
  box-shadow: 0 8px 16px rgba(79, 172, 254, 0.3);
}

.stat-card-4 .stat-icon {
  background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
  box-shadow: 0 8px 16px rgba(67, 233, 123, 0.3);
}

.stat-info {
  flex: 1;
}

.stat-label {
  font-size: 14px;
  color: #999;
  margin-bottom: 8px;
}

.stat-value {
  font-size: 32px;
  font-weight: 700;
  color: #333;
  line-height: 1;
  margin-bottom: 8px;
}

.stat-trend {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  font-size: 13px;
  padding: 4px 8px;
  border-radius: 4px;
}

.trend-up {
  background: rgba(67, 233, 123, 0.1);
  color: #43e97b;
}

/* 内容卡片 */
.content-card {
  border-radius: 12px;
}

.card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.card-title {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 16px;
  font-weight: 600;
  color: #333;
}

/* 图表区域 */
.chart-container {
  height: 300px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(102, 126, 234, 0.03);
  border-radius: 8px;
}

.chart-placeholder {
  text-align: center;
  color: #999;
}

.chart-icon {
  font-size: 64px;
  margin-bottom: 16px;
  color: #667eea;
  opacity: 0.3;
}

/* 快捷操作 */
.quick-actions {
  margin-top: -8px;
}

.action-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 12px;
  padding: 24px;
  border-radius: 12px;
  background: rgba(102, 126, 234, 0.03);
  border: 2px solid rgba(102, 126, 234, 0.1);
  cursor: pointer;
  transition: all 0.3s;
}

.action-item:hover {
  background: rgba(102, 126, 234, 0.08);
  border-color: rgba(102, 126, 234, 0.3);
  transform: translateY(-3px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
}

.action-icon {
  width: 56px;
  height: 56px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 28px;
  color: #fff;
  box-shadow: 0 8px 16px rgba(102, 126, 234, 0.2);
}

.action-name {
  font-size: 14px;
  color: #666;
  font-weight: 500;
}

/* 通知列表 */
.notification-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.notification-item {
  display: flex;
  gap: 12px;
  padding: 12px;
  background: rgba(102, 126, 234, 0.03);
  border-radius: 8px;
  border: 1px solid rgba(102, 126, 234, 0.1);
  transition: all 0.3s;
  cursor: pointer;
}

.notification-item:hover {
  background: rgba(102, 126, 234, 0.08);
  border-color: rgba(102, 126, 234, 0.3);
  transform: translateX(4px);
}

.notification-icon {
  width: 40px;
  height: 40px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 20px;
  flex-shrink: 0;
}

.notification-info {
  background: rgba(102, 126, 234, 0.1);
  color: #667eea;
}

.notification-warning {
  background: rgba(251, 191, 36, 0.1);
  color: #fbbf24;
}

.notification-success {
  background: rgba(67, 233, 123, 0.1);
  color: #43e97b;
}

.notification-content {
  flex: 1;
}

.notification-title {
  font-size: 14px;
  font-weight: 500;
  color: #333;
  margin-bottom: 4px;
}

.notification-desc {
  font-size: 13px;
  color: #666;
  margin-bottom: 4px;
}

.notification-time {
  font-size: 12px;
  color: #999;
}
</style>
