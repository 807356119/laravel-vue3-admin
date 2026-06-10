import { createRouter, createWebHistory } from 'vue-router'
import { useUserStore } from '@/stores/user'

const routes = [
  {
    path: '/login',
    name: 'Login',
    component: () => import('@/views/login/index.vue'),
    meta: { title: '登录', requiresAuth: false }
  },
  {
    path: '/',
    component: () => import('@/views/Layout.vue'),
    redirect: '/dashboard',
    meta: { requiresAuth: true },
    children: [
      {
        path: 'dashboard',
        name: 'Dashboard',
        component: () => import('@/views/Dashboard.vue'),
        meta: { title: '首页' }
      },
      {
        path: 'users',
        name: 'UserList',
        component: () => import('@/views/user/index.vue'),
        meta: { title: '用户管理', permission: 'user.view' }
      },
      {
        path: 'roles',
        name: 'RoleList',
        component: () => import('@/views/role/index.vue'),
        meta: { title: '角色管理', permission: 'role.view' }
      },
      {
        path: 'accounts',
        name: 'Accounts',
        component: () => import('@/views/account/index.vue'),
        meta: { title: '账户管理' }
      },
      {
        path: 'system',
        name: 'SystemSettings',
        component: () => import('@/views/system/index.vue'),
        meta: { title: '系统设置', permission: 'system.manage' }
      }
    ]
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

router.beforeEach(async (to, from, next) => {
  const userStore = useUserStore()

  if (to.meta.requiresAuth === false) {
    next()
    return
  }

  if (!userStore.isLoggedIn) {
    next('/login')
    return
  }

  if (!userStore.userInfo) {
    try {
      await userStore.getUserInfo()
    } catch (error) {
      next('/login')
      return
    }
  }

  if (to.meta.permission && !userStore.hasPermission(to.meta.permission)) {
    next('/')
    return
  }

  next()
})

export default router
