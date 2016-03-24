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
        'from_url_comment' => 'Enter an URL to match. '
            . 'When match type is set to \'Placeholders\' you can use placeholders such as {category} or {id}.',
        'to_url' => 'Target URL',
        'to_url_comment' => 'Enter the URL to redirect to. '
            . 'Placeholders can be used if match type is set to \'Placeholders\'.',
        'match_type' => 'Match type',
        'status_code' => 'HTTP status code',
        'sort_order' => 'Sort order',
        'sort_order_comment' => 'Please enter a numeric value to prioritize redirects.',
        'requirements' => 'Requirements',
        'requirements_comment' => 'Provide requirements for each placeholder.',
        'placeholder' => 'Placeholder',
        'placeholder_comment' => 'The placeholder name (including curly braces) provided in the \'From URL\' field. '
            . 'E.g. {category} or {id}',
        'requirement' => 'Requirement',
        'requirement_comment' => 'Provide the requirement in regular expression syntax. E.g. [0-9]+ or [a-zA-Z].',
        'requirements_prompt' => 'Add new requirement',
        'replacement' => 'Replacement',
        'replacement_comment' => 'Provide an optional replacement value for this placeholder. '
            . 'The matched placeholder will be replaced with this value in the target URL.',
        'exact' => 'Exact',
        'placeholders' => 'Placeholders',
        'permanent' => '301 - Permanent',
        'temporary' => '302 - Temporary',
        'enabled' => 'Enabled',
        'enable_selected' => 'Enable selected',
        'disable_selected' => 'Disable selected',
        'enabled_comment' => 'Flick this switch to enable this redirect',
        'publish' => 'Publish',
        'publish_success' => ':number redirects are successfully published',
        'hits' => 'Hits',
        'reorder_redirects' => 'Reorder',
        'new_redirect' => 'New Redirect',
        'return_to_redirects' => 'Return to redirects list',
        'hide_disabled' => 'Hide Disabled',
        'delete_confirm' => 'Are you sure?',
        'manage_redirects' => 'Manage Redirects',
    ],
];
