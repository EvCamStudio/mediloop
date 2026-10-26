<?php

return [

    'page_title' => 'Facility Network Map',
    'title' => 'Facility Network & Distance Map',
    'subtitle' => 'Partner health facility distribution and air distance from your facility, ordered from nearest.',
    'not_linked' => 'Facility not linked yet',
    'manage_stock' => 'Manage all stock',

    'stats' => [
        'in_range' => 'Facilities in range',
        'in_range_sub' => 'Within a :radius km radius',
        'nearest' => 'Nearest facility',
        'nearest_sub' => 'Air distance (Haversine)',
        'registered' => 'Registered facilities',
        'registered_sub' => 'After active filters',
    ],

    'filters' => [
        'aria' => 'Facility network filters',
        'type_label' => 'Facility type',
        'all_types' => 'All types',
        'cold_chain' => 'Cold-chain ready facilities only',
        'apply' => 'Apply filters',
    ],

    'map' => [
        'title' => 'Facility Distribution Map',
        'subtitle_origin' => 'Your facility is the center of the distance calculation. The dashed circle marks a :radius km radius.',
        'subtitle_no_origin' => 'This account is not linked to a facility, so distance and radius are not calculated.',
        'aria' => 'Health facility map. The nearest facility list is available in the panel beside it.',
        'loading' => 'Loading interactive map…',
        'noscript' => 'The interactive map requires JavaScript. All distance and facility information remains available in the list below.',
        'source' => 'Distances are calculated with the Haversine formula (air distance), not road travel distance. Facility coordinates are simulated data for demonstration.',
    ],

    'legend' => [
        'origin' => 'Your facility',
        'radius' => 'Radius :radius km',
    ],

    'list' => [
        'title' => 'Nearest to Your Facility',
        'found' => ':count facility found. Select one to highlight its location on the map.|:count facilities found. Select one to highlight its location on the map.',
        'cold_chain' => 'Cold-chain ready',
        'proximity' => 'Distance score :score%',
        'distance_missing' => 'Distance unavailable',
        'empty' => 'No other facilities match the active filters.',
        'show_all' => 'Show all facilities',
    ],

];
