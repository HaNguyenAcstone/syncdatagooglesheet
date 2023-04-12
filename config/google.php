<?php  

/*

|----------------------------------------------------------------------------
| Google OAuth 2.0 access
|----------------------------------------------------------------------------
|
| Keys for OAuth 2.0 access, see the API console at
| https://developers.google.com/console
|
*/
return [
    'client_id' => env('GOOGLE_CLIENT_ID', ''),
    'client_secret' => env('GOOGLE_CLIENT_SECRET', ''),
    'redirect_uri' => env('GOOGLE_REDIRECT', ''),
    'scopes' => [\Google\Service\Sheets::DRIVE, \Google\Service\Sheets::SPREADSHEETS],
    'access_type' => 'online',
    'approval_prompt' => 'auto',    
];