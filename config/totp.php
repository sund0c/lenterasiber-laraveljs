<?php

return [
    'issuer'  => env('TOTP_ISSUER', 'Lentera Siber Admin'),
    'digits'  => 6,
    'period'  => 30,
    'algo'    => 'SHA1',
    'window'  => 1,   // ±1 period clock drift
];
