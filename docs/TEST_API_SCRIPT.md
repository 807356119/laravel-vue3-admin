## 🧪 测试脚本

在浏览器控制台（F12 → Console）执行以下代码：

```javascript
// 1. 检查Token是否存在
console.log('Token存在:', !!localStorage.getItem('token'))
console.log('Token长度:', localStorage.getItem('token')?.length)

// 2. 测试账户列表API
const testAccountsAPI = async () => {
  try {
    const token = localStorage.getItem('token')
    console.log('开始请求...')
    
    const response = await fetch('http://localhost:8000/api/accounts?page=1&size=12', {
      method: 'GET',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json',
        'Content-Type': 'application/json'
      }
    })
    
    console.log('HTTP状态:', response.status)
    
    const data = await response.json()
    console.log('返回数据:', data)
    
    if (data.code === 200) {
      console.log('✅ 成功！列表数量:', data.data.list?.length)
      console.log('总数:', data.data.pagination?.total)
    } else {
      console.error('❌ 错误:', data.message)
    }
  } catch (error) {
    console.error('❌ 请求失败:', error)
  }
}

testAccountsAPI()
```

这个脚本会告诉你：
1. Token是否存在
2. API请求是否成功
3. 返回的数据格式
4. 具体的错误信息

**请运行这个脚本并告诉我输出结果！** 🔍
