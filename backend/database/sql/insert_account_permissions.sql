-- 插入账户管理权限
INSERT INTO `permissions` (`name`, `code`, `parent_id`, `sort`, `created_at`, `updated_at`) VALUES
('账户管理', 'account', NULL, 4, NOW(), NOW());

-- 获取刚插入的账户管理父权限ID（假设为5，实际需要查询）
SET @account_parent_id = LAST_INSERT_ID();

-- 插入账户管理子权限
INSERT INTO `permissions` (`name`, `code`, `parent_id`, `sort`, `created_at`, `updated_at`) VALUES
('查看账户', 'account.view', @account_parent_id, 1, NOW(), NOW()),
('创建账户', 'account.create', @account_parent_id, 2, NOW(), NOW()),
('编辑账户', 'account.edit', @account_parent_id, 3, NOW(), NOW()),
('删除账户', 'account.delete', @account_parent_id, 4, NOW(), NOW());

-- 为超级管理员角色(ID=1)分配所有账户管理权限
INSERT INTO `role_permissions` (`role_id`, `permission_id`)
SELECT 1, id FROM `permissions` WHERE `code` LIKE 'account%';

-- 查看结果
SELECT * FROM `permissions` WHERE `code` LIKE 'account%' ORDER BY `sort`;
