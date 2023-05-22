<?php
   $cred_prefix = (env('GOOGLE_ENV','') != '' ? env('GOOGLE_ENV').'_' : '');
   return [
    /*
    |----------------------------------------------------------------------------
    | Google application name
    |----------------------------------------------------------------------------
    */
    'application_name' => env('GOOGLE_APP_NAME', 'Google Laravel App'),

    /*
    |----------------------------------------------------------------------------
    | Google OAuth 2.0 access
    |----------------------------------------------------------------------------
    |
    | Keys for OAuth 2.0 access, see the API console at
    | https://developers.google.com/console
    |
    */
    'client_id' => env($cred_prefix.'GOOGLE_CLIENT_ID', ''),
    'client_secret' => env($cred_prefix.'GOOGLE_CLIENT_SECRET', ''),
    'redirect_uri' => env($cred_prefix.'GOOGLE_REDIRECT_URI', ''),
    'use_application_default_env' => false,
    'scopes' => [
//         Google_Service_DataTransfer::ADMIN_DATATRANSFER,
//         Google_Service_Directory::ADMIN_DIRECTORY_CUSTOMER,
//         Google_Service_Directory::ADMIN_DIRECTORY_DEVICE_CHROMEOS,
//         Google_Service_Directory::ADMIN_DIRECTORY_DEVICE_MOBILE,
//         Google_Service_Directory::ADMIN_DIRECTORY_DOMAIN,
//         Google_Service_Directory::ADMIN_DIRECTORY_GROUP,
//         Google_Service_Directory::ADMIN_DIRECTORY_ORGUNIT_READONLY,
//         Google_Service_Directory::ADMIN_DIRECTORY_RESOURCE_CALENDAR,
//         Google_Service_Directory::ADMIN_DIRECTORY_ROLEMANAGEMENT,
//         Google_Service_Directory::ADMIN_DIRECTORY_USER,
//         Google_Service_Directory::ADMIN_DIRECTORY_USER_SECURITY,
//         Google_Service_Directory::ADMIN_DIRECTORY_USERSCHEMA,
//         Google_Service_Reports::ADMIN_REPORTS_AUDIT_READONLY,
//         Google_Service_Reports::ADMIN_REPORTS_USAGE_READONLY,
//         Google_Service_Calendar::CALENDAR,
//         Google_Service_Classroom::CLASSROOM_COURSES,
//         Google_Service_Classroom::CLASSROOM_GUARDIANLINKS_STUDENTS,
//         Google_Service_Classroom::CLASSROOM_PROFILE_EMAILS,
//         Google_Service_Classroom::CLASSROOM_PROFILE_PHOTOS,
//         Google_Service_Classroom::CLASSROOM_ROSTERS,
//         Google_Service_Drive::DRIVE,
//         Google_Service_Gmail::MAIL_GOOGLE_COM,
// 	   Google_Service_Groupssettings::APPS_GROUPS_SETTINGS,
    ],
    'access_type' => 'offline',
    'approval_prompt' => 'auto',
    'env_file' => env($cred_prefix.'GOOGLE_CREDENTIALS_PATH'),
    'token_file' => env($cred_prefix.'GOOGLE_TOKEN_PATH'),

    /*
    |----------------------------------------------------------------------------
    | Google developer key
    |----------------------------------------------------------------------------
    |
    | Simple API access key, also from the API console. Ensure you get
    | a Server key, and not a Browser key.
    |
    */
    'developer_key' => '',

    /*
    |----------------------------------------------------------------------------
    | Google service account
    |----------------------------------------------------------------------------
    |
    | Set the credentials JSON's location to use assert credentials, otherwise
    | app engine or compute engine will be used.
    |
    */
    'service' => [
        /*
        | Enable service account auth or not.
        */
        'enable' => true,

        'username' => env($cred_prefix.'GOOGLE_SERVICE_ACCOUNT_NAME'),

        /*
        | Path to service account json file
        */
        'file' => env($cred_prefix.'GOOGLE_SERVICE_ACCOUNT_JSON'),
    ],
];
