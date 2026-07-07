<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Zero Trust Architecture Configuration
    |--------------------------------------------------------------------------
    |
    | Configure the Zero Trust settings, such as checking for IP reputation,
    | enforcing MFA based on context, and blocking tor exit nodes.
    |
    */
    'zero_trust' => [
        'enabled' => env('CYBERSHIELD_ZERO_TRUST_ENABLED', true),
        'check_ip_reputation' => env('CYBERSHIELD_IP_REP_ENABLED', false),
        'block_tor' => env('CYBERSHIELD_BLOCK_TOR', false),
        'require_mfa_for_new_device' => env('CYBERSHIELD_NEW_DEVICE_MFA', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Threat Intelligence Integration
    |--------------------------------------------------------------------------
    |
    | API keys for threat intelligence providers.
    |
    */
    'threat_intel' => [
        'abuseipdb_api_key' => env('ABUSEIPDB_API_KEY', null),
        'virustotal_api_key' => env('VIRUSTOTAL_API_KEY', null),
    ],

    /*
    |--------------------------------------------------------------------------
    | Password Policy & Manager Configuration
    |--------------------------------------------------------------------------
    |
    | Integration with HaveIBeenPwned and strict password rules.
    |
    */
    'passwords' => [
        'check_breaches' => env('CYBERSHIELD_CHECK_BREACHED_PASSWORDS', true),
        'encryption_key' => env('CYBERSHIELD_ENCRYPTION_KEY', env('APP_KEY')),
    ],

    /*
    |--------------------------------------------------------------------------
    | SIEM & Logging Integration
    |--------------------------------------------------------------------------
    |
    | Settings for exporting security events to a SIEM.
    |
    */
    'siem' => [
        'enabled' => env('CYBERSHIELD_SIEM_ENABLED', false),
        'endpoint' => env('CYBERSHIELD_SIEM_ENDPOINT', null),
        'format' => 'json', // json, splunk_hec, etc.
    ],

    /*
    |--------------------------------------------------------------------------
    | Endpoint Security (WAF)
    |--------------------------------------------------------------------------
    |
    | Web Application Firewall settings to block common injection attacks.
    |
    */
    'waf' => [
        'enabled' => env('CYBERSHIELD_WAF_ENABLED', true),
        'block_sqli' => true,
        'block_xss' => true,
        'max_payload_size' => 1024 * 1024 * 2, // 2MB
    ],
];
