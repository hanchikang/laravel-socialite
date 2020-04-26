<?php

namespace CKHan\Socialite;

use Illuminate\Support\ServiceProvider;
use Laravel\Socialite\Contracts\Factory;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use CKHan\Socialite\Two\WechatProvider;
use CKHan\Socialite\Two\WechatQrconnectProvider;
use CKHan\Socialite\Two\QQProvider;

class SocialiteServiceProvider extends ServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        $this->app->make(Factory::class)->extend('wechat', function ($app) {
            $config = $app['config']['services.wechat'];
            return $this->buildProvider(WechatProvider::class, $config);
        });

        $this->app->make(Factory::class)->extend('wechat-qrconnect', function ($app) {
            $config = $app['config']['services.wechat-qrconnect'];
            return $this->buildProvider(WechatQrconnectProvider::class, $config);
        });

        $this->app->make(Factory::class)->extend('qq', function ($app) {
            $config = $app['config']['services.qq'];
            $provider = $this->buildProvider(QQProvider::class, $config);
            if($config['mobile'] === true)
                $provider->mobile();
            if($config['unionid'] === true)
                $provider->haveAppliedUnionid();
            return $provider;
        });
    }

    /**
     * Build an OAuth 2 provider instance.
     *
     * @param  string  $provider
     * @param  array  $config
     * @return \Laravel\Socialite\Two\AbstractProvider
     */
    public function buildProvider($provider, $config)
    {
        return new $provider(
            $this->app['request'],
            $config['client_id'],
            $config['client_secret'],
            $this->formatRedirectUrl($config),
            Arr::get($config, 'guzzle', [])
        );
    }

    /**
     * Format the callback URL, resolving a relative URI if needed.
     *
     * @param  array  $config
     * @return string
     */
    protected function formatRedirectUrl(array $config)
    {
        $redirect = value($config['redirect']);

        return Str::startsWith($redirect, '/')
            ? $this->app['url']->to($redirect)
            : $redirect;
    }
}
