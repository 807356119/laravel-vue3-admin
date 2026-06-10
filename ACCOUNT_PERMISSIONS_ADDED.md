# ✅ 账户管理权限已添加到数据库！

## 📊 已插入的权限

### 父权限
- **账户管理** (`account`)

### 子权限
- **查看账户** (`account.view`)
- **创建账户** (`account.create`)
- **编辑账户** (`account.edit`)
- **删除账户** (`account.delete`)

---

## 🔐 权限说明

### account.view - 查看账户
查看账户列表和详情

### account.create - 创建账户
添加新账户

### account.edit - 编辑账户
修改账户信息

### account.delete - 删除账户
删除账户

---

## 👥 角色分配

**超级管理员（ID=1）** 已自动分配所有账户管理权限。

其他角色可以在**角色管理**页面手动分配权限。

---

## 🎯 使用方法

1. **登录系统**
2. **进入"角色管理"**
3. **编辑角色**
4. **勾选"账户管理"相关权限**
5. **保存**

---

## 🔄 如果需要重新执行

如果遇到问题，可以先删除再重新插入：

```sql
-- 删除账户管理相关权限
DELETE FROM role_permissions WHERE permission_id IN (SELECT id FROM permissions WHERE code LIKE 'account%');
DELETE FROM permissions WHERE code LIKE 'account%';

-- 然后重新执行插入SQL
```

---

**账户管理权限已成功添加到数据库！** ✅
