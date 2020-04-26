# Socialite for Laravel 6+
Socialite是基于官方扩展包laravel/socialite的扩展，加入了微信扫描登录、微信公众号授权登录、QQ登录。

## Require
- laravel/socialite >= 4.3

## Installation
运行下面的命令安装:

    "composer require  ckhan/laravel-socialite"   

## Configuration
配置方法和官方一致[Socialite 社会化登录](https://learnku.com/docs/laravel/6.x/socialite/5192#configuration)
密钥凭证放在config/services.php配置文件中：

### 微信扫描登录
```php
'wechat-qrconnect' => [
    'client_id' => '应用唯一标识，在微信开放平台提交应用审核通过后获得',
    'client_secret' => '应用密钥AppSecret，在微信开放平台提交应用审核通过后获得',
    'redirect' => '授权后，重定向到网站的地址',
],
``` 

### 微信公众号授权登录
```php
'wechat' => [
    'client_id' => '公众号的唯一标识',
    'client_secret' => '公众号的appsecret',
    'redirect' => '授权后，重定向到网站的地址',
],
``` 

### 微信公众号授权登录
```php
'qq' => [
    'client_id' => '申请QQ登录成功后，分配给应用的appid',
    'client_secret' => '申请QQ登录成功后，分配给网站的appkey',
    'redirect' => '授权后，重定向到网站的地址',
    'mobile' => 'true|false，为true时，显示为手机端的样式',
    'unionid' => 'true|false，如何已经申请unionID了，请设置为true',
],
``` 

## Usage
使用方法和官方一致[Socialite 社会化登录](https://learnku.com/docs/laravel/6.x/socialite/5192#routing)

### 路由
```php
Route::get('login/socialite/{driver}', 'Auth\LoginController@redirectToProvider');
Route::get('login/socialite/{driver}/callback', 'Auth\LoginController@handleProviderCallback');
```

### 控制器
```php
/**
 * 重定向到第三方，获取code
 * @param $driver
 * @return \Symfony\Component\HttpFoundation\RedirectResponse
 */
public function redirectToProvider($driver)
{
    if(!config('services.'.$driver))
        abort(404);
    return Socialite::driver($driver)->redirect();
}

/**
 * 从第三方，获取用户信息
 * @param $driver
 */
public function handleProviderCallback($driver)
{
    if(!config('services.'.$driver))
        abort(404);
    $user = Socialite::driver($driver)->user();

    //实现你的授权登录逻辑;
}
```
 
## Documentation
官方扩展[Socialite 社会化登录](https://learnku.com/docs/laravel/6.x/socialite/5192)

## License
`Socialite` is licensed under [The MIT License (MIT)](LICENSE).
