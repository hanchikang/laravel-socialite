<?php

namespace CKHan\Socialite\Two;

use CKHan\Socialite\Two\WechatProvider;

class WechatQrconnectProvider extends WechatProvider
{
    /**
     * {@inheritdoc}.
     */
    protected $scopes = ['snsapi_login'];

    /**
     * {@inheritdoc}.
     */
    public function getAuthUrl($state)
    {
        return $this->buildAuthUrlFromBase('https://open.weixin.qq.com/connect/qrconnect', $state);
    }
}
