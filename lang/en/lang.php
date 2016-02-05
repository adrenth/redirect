<?php

return [
    'plugin' => [
        'name' => 'Redirect',
        'description' => 'Easily manage redirects',
    ],
    'permission' => [
        'access_redirects' => [
            'label' => 'Redirects',
            'tab' => 'Redirects',
        ],
    ],
    'navigation' => [
        'menu_label' => 'Redirects',
        'menu_description' => 'Manage redirects',
    ],
    'redirect' => [
        'from_url' => 'From URL',
        'from_url_comment' => 'E.g. http://www.domain.com/blog/(.+)',
        'to_url' => 'To URL',
        'to_url_comment' => 'E.g. http://www.domain.com/food-blog/$1',
        'match_type' => 'Match type',
        'status_code' => 'HTTP status code',
        'sort_order' => 'Sort order',
        'sort_order_comment' => 'Please enter a numeric value to prioritize redirects',
        'exact' => 'Exact',
        'starts_with' => 'Starts with',
        'ends_with' => 'Ends with',
        'regex' => 'Regex',
        'permanent' => 'Permanent',
        'temporary' => 'Temporary',
        'enabled' => 'Enabled',
        'enabled_comment' => 'Flick this switch to enable this redirect',
        'hits' => 'Hits',
        'reorder_redirects' => 'Reorder',
        'new_redirect' => 'New Redirect'
    ],
];
