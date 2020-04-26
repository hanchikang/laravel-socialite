<?php

namespace CKHan\Socialite\Two;

use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\ProviderInterface;
use Laravel\Socialite\Two\User;

class WechatProvider extends AbstractProvider implements ProviderInterface
{
    /**
     * @var string
     */
    protected $openId;

    /**
     * {@inheritdoc}.
     */
    protected $scopes = ['snsapi_userinfo'];

    /**
     * {@inheritdoc}.
     */
    public function getAuthUrl($state)
    {
        return $this->buildAuthUrlFromBase('https://open.weixin.qq.com/connect/oauth2/authorize', $state);
    }

    /**
     * {@inheritdoc}.
     */
    protected function buildAuthUrlFromBase($url, $state)
    {
        return parent::buildAuthUrlFromBase($url, $state).'#wechat_redirect';
    }

    /**
     * {@inheritdoc}.
     */
    protected function getCodeFields($state = null)
    {
        $codeFields = parent::getCodeFields($state);
        $codeFields['appid'] = $codeFields['client_id'];
        unset($codeFields['client_id']);

        return $codeFields;
    }

    /**
     * {@inheritdoc}.
     */
    public function getTokenUrl()
    {
        return 'https://api.weixin.qq.com/sns/oauth2/access_token';
    }

    /**
     * {@inheritdoc}.
     */
    public function getAccessTokenResponse($code)
    {
        $accessTokenResponse = parent::getAccessTokenResponse($code);
        $this->openId = $accessTokenResponse['openid'];

        return $accessTokenResponse;
    }

    /**
     * {@inheritdoc}.
     */
    protected function getTokenFields($code)
    {
        return [
            'appid' => $this->clientId,
            'secret' => $this->clientSecret,
            'code' => $code,
            'grant_type' => 'authorization_code',
        ];
    }

    /**
     * {@inheritdoc}.
     */
    public function getUserByToken($token)
    {
        $response = $this->getHttpClient()->get('https://api.weixin.qq.com/sns/userinfo', [
            'query' => [
                'access_token' => $token,
                'openid' => $this->openId,
                'lang' => 'zh_CN',
            ],
        ]);

        return json_decode($response->getBody(), true);

    }

    /**
     * {@inheritdoc}.
     */
    public function mapUserToObject(array $user)
    {
        return (new User())->setRaw($user)->map([
            'id' => $user['unionid'],
            'nickname' => $user['nickname'],
            'name' => $user['nickname'],
            'email' => null,
            'avatar' => $user['headimgurl'],
        ]);
    }
}
