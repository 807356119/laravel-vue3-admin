import request from '@/utils/request'

export const getAccountList = (params) => {
  return request({
    url: '/accounts',
    method: 'get',
    params
  })
}

export const createAccount = (data) => {
  return request({
    url: '/accounts',
    method: 'post',
    data
  })
}

export const updateAccount = (id, data) => {
  return request({
    url: `/accounts/${id}`,
    method: 'put',
    data
  })
}

export const deleteAccount = (id) => {
  return request({
    url: `/accounts/${id}`,
    method: 'delete'
  })
}

export const getAccountDetail = (id) => {
  return request({
    url: `/accounts/${id}`,
    method: 'get'
  })
}

export const toggleFavorite = (id) => {
  return request({
    url: `/accounts/${id}/toggle-favorite`,
    method: 'post'
  })
}

export const getPassword = (id) => {
  return request({
    url: `/accounts/${id}/password`,
    method: 'get'
  })
}

export const uploadImage = (file) => {
  const formData = new FormData()
  formData.append('image', file)
  return request({
    url: '/accounts/upload-image',
    method: 'post',
    data: formData,
    headers: {
      'Content-Type': 'multipart/form-data'
    }
  })
}
