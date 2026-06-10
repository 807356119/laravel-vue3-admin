<template>
  <el-container class="layout-container">
    <!-- 侧边栏 -->
    <el-aside :width="isCollapse ? '64px' : '220px'" class="sidebar">
      <div class="logo-container" :class="{ collapse: isCollapse }">
        <transition name="fade">
          <div v-if="!isCollapse" class="logo-content">
            <div class="logo-icon-wrapper">
              <el-icon class="logo-icon"><DataAnalysis /></el-icon>
            </div>
            <div class="logo-text-wrapper">
              <span class="logo-text">Admin</span>
              <span class="logo-sub">Control System</span>
            </div>
          </div>
          <el-icon v-else class="logo-icon-collapse"><DataAnalysis /></el-icon>
        </transition>
      </div>

      <el-scrollbar class="menu-scrollbar">
        <el-menu
          :default-active="$route.path"
          :collapse="isCollapse"
          :collapse-transition="false"
          router
          class="sidebar-menu"
        >
          <el-menu-item index="/dashboard" class="menu-item">
            <el-icon><Odometer /></el-icon>
            <template #title>
              <span>数据看板</span>
            </template>
          </el-menu-item>

          <el-menu-item index="/users" v-if="hasPermission('user.view')" class="menu-item">
            <el-icon><Avatar /></el-icon>
            <template #title>
              <span>用户管理</span>
            </template>
          </el-menu-item>

          <el-menu-item index="/roles" v-if="hasPermission('role.view')" class="menu-item">
            <el-icon><Lock /></el-icon>
            <template #title>
              <span>角色权限</span>
            </template>
          </el-menu-item>

          <el-menu-item index="/accounts" class="menu-item">
            <el-icon><Wallet /></el-icon>
            <template #title>
              <span>账户管理</span>
            </template>
          </el-menu-item>

          <el-menu-item index="/system" v-if="hasPermission('system.manage')" class="menu-item">
            <el-icon><Setting /></el-icon>
            <template #title>
              <span>系统配置</span>
            </template>
          </el-menu-item>
        </el-menu>
      </el-scrollbar>

      <!-- 侧边栏底部 -->
      <div class="sidebar-footer" v-if="!isCollapse">
        <div class="system-status">
          <div class="status-item">
            <span class="status-dot"></span>
            <span class="status-text">系统运行正常</span>
          </div>
        </div>
      </div>
    </el-aside>

    <el-container class="main-container">
      <!-- 顶部导航 -->
      <el-header class="header">
        <div class="header-left">
          <div class="collapse-btn" @click="toggleCollapse">
            <el-icon>
              <Expand v-if="isCollapse" />
              <Fold v-else />
            </el-icon>
          </div>

          <div class="breadcrumb-wrapper">
            <el-icon class="breadcrumb-icon"><Location /></el-icon>
            <el-breadcrumb separator="/">
              <el-breadcrumb-item>控制中心</el-breadcrumb-item>
              <el-breadcrumb-item v-if="$route.meta.title">{{ $route.meta.title }}</el-breadcrumb-item>
            </el-breadcrumb>
          </div>
        </div>

        <div class="header-right">
          <!-- 搜索 -->
          <div class="header-search">
            <el-icon><Search /></el-icon>
          </div>

          <!-- 通知 -->
          <div class="header-notification">
            <el-badge :value="3" :max="99">
              <el-icon><Bell /></el-icon>
            </el-badge>
          </div>

          <!-- 全屏 -->
          <div class="header-fullscreen" @click="toggleFullscreen">
            <el-icon><FullScreen /></el-icon>
          </div>

          <!-- 用户信息 -->
          <el-dropdown trigger="click" @command="handleCommand" class="user-dropdown">
            <div class="user-info">
              <el-avatar :size="36" class="user-avatar">
                <el-icon><UserFilled /></el-icon>
              </el-avatar>
              <div class="user-details">
                <span class="user-name">{{ userStore.userInfo?.name }}</span>
                <span class="user-role">管理员</span>
              </div>
              <el-icon class="arrow-icon"><ArrowDown /></el-icon>
            </div>
            <template #dropdown>
              <el-dropdown-menu class="user-menu">
                <el-dropdown-item command="profile">
                  <el-icon><User /></el-icon>
                  个人中心
                </el-dropdown-item>
                <el-dropdown-item command="settings">
                  <el-icon><Setting /></el-icon>
                  账号设置
                </el-dropdown-item>
                <el-dropdown-item divided command="logout">
                  <el-icon><SwitchButton /></el-icon>
                  退出登录
                </el-dropdown-item>
              </el-dropdown-menu>
            </template>
          </el-dropdown>
        </div>
      </el-header>

      <!-- 主内容区 -->
      <el-main class="main-content">
        <transition name="fade-transform" mode="out-in">
          <router-view />
        </transition>
      </el-main>
    </el-container>

    <!-- 背景装饰 -->
    <div class="bg-decoration">
      <div class="grid-lines"></div>
      <div class="glow-orb glow-orb-1"></div>
      <div class="glow-orb glow-orb-2"></div>
    </div>
  </el-container>
