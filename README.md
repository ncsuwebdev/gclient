# Google API Client for Laravel #

The `ncsu/gclient` package allows you to more easily integrate the use of Google APIs into your Laravel Application. This package's original focus was aiding in the development of applications that needed tighter integration with an organization's G Suite domain to provide domain management and support. However, this package can be used for other Google APIs.

This package was inspired by the [Google API Client Wrapper](https://github.com/pulkitjalan/google-apiclient) package.

- [Requirements](#requirements)
- [Installation](#installation)
- [Configuration](#configuration)
  - [Setup Credential File Storage](#setup-credential-file-storage)
  - [Create Project in Google Developer Console](#create-project-in-google-developer-console)
  - [Initialize Configuration Variables](#initialize-configuration-variables)
- [Authorization](#authorization)

## Requirements ##
- PHP >7.1
- Laravel >5.6

## Installation ##
You can install the package via composer:
```
composer require ncsu/gclient
```
The package will automatically register itself.

## Configuration ##
There are numerous steps to get API credentials properly working with the Google API.

### Setup Credential File Storage ###
The Google API will provide several json files used to store credentials. These should be stored outside your web-servicable directory space. We recommend creating a new directory called `googleapi` in the root of your project. You can name this whatever you'd like but be sure to reference it properly in the config file.

### Create Project in Google Developer Console ###
Visit the [Google Developer Console](https://console.developers.google.com/) to create a new project that will have it's own API credentials.
1. Specify the necessary API scopes for your project. 
*Make note of the API's selected to ensure you can add them to the config file.*

2. Setup an OAuth 2.0 client ID with application type of Other. Once setup is complete, download the client_secrets.json file and save to the `googleapi` directory.

#### Domain Wide Delegation ###
If working with a G Suite domain and **Domain Wide Delegation** is needed, you'll need to setup and configure a Service Account inside the Developer Console along with an associated user. Download the provided `oauth2service.json` file and save it to the `googleapi` directory.

You'll also need to authorize the API scope list for the service account client ID inside the G Suite Admin Console. [Check out the wiki](https://github.com/ncsuwebdev/gclient/wiki/Google-API-Scopes-for-Delegate-Admin-Access) for a default list to get you started.

### Initialize Configuration Variables ###
You may optionally run
```
php artisan vendor:publish --provider="NCSU\GClient\GoogleClientServiceProvider" --tag="config"
```
To publish a copy of the default configuration values into your application. If you wish to alter any of the Google API Scopes used, you'll need to publish a configuration file.

You'll need to configure the following configuration values in your .env file:

- GOOGLE_APP_NAME
- GOOGLE_CLIENT_ID
- GOOGLE_CLIENT_SECRET
- GOOGLE_SERVICE_ACCOUNT_NAME
- GOOGLE_SERVICE_ACCOUNT_JSON
- GOOGLE_CUSTOMER_ID
- GOOGLE_DOMAIN_NAME
- GOOGLE_SECRETS_FROM_BASE_PATH
- GOOGLE_CREDENTIALS_PATH
- GOOGLE_TOKEN_PATH

#### Multiple Domains ####
If you need to handle multiple domains but don't want to worry about constantly changing your .env or credentials file, you can set `GOOGLE_ENV` in your `.env` file to be a string that will prefix your config variables.

For example if you set `GOOGLE_ENV='TESTING'` the config file will search for the necessary variables prefixed with `TESTING_` such as `TESTING_GOOGLE_CLIENT_ID`.

## Authorization ##
To authorize your application to talk to the Google API run the following command:
```
php artisan gclient:authorize
```
Follow the prompts to authorize API access using the Google Account that you used with the developer console.

If you change any API scopes after authorization, you should run
```
php artisan gclient:authorize --reauth
```
to grab a fresh token with the proper scopes.
