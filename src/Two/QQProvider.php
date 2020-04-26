<?php

namespace CKHan\Socialite\Two;

use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\ProviderInterface;
use Laravel\Socialite\Two\User;

class QQProvider extends AbstractProvider implements ProviderInterface
{
    /**
     * 用于展示的样式。不传则默认展示为PC下的样式。
     * 如果传入“mobile”，则展示为mobile端下的样式。
     * @var string
     */
    protected $display = '';

    /**
     * 是否有申请unionid
     * @var bool
     */
    protected $haveAppliedUnionid = false;

    /**
     * {@inheritdoc}.
     */
    protected $scopes = ['get_user_info'];

    /**
     * 设置展示的样式
     * @return $this
     */
    public function mobile(string $value = 'mobile')
    {
        $this->display = $value;

        return $this;
    }

    /**
     * 设置已申请unionid
     * @param bool $value
     * @return $this
     */
    public function haveAppliedUnionid(bool $value = true)
    {
        $this->haveAppliedUnionid = $value;

        return $this;
    }

    /**
     * {@inheritdoc}.
     */
    public function getAuthUrl($state)
    {
        return $this->buildAuthUrlFromBase('https://graph.qq.com/oauth2.0/authorize', $state);
    }

    /**
     * {@inheritdoc}.
     */
    protected function getCodeFields($state = null)
    {
        $codeFields = parent::getCodeFields($state);
        if($this->display)
            $codeFields['display'] = $this->display;

        return $codeFields;
    }

    /**
     * {@inheritdoc}.
     */
    public function getTokenUrl()
    {
        return 'https://graph.qq.com/oauth2.0/token';
    }

    /**
     * {@inheritdoc}.
     */
    protected function getTokenFields($code)
    {
        $tokenFields = parent::getCodeFields($code);
        $tokenFields['grant_type'] = 'authorization_code';
        return $tokenFields;
    }

    /**
     * {@inheritdoc}.
     */
    public function getUserByToken($token)
    {
        $query = [
            'access_token' => $token,
        ];
        if($this->haveAppliedUnionid)
            $query['unionid'] = 1;
        $response = $this->getHttpClient()->get('https://graph.qq.com/oauth2.0/me', [
            'query' => $query,
        ]);
        $me = json_decode($response->getBody(), true);

        $response = $this->getHttpClient()->get('https://graph.qq.com/user/get_user_info', [
            'query' => [
                'access_token' => $token,
                'oauth_consumer_key' => $this->clientId,
                'openid' => $me['openid'],
            ],
        ]);
        $user = json_decode($response->getBody(), true);
        $user['unionid'] = isset($me['unionid']) ? $me['unionid'] : null;

        return $user;

    }

    /**
     * {@inheritdoc}.
     */
    public function mapUserToObject(array $user)
    {
        return (new User())->setRaw($user)->map([
            'id' => $user['unionid'] ? $user['unionid'] : $user['openid'],
            'nickname' => $user['nickname'],
            'name' => $user['nickname'],
            'email' => null,
            'avatar' => $user['figureurl_qq_2'],
        ]);
    }
}
