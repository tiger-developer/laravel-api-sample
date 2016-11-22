<?php
return [
    'email'                   => 'email|max:128',
    'occupation_code'         => 'in:1,2,3,6,9|size:1',
    'register_token'          => 'alpha_num|size:64',
    'password'                => 'password',
    'given_name'              => 'name',
    'family_name'             => 'name',
    'given_name_kana'         => 'kana',
    'family_name_kana'        => 'kana',
    'gender'                  => 'in:0,1,2,9|size:1',
    'birthdate'               => 'date_format:Y-m-d',
    'country'                 => 'regex:/^[A-Z]+$/|size:3',
    'region'                  => 'integer|min:1|max:47',
    'graduation_year'         => 'digits:4',
    'authentication_email'    => 'regex:/^[ -\~]+$/|max:128',
    'authentication_password' => 'regex:/^[!-~]+$/|between:4,32',
    'is_login_auto'           => 'boolean',
    'sub'                     => 'integer|between:1,4294967294',
    'email_verify_token'      => 'alpha_num|size:64',
];