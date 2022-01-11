# phplib
lessok的意思就是少就是好! php最简洁的操作类。

## 安装

> composer require lessok/phplib

### db类使用教程

1. 创建user数据表

```sql
CREATE TABLE `user` (
	`id` INT(10) NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(50) NULL DEFAULT NULL COMMENT '姓名',
	`age` INT(10) NULL DEFAULT NULL COMMENT '年龄',
	`sex` ENUM('男','女') NULL DEFAULT NULL COMMENT '性别',
	PRIMARY KEY (`id`) USING BTREE
)
ENGINE=InnoDB;
```



2. mysql操作示例

```php
<?php
require "../vendor/autoload.php";
$config= array (
		'dsn'=> 'mysql:host=localhost;port=3306;dbname=test',
		'user'=>'root',
		'pass'=>'',
		'options'=>array(
		    \PDO::ATTR_ERRMODE=>\PDO::ERRMODE_EXCEPTION,
	      	\PDO::ATTR_TIMEOUT => 5,
		));
$db = new \lessok\Db($config);
$ret = $db->rows("select * from user");
?>
```



### http操作类

```php
<?php
require "../vendor/autoload.php";
$http = \lessok\Http::getInstance();
$url = "http://httpbin.org/ip";
$ret = $http->request($url);
?>
```
