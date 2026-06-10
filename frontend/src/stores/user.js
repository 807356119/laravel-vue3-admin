import { defineStore } from 'pinia'
import { login, logout, getUserInfo } from '@/api/auth'
import router from '@/router'

export const useUserStore = defineStore('user', {
  state: () => ({
    token: localStorage.getItem('token') || '',
    userInfo: null,
    permissions: []
  }),

  getters: {
    isLoggedIn: (state) => !!state.token,
    hasPermission: (state) => (permission) => {
      return state.permissions.includes(permission)
    }
  },

  actions: {
    async login(credentials) {
      try {
        const res = await login(credentials)

        // 修复：后端返回的是 access_token，不是 token
        const token = res.data.access_token
        const user = res.data.user
        const permissions = res.data.permissions || []

        // 保存到state和localStorage
        this.token = token
        this.userInfo = user
        this.permissions = permissions

        localStorage.setItem('token', token)
        localStorage.setItem('user', JSON.stringify(user))
        localStorage.setItem('permissions', JSON.stringify(permissions))

        return res
      } catch (error) {
        throw error
      }
    },

    async getUserInfo() {
      try {
        const res = await getUserInfo()
        this.userInfo = res.data.user
        this.permissions = res.data.permissions || []

        // 同步到localStorage
        localStorage.setItem('user', JSON.stringify(res.data.user))
        localStorage.setItem('permissions', JSON.stringify(res.data.permissions))

        return res
      } catch (error) {
        this.logout()
        throw error
      }
    },

    async logout() {
      try {
        await logout()
      } catch (error) {
        console.error('Logout error:', error)
      } finally {
        this.token = ''
        this.userInfo = null
        this.permissions = []
        localStorage.removeItem('token')
        localStorage.removeItem('user')
        localStorage.removeItem('permissions')
        router.push('/login')
      }
    }
  }
})
