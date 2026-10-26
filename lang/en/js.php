<?php

return [

    'validation' => [
        'required' => 'This field is required.',
        'email' => 'Enter a valid email address, for example: name@facility.id.',
        'number' => 'Enter a valid number.',
        'date' => 'Enter a valid date.',
        'min' => 'The value is too small.',
        'max' => 'The value is too large.',
        'step' => 'The value does not match the allowed increment.',
        'minlength' => 'The entry is too short.',
        'maxlength' => 'The entry is too long.',
        'pattern' => 'The entry format is not valid.',
        'invalid' => 'The entry is not valid yet.',
        'min_value' => 'The minimum value is :min.',
        'max_value' => 'The maximum value is :max.',
        'min_characters' => 'At least :min characters.',
        'max_characters' => 'At most :max characters.',
        'summary_title' => 'Check the following fields:',
    ],

    'password' => [
        'show' => 'Show password',
        'hide' => 'Hide password',
        'show_confirmation' => 'Show password confirmation',
        'hide_confirmation' => 'Hide password confirmation',
    ],

    'batch_form' => [
        'days_to_expiry' => '<small>days</small>',
        'days_to_deplete' => '<small>days</small>',
        'never_depletes' => 'Does not deplete <small>0/day</small>',
        'unit_ratio' => '<small>units (:ratio%)</small>',
        'risk_critical' => 'Critical',
        'risk_critical_note' => 'Remaining shelf life is 30 days or less; prioritise redistribution.',
        'risk_high' => 'High',
        'risk_high_note' => 'High risk; review redistribution needs.',
        'risk_medium' => 'Medium',
        'risk_medium_note' => 'Monitor consumption rate and remaining shelf life.',
        'risk_low' => 'Safe',
        'risk_low_note' => 'Stock is projected to run out before expiry.',
    ],

    'network_map' => [
        'distance_unavailable' => 'Distance unavailable',
        'distance_from_facility' => ':distance km from your facility',
        'origin' => 'Origin facility for distance calculation',
        'cold_chain_ready' => 'Cold chain ready',
        'zoom_hint' => 'Ctrl + scroll to zoom',
        'show_radius' => 'Show :radius km radius',
        'invalid_data' => 'Map data could not be loaded. Use the facility list below.',
    ],

];
