<?php

namespace NCSU\GClient;

use Google_Client;
use NCSU\GClient\Exceptions\MissingCredentialsException;
use NCSU\GClient\Exceptions\UnknownServiceException;

class Client
{
    /**
     * @var array
     */
    protected $config;

    /**
     * @var \Google_Client
     */
    protected $client;

    /**
     * Client constructor.
     *
     * @param array $config
     * @param string $method
     * @param string $userEmail
     *
     * @throws \Exception
     */
    public function __construct( array $config, $method = 'auto', $userEmail = '' ) {
        if ( $this->credentialsMissing( $config ) ) {
            throw new MissingCredentialsException( 'Google API Credentials missing. Please check your config file to ensure credentials are present.' );
        }

        $this->config = $config;

        $this->client = new Google_Client();
        $this->client->setApplicationName( \Arr::get( $config, 'application_name', '' ) );

        //authentication as service account
        if ( ( $method == 'auto' || $method == 'serviceaccount' ) &&
             \Arr::get( $this->config, 'service.enable' ) == true
        ) {
            putenv( 'GOOGLE_APPLICATION_CREDENTIALS=' . \Arr::get( $config, 'service.file' ) );
            $this->serviceAccountAuth( $config );
        } elseif ( $method != 'serviceaccount' ) {
            $this->client->setScopes( \Arr::get( $config, 'scopes', [] ) );
            try {
                $this->normalOAuth2Auth( $config, $userEmail );
            } catch ( MissingCredentialsException $e ) {
                throw new MissingCredentialsException($e->getMessage());
            }
        } else {
            throw new MissingCredentialsException( 'Credentials are not available for specified method' );
        }
    }

    /**
     * Getter for the google client.
     *
     * @return \Google_Client
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Setter for the google client.
     *
     * @param $client
     *
     * @return self
     */
    public function setClient(Google_Client $client)
    {
        $this->client = $client;

        return $this;
    }

    protected function serviceAccountAuth(array $config, $userEmail = '')
    {
        try {
            $this->client->setScopes(\Arr::get($config, 'scopes', []));

            $this->client->setAuthConfig(base_path(\Arr::get($config, 'service.file')));

            if ($userEmail != '') {
                $this->client->setSubject($userEmail);
            } else {
                $this->client->setSubject(\Arr::get($config, 'service.username', $userEmail));
            }
        } catch (\Exception $e) {
            dd($e);
            return false;
        }

        return true;
    }

    /**
     * @param array $config
     * @param string $userEmail
     *
     * @throws MissingCredentialsException
     */
    protected function normalOAuth2Auth(array $config, $userEmail = '')
    {
        $this->client->setAccessType(\Arr::get($config, 'access_type', 'offline'));
        $this->client->setApprovalPrompt(\Arr::get($config, 'approval_prompt', 'auto'));

        if (base_path(\Arr::get($config, 'credentials_file') != '')) {
            try {
                $this->client->setAuthConfig(base_path(\Arr::get($config, 'credentials_file', '')));
            } catch (\Exception $e) {
                throw new MissingCredentialsException('Could not load credentials file: ' . base_path(\Arr::get($config, 'credentials_file', '')));
            }
        } else {
            $this->client->setClientId(\Arr::get($config, 'client_id'));
            $this->client->setClientSecret(\Arr::get($config, 'client_secret'));
            $this->client->setRedirectUri(\Arr::get($config, 'redirect_uri'));
        }

        try {
            $this->authorizeCredentials($config, $userEmail);
        } catch (\Exception $e) {
        }
    }

    /**
     * @param array $config
     * @param string $userEmail
     *
     * @throws \Exception
     */
    protected function authorizeCredentials(array $config, $userEmail = '')
    {
        if (file_exists(base_path(\Arr::get($config, 'token_file')))) {
            $accessToken = json_decode(
                file_get_contents(base_path(\Arr::get($config, 'token_file')), true),
                true
            );
        } else {
            // Request authorization from the user.
            $authUrl = $this->client->createAuthUrl();
            printf("Using the Google account which you plan to authorize, open the following link in your browser:\n\n%s\n\n", $authUrl);
            print 'Enter verification code: ';
            $authCode = trim(fgets(STDIN));
            printf("\n");
            // Exchange authorization code for an access token.
            $accessToken = $this->client->fetchAccessTokenWithAuthCode($authCode);

            // Check to see if there was an error.
            if (array_key_exists('error', $accessToken)) {
                throw new \Exception(join(', ', $accessToken));
            }

            // Store the credentials to disk.
            if (!file_exists(dirname(base_path(\Arr::get($config, 'token_file'))))) {
                mkdir(dirname(base_path(\Arr::get($config, 'token_file'))), 0700, true);
            }
            file_put_contents(base_path(\Arr::get($config, 'token_file')), json_encode($accessToken));
            printf("Credentials saved to %s\n", base_path(\Arr::get($config, 'token_file')));
        }

        $this->client->setAccessToken($accessToken);

        // Refresh the token if it's expired.
        if ($this->client->isAccessTokenExpired()) {
            $this->client->fetchAccessTokenWithRefreshToken($this->client->getRefreshToken());
            file_put_contents(
                base_path(\Arr::get($config, 'token_file')),
                json_encode($this->client->getAccessToken())
            );
        }
    }

    /**
     * Getter for the google service.
     *
     * @param string $service
     * @param string $username
     *
     * @throws \Exception
     *
     * @return \Google_Service|object
     */
    public function make($service, $username = '')
    {
        if ($username != '') {
            $this->client->setSubject($username);
        } else {
            $this->client->setSubject(\Arr::get($this->config, 'service.username', $username));
        }
        $service = 'Google_Service_' . ucfirst($service);
        if (class_exists($service)) {
            $class = new \ReflectionClass($service);

            return $class->newInstance($this->client);
        }
        throw new UnknownServiceException($service);
    }

    /**
     * Setup correct auth method based on type.
     *
     * @param $userEmail
     *
     * @return void
     */
    protected function auth($userEmail = '')
    {
        // see (and use) if user has set Credentials
        if ($this->useAssertCredentials($userEmail)) {
            return;
        }
        // fallback to compute engine or app engine
        $this->client->useApplicationDefaultCredentials();
    }

    /**
     * Determine and use credentials if user has set them.
     *
     * @param $userEmail
     *
     * @return bool used or not
     */
    protected function useAssertCredentials($userEmail = '')
    {
        $serviceJsonUrl = \Arr::get($this->config, 'service.file', '');
        if (empty($serviceJsonUrl)) {
            return false;
        }
        try {
            $this->client->setAuthConfig($serviceJsonUrl);
        } catch (\Google_Exception $e) {
        }

        if (!empty($userEmail)) {
            $this->client->setSubject($userEmail);
        }

        return true;
    }

    /**
     * Magic call method.
     *
     * @param string $method
     * @param array $parameters
     *
     * @throws \BadMethodCallException
     *
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        if (method_exists($this->client, $method)) {
            return call_user_func_array([$this->client, $method], $parameters);
        }
        throw new \BadMethodCallException(sprintf('Method [%s] does not exist.', $method));
    }

    /**
     * @param array $config
     *
     * @return bool $credentialsMissing
     */
    public function credentialsMissing(array $config) {
        $credentialsMissing = true;

        if($config['client_id'] != '' && $config['client_secret'] != '' && $config['redirect_uri'] != '') {
            $credentialsMissing = false;
            return $credentialsMissing;
        }

        if(\Arr::get($config, 'service.file') != null) {
            $credentialsMissing = false;
            return $credentialsMissing;
        }

        return $credentialsMissing;
    }
}
