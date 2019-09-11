# 有序
有序任务看板由[前端web](https://github.com/TLideaCreator/taskWeb)和后端api两部分组成，此项目为后端API项目

### 后端API
基于[LUMEN](https://lumen.laravel.com/)框架，RESTFul api风格开发接口

#### 安装
###### check代码后运行如下命令安装依赖库
```
composer install
```
###### 安装数据库
```
php artisan migrate
```

###### 修改管理员密码
在database/seeds/InitAdmin.php中将phone，email以及密码（123456）,改为所需字符

```php
class InitAdmin extends BaseSeeder
{
    protected function task()
    {
        \App\Models\User::create([
            'phone'=> '13333333333',
            'email'=> '13333333333@133.com',
            'password'=> \Illuminate\Support\Facades\Crypt::encrypt('123456'),
            'nickname'=> '管理员',
            'avatar'=> '1',
            'is_admin'=>1,
            'token'=>''
        ]);
    }
}
```

###### 初始化数据
在命令行中执行命令
```
php artisan db:seed
```

###### 启动服务
在命令行中执行命令
```
php -S 127.0.0.1:9090 -t public 
```