</template>

<script setup>
import { ref } from 'vue'
import { useUserStore } from '@/stores/user'
import { ElMessageBox, ElMessage } from 'element-plus'

const userStore = useUserStore()
const isCollapse = ref(false)

const hasPermission = (permission) => {
  return userStore.hasPermission(permission)
}

const toggleCollapse = () => {
  isCollapse.value = !isCollapse.value
}

const toggleFullscreen = () => {
  if (!document.fullscreenElement) {
    document.documentElement.requestFullscreen()
  } else {
    document.exitFullscreen()
  }
}

const handleCommand = (command) => {
  switch (command) {
    case 'profile':
      ElMessage.info('个人中心功能开发中')
      break
    case 'settings':
      ElMessage.info('账号设置功能开发中')
      break
    case 'logout':
      handleLogout()
      break
  }
}

const handleLogout = () => {
  ElMessageBox.confirm('确定要退出登录吗?', '退出确认', {
    confirmButtonText: '确定',
    cancelButtonText: '取消',
    type: 'warning'
  }).then(() => {
    userStore.logout()
  })
}
</script>

<style scoped>
/* 主容器 - 天空渐变背景 */
.layout-container {
  height: 100vh;
  width: 100vw;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
  position: relative;
  overflow: hidden;
}

/* 背景装饰 */
.bg-decoration {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  pointer-events: none;
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
  filter: blur(80px);
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
  width: 450px;
  height: 450px;
  background: radial-gradient(circle, rgba(255, 255, 255, 0.1), transparent);
  bottom: -150px;
  left: -150px;
  animation-delay: -7s;
}

@keyframes float {
  0%, 100% { transform: translate(0, 0) scale(1); }
  50% { transform: translate(30px, -30px) scale(1.1); }
}

/* 侧边栏 */
.sidebar {
  background: rgba(255, 255, 255, 0.15);
  backdrop-filter: blur(30px);
  border-right: 1px solid rgba(255, 255, 255, 0.2);
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  box-shadow: 4px 0 24px rgba(0, 0, 0, 0.1);
  position: relative;
  z-index: 10;
  display: flex;
  flex-direction: column;
}

.logo-container {
  height: 70px;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 0 20px;
  border-bottom: 1px solid rgba(255, 255, 255, 0.2);
  background: rgba(255, 255, 255, 0.1);
}

.logo-content {
  display: flex;
  align-items: center;
  gap: 12px;
}

