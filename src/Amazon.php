<?php namespace LemonStand\OAuth2\Client\Provider;

use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Token\AccessToken;
use Psr\Http\Message\ResponseInterface;

class Amazon extends AbstractProvider
{
    public $scopeSeparator = ' ';
    public $testMode = false;

    public function __construct($options = [])
    {
        parent::__construct($options);

        if (isset($options['testMode'])) {
            $this->testMode = $options['testMode'];
        }
    }

    public function getBaseAuthorizationUrl()
    {
        return 'https://www.amazon.com/ap/oa';
    }

    public function getBaseAccessTokenUrl(array $params)
    {
        return ($this->testMode) ? 'https://api.sandbox.amazon.com/auth/o2/token' : 'https://api.amazon.com/auth/o2/token';
    }

    public function getResourceOwnerDetailsUrl(AccessToken $token)
    {
        $url = ($this->testMode) ? 'https://api.sandbox.amazon.com/user/profile' : 'https://api.amazon.com/user/profile';
        return $url . '?access_token=' . $token;
    }

    public function getAuthorizationUrl(array $options = array())
    {
        $url = parent::getAuthorizationUrl($options);

        if ($this->testMode) {
            $url .= '&sandbox=true';
        }

        return $url;
    }

    public function getDefaultScopes()
    {
        return  ['profile', 'payments:widget', 'payments:shipping_address'];
    }

    protected function checkResponse(ResponseInterface $response, $data)
    {
        if (!empty($data['error'])) {
            $message = print_r( $data['error'], true );
            throw new \Exception($message);
        }
    }

    protected function createResourceOwner(array $response, AccessToken $token)
    {
        return new AmazonUser((array)$response);
    }
}
