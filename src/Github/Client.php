<?php

namespace Guesl\Admin\Github;

use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * Class Client
 * @package Guesl\Admin\Github
 */
class Client
{
    /**
     * The HTTP request instance.
     *
     * @var Request
     */
    protected $request;

    /**
     * The HTTP Client instance.
     *
     * @var \GuzzleHttp\Client
     */
    protected $httpClient;

    /**
     * The client ID.
     *
     * @var string
     */
    protected $clientId;

    /**
     * The client secret.
     *
     * @var string
     */
    protected $clientSecret;

    /**
     * The redirect URL.
     *
     * @var string
     */
    protected $redirectUrl;

    /**
     * The scopes being requested.
     *
     * @var array
     */
    protected $scopes = [];

    /**
     * The separating character for the requested scopes.
     *
     * @var string
     */
    protected $scopeSeparator = ' ';

    /**
     * The type of the encoding in the query.
     *
     * @var int Can be either PHP_QUERY_RFC3986 or PHP_QUERY_RFC1738.
     */
    protected $encodingType = PHP_QUERY_RFC1738;

    /**
     * Redirect the user of the application to the provider's authentication screen.
     *
     * @return RedirectResponse
     */
    public function redirect()
    {
        $state = null;
        $this->request->session()->put('state', $state = $this->getState());

        return new RedirectResponse($this->getAuthUrl($state));
    }

    /**
     * Get the authentication URL for the provider.
     *
     * @param string $state
     * @return string
     */
    protected function getAuthUrl($state)
    {
        return $this->buildAuthUrlFromBase('https://github.com/login/oauth/authorize', $state);
    }

    /**
     * Build the authentication URL for the provider from the given base URL.
     *
     * @param string $url
     * @param string $state
     * @return string
     */
    protected function buildAuthUrlFromBase($url, $state)
    {
        return $url . '?' . http_build_query($this->getCodeFields($state), '', '&', $this->encodingType);
    }

    /**
     * Format the given scopes.
     *
     * @param array $scopes
     * @param string $scopeSeparator
     * @return string
     */
    protected function formatScopes(array $scopes, $scopeSeparator)
    {
        return implode($scopeSeparator, $scopes);
    }

    /**
     * Get the GET parameters for the code request.
     *
     * @param string|null $state
     * @return array
     */
    protected function getCodeFields($state = null)
    {
        return [
            'client_id' => $this->clientId,
            'redirect_uri' => $this->redirectUrl,
            'scope' => $this->formatScopes($this->getScopes(), $this->scopeSeparator),
            'response_type' => 'code',
            'state' => $state,
        ];
    }

    /**
     * Get the token URL for the provider.
     *
     * @return string
     */
    protected function getTokenUrl()
    {
        return 'https://github.com/login/oauth/access_token';
    }

    /**
     * Determine if the current request / session has a mismatching "state".
     *
     * @return bool
     */
    protected function hasInvalidState()
    {
        $state = $this->request->session()->pull('state');
        return !(strlen($state) > 0 && $this->request->input('state') === $state);
    }

    /**
     * Get the access token response for the given code.
     *
     * @param string $code
     * @return array
     * @throws GuzzleException
     */
    public function getAccessTokenResponse($code)
    {
        $response = $this->getHttpClient()->post($this->getTokenUrl(), [
            'headers' => ['Accept' => 'application/json'],
            'form_params' => $this->getTokenFields($code),
        ]);

        return json_decode($response->getBody(), true);
    }

    /**
     * Get the POST fields for the token request.
     *
     * @param string $code
     * @return array
     */
    protected function getTokenFields(string $code)
    {
        return [
            'grant_type' => 'authorization_code',
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'code' => $code,
            'redirect_uri' => $this->redirectUrl,
        ];
    }

    /**
     * Get the access token response for the given refresh token.
     *
     * @param string $refreshToken
     * @return array
     * @throws GuzzleException
     */
    public function getAccessTokenByRefreshToken(string $refreshToken)
    {
        $response = $this->getHttpClient()->post($this->getTokenUrl(), [
            'headers' => ['Accept' => 'application/json'],
            'form_params' => $this->getRefreshTokenFields($refreshToken),
        ]);

        return json_decode($response->getBody(), true);
    }

    /**
     * Get the POST fields for the token request.
     *
     * @param string $refreshToken
     * @return array
     */
    protected function getRefreshTokenFields(string $refreshToken)
    {
        return [
            'grant_type' => 'refresh_token',
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'refresh_token' => $refreshToken,
        ];
    }

    /**
     * @return Request
     */
    public function getRequest(): Request
    {
        return $this->request;
    }

    /**
     * @param Request $request
     * @return Client
     */
    public function setRequest(Request $request): Client
    {
        $this->request = $request;
        return $this;
    }

    /**
     * Get a instance of the Guzzle HTTP client.
     *
     * @return \GuzzleHttp\Client
     */
    protected function getHttpClient()
    {
        if (is_null($this->httpClient)) {
            $this->httpClient = new \GuzzleHttp\Client();
        }

        return $this->httpClient;
    }

    /**
     * @param Client $httpClient
     * @return Client
     */
    public function setHttpClient(Client $httpClient): Client
    {
        $this->httpClient = $httpClient;
        return $this;
    }

    /**
     * @return string
     */
    public function getClientId(): string
    {
        return $this->clientId;
    }

    /**
     * @param string $clientId
     * @return Client
     */
    public function setClientId(string $clientId): Client
    {
        $this->clientId = $clientId;
        return $this;
    }

    /**
     * @return string
     */
    public function getClientSecret(): string
    {
        return $this->clientSecret;
    }

    /**
     * @param string $clientSecret
     * @return Client
     */
    public function setClientSecret(string $clientSecret): Client
    {
        $this->clientSecret = $clientSecret;
        return $this;
    }

    /**
     * @return string
     */
    public function getRedirectUrl(): string
    {
        return $this->redirectUrl;
    }

    /**
     * @param string $redirectUrl
     * @return Client
     */
    public function setRedirectUrl(string $redirectUrl): Client
    {
        $this->redirectUrl = $redirectUrl;
        return $this;
    }

    /**
     * @return array
     */
    public function getScopes(): array
    {
        return $this->scopes;
    }

    /**
     * @param array $scopes
     * @return Client
     */
    public function setScopes(array $scopes): Client
    {
        $this->scopes = $scopes;
        return $this;
    }

    /**
     * Get the string used for session state.
     *
     * @return string
     */
    protected function getState()
    {
        return Str::random(40);
    }
}