.logo-icon-wrapper {
  width: 42px;
  height: 42px;
  background: linear-gradient(135deg, #fff, rgba(255, 255, 255, 0.8));
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 4px 15px rgba(255, 255, 255, 0.3);
}

.logo-icon, .logo-icon-collapse {
  font-size: 24px;
  color: #667eea;
}

.logo-text-wrapper {
  display: flex;
  flex-direction: column;
}

.logo-text {
  font-size: 20px;
  font-weight: 700;
  color: #fff;
  line-height: 1;
  letter-spacing: 1px;
}

.logo-sub {
  font-size: 11px;
  color: rgba(255, 255, 255, 0.7);
  letter-spacing: 2px;
  text-transform: uppercase;
  margin-top: 2px;
}

.menu-scrollbar {
  flex: 1;
  padding: 10px;
}

.sidebar-menu {
  border: none;
  background: transparent;
}

.sidebar-menu :deep(.el-menu-item) {
  margin: 4px 0;
  border-radius: 8px;
  color: rgba(255, 255, 255, 0.9);
  transition: all 0.3s;
  position: relative;
  overflow: hidden;
}

.sidebar-menu :deep(.el-menu-item::before) {
  content: '';
  position: absolute;
  left: 0;
  top: 0;
  width: 3px;
  height: 100%;
  background: linear-gradient(to bottom, #fff, rgba(255, 255, 255, 0.8));
  opacity: 0;
  transition: opacity 0.3s;
}

.sidebar-menu :deep(.el-menu-item:hover) {
  background: rgba(255, 255, 255, 0.2);
  color: #fff;
}

.sidebar-menu :deep(.el-menu-item.is-active) {
  background: rgba(255, 255, 255, 0.25);
  color: #fff;
  box-shadow: 0 4px 12px rgba(255, 255, 255, 0.2);
}

.sidebar-menu :deep(.el-menu-item.is-active::before) {
  opacity: 1;
}

.sidebar-footer {
  padding: 16px;
  border-top: 1px solid rgba(255, 255, 255, 0.2);
  background: rgba(255, 255, 255, 0.1);
}

.system-status {
  display: flex;
  align-items: center;
}

.status-item {
  display: flex;
  align-items: center;
  gap: 8px;
}

.status-dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background: #10b981;
  box-shadow: 0 0 10px #10b981;
  animation: pulse 2s infinite;
}

@keyframes pulse {
  0%, 100% { opacity: 1; }
  50% { opacity: 0.5; }
}

.status-text {
  font-size: 12px;
  color: rgba(255, 255, 255, 0.8);
}

/* 主容器 */
.main-container {
  position: relative;
  z-index: 1;
  background: transparent;
}

/* 顶部导航 */
.header {
  background: rgba(255, 255, 255, 0.15);
  backdrop-filter: blur(30px);
  border-bottom: 1px solid rgba(255, 255, 255, 0.2);
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0 24px;
  box-shadow: 0 4px 24px rgba(0, 0, 0, 0.05);
}

.header-left {
  display: flex;
  align-items: center;
  gap: 24px;
}

.collapse-btn {
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 8px;
  background: rgba(255, 255, 255, 0.2);
  color: #fff;
  cursor: pointer;
  transition: all 0.3s;
}

.collapse-btn:hover {
  background: rgba(255, 255, 255, 0.3);
  transform: scale(1.05);
}

.breadcrumb-wrapper {
  display: flex;
  align-items: center;
  gap: 8px;
}

.breadcrumb-icon {
  color: #0ea5e9;
  font-size: 18px;
}

.breadcrumb-wrapper :deep(.el-breadcrumb) {
  font-size: 14px;
}

.breadcrumb-wrapper :deep(.el-breadcrumb__item) {
  color: #94a3b8;
}

.breadcrumb-wrapper :deep(.el-breadcrumb__item:last-child) {
  color: #fff;
}

.header-right {
  display: flex;
  align-items: center;
  gap: 16px;
}

.header-search,
.header-notification,
.header-fullscreen {
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 8px;
  background: rgba(14, 165, 233, 0.1);
  color: #94a3b8;
  cursor: pointer;
  transition: all 0.3s;
}

.header-search:hover,
.header-notification:hover,
.header-fullscreen:hover {
  background: rgba(14, 165, 233, 0.2);
  color: #0ea5e9;
  transform: scale(1.05);
}

.user-info {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 6px 12px;
  border-radius: 8px;
  background: rgba(14, 165, 233, 0.1);
  cursor: pointer;
  transition: all 0.3s;
}

.user-info:hover {
  background: rgba(14, 165, 233, 0.2);
}

.user-avatar {
  background: linear-gradient(135deg, #0ea5e9, #8b5cf6);
  border: 2px solid rgba(14, 165, 233, 0.3);
}

.user-details {
  display: flex;
  flex-direction: column;
}

.user-name {
  font-size: 14px;
  font-weight: 500;
  color: #fff;
  line-height: 1;
}

.user-role {
  font-size: 12px;
  color: #64748b;
  margin-top: 2px;
}

.arrow-icon {
  color: #64748b;
  font-size: 12px;
}

/* 主内容区 */
.main-content {
  background: transparent;
  padding: 24px;
  overflow-y: auto;
  height: calc(100vh - 70px);
}

.main-content::-webkit-scrollbar {
  width: 8px;
}

.main-content::-webkit-scrollbar-track {
  background: rgba(15, 23, 42, 0.3);
}

.main-content::-webkit-scrollbar-thumb {
  background: rgba(14, 165, 233, 0.3);
  border-radius: 4px;
}

.main-content::-webkit-scrollbar-thumb:hover {
  background: rgba(14, 165, 233, 0.5);
}

/* 动画 */
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.3s;
}

.fade-enter-from, .fade-leave-to {
  opacity: 0;
}

.fade-transform-enter-active,
.fade-transform-leave-active {
  transition: all 0.3s;
}

.fade-transform-enter-from {
  opacity: 0;
  transform: translateX(-30px);
}

.fade-transform-leave-to {
  opacity: 0;
  transform: translateX(30px);
}

/* 用户菜单 */
.user-menu {
  background: rgba(15, 23, 42, 0.95);
  backdrop-filter: blur(20px);
  border: 1px solid rgba(14, 165, 233, 0.2);
}

.user-menu :deep(.el-dropdown-menu__item) {
  color: #94a3b8;
}

.user-menu :deep(.el-dropdown-menu__item:hover) {
  background: rgba(14, 165, 233, 0.1);
  color: #0ea5e9;
}
</style>
