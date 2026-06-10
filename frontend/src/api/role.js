import request from '@/utils/request'

export const getRoleList = (params) => {
  return request({
    url: '/roles',
    method: 'get',
    params
  })
}

export const createRole = (data) => {
  return request({
    url: '/roles',
    method: 'post',
    data
  })
}

export const updateRole = (id, data) => {
  return request({
    url: `/roles/${id}`,
    method: 'put',
    data
  })
}

export const deleteRole = (id) => {
  return request({
    url: `/roles/${id}`,
    method: 'delete'
  })
}

export const getPermissions = () => {
  return request({
    url: '/permissions',
    method: 'get'
  })
}

export const assignPermissions = (roleId, permissions) => {
  return request({
    url: `/roles/${roleId}/permissions`,
    method: 'post',
    data: { permissions }
  })
}
