<?php

return [

    'accept' => [
        'page_title' => 'Accept Invitation',
        'eyebrow' => 'Team invitation',
        'heading' => 'Join :facility',
        'invited_as' => 'You have been invited as',
        'invited_description' => 'Activate your account to start managing stock and coordinating facility redistribution.',
        'facility_fallback' => 'Health facility',
        'invited_by' => 'Invited by',
        'expires_at' => 'Valid until',
        'single_use' => 'This invitation can only be used once. If the link has expired, request a new invitation from the facility head.',
        'activate_title' => 'Activate account',
        'activate_description' => 'Enter your full name and create a password to complete registration.',
        'full_name' => 'Full name',
        'name_required' => 'Full name is required.',
        'password_required' => 'Password is required.',
        'password_minlength' => 'Password must be at least 8 characters.',
        'password_help' => 'Use a strong password that you do not use on other services.',
        'password_confirmation' => 'Repeat password',
        'password_confirmation_required' => 'Password confirmation is required.',
        'activate_button' => 'Activate account',
        'footnote' => 'Once your account is active, you can sign in using :email.',
        'welcome' => 'Welcome to MediLoop, :name.',
    ],

    'invalid' => [
        'page_title' => 'Invalid Invitation',
        'heading' => 'Invitation cannot be used',
        'back_to_login' => 'Go to sign-in page',
    ],

    'errors' => [
        'email_taken' => 'This email already has a MediLoop account.',
        'facility_inactive' => 'The facility must be active before inviting team members.',
        'not_found' => 'Invitation not found.',
        'already_used' => 'This invitation has already been used.',
        'expired' => 'This invitation has expired. Request a new invitation from the facility head.',
        'facility_inactive_token' => 'This facility is currently inactive. Contact the platform administrator.',
    ],

    'validation' => [
        'name_required' => 'Full name is required.',
        'password_confirmation_mismatch' => 'The password confirmation does not match.',
    ],

    'audit' => [
        'invited' => 'Invitation sent to :email for :facility.',
        'joined' => ':name joined :facility.',
        'revoked' => 'Invitation for :email revoked.',
    ],

];
