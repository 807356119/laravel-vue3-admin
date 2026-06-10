import request from '@/utils/request'

export const getUserList = (params) => {
  return request({
    url: '/users',
    method: 'get',
    params
  })
}

export const createUser = (data) => {
  return request({
    url: '/users',
    method: 'post',
    data
  })
}

export const updateUser = (id, data) => {
  return request({
    url: `/users/${id}`,
    method: 'put',
    data
  })
}

export const deleteUser = (id) => {
  return request({
    url: `/users/${id}`,
    method: 'delete'
  })
}

export const resetPassword = (id, data) => {
  return request({
    url: `/users/${id}/reset-password`,
    method: 'post',
    data
  })
}

export const toggleUserStatus = (id) => {
  return request({
    url: `/users/${id}/toggle-status`,
    method: 'post'
  })
}
