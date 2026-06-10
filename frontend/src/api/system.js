import request from '@/utils/request'

export const getSystemSettings = () => {
  return request({
    url: '/settings',
    method: 'get'
  })
}

export const updateSystemSettings = (data) => {
  return request({
    url: '/settings',
    method: 'put',
    data
  })
}
