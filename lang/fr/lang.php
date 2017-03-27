<?php

return [
    'plugin' => [
        'name' => 'Redirect',
        'description' => 'Gestion facile des redirections',
    ],
    'permission' => [
        'access_redirects' => [
            'label' => 'Redirects',
            'tab' => 'Redirects',
        ],
    ],
    'navigation' => [
        'menu_label' => 'Redirects',
        'menu_description' => 'Gestion des redirections',
    ],
    'settings' => [
        'menu_label' => 'Redirects', // TODO
        'menu_description' => 'Manage settings for Redirects.', // TODO
        'logging_enabled_label' => 'Log redirect events', // TODO
        'logging_enabled_comment' => 'Store redirect events in the database.', // TODO
        'statistics_enabled_label' => 'Gather statistics', // TODO
        'statistics_enabled_comment' => 'Gather statistics of redirected requests to get more insight.', // TODO
        'test_lab_enabled_label' => 'TestLab (beta)', // TODO
        'test_lab_enabled_comment' => 'TestLab allows you to mass test your redirects.', // TODO
    ],
    'redirect' => [
        'redirect' => 'Redirect',
        'from_url' => 'Chemin source',
        'from_url_placeholder' => '/source/path',
        'from_url_comment' => 'Le chemin source à trouver',
        'from_scheme' => 'Source scheme', // TODO
        'from_scheme_comment' => 'Force match on scheme. If HTTP is selected <u>http://domain.com/path</u> will '
            . 'match and <u>https://domain.com/path</u> does not match.', // TODO
        'to_url' => 'Chemin cible ou URL',
        'to_url_placeholder' => '/chemin/cible or http://cible.url',
        'to_url_comment' => 'Le chemin cible ou l\'url vers laquelle rediriger.',
        'to_url_required_if' => 'Le chemin cible ou l\'url est requis',
        'to_scheme' => 'Target scheme', // TODO
        'to_scheme_comment' => 'Target scheme will be forced to HTTP or HTTPS '
            . 'or choose AUTOMATIC to use the default scheme of the website.', // TODO
        'scheme_auto' => 'Automatic', // TODO
        'input_path_placeholder' => '/input/path', // TODO
        'cms_page_required_if' => 'Veuillez spécifier une CMS Page de destination',
        'static_page_required_if' => 'Veuillez spécifier une Static Page de destination',
        'match_type' => 'Type de correspondance',
        'exact' => 'Exacte',
        'placeholders' => 'Placeholders',
        'target_type' => 'Type de cible',
        'target_type_none' => 'Not applicable', // TODO
        'target_type_path_or_url' => 'Chemin ou URL',
        'target_type_cms_page' => 'CMS Page',
        'target_type_static_page' => 'Static Page',
        'status_code' => 'Code HTTP',
        'sort_order' => 'Ordre de tri',
        'requirements' => 'Conditions',
        'requirements_comment' => 'Spécifier une condition pour chaque placeholder.',
        'placeholder' => 'Placeholder',
        'placeholder_comment' => 'Le nom du placeholder (en includant les accolades) '
            . 'renseigné dans le champ \'Chemin source\'. Ex. {category} ou {id}',
        'requirement' => 'Condition',
        'requirement_comment' => 'Défini la condition avec la syntaxe des expressions régulières. '
            . 'Ex. [0-9]+ ou [a-zA-Z]+.',
        'requirements_prompt' => 'Ajouter une nouvelle condition',
        'replacement' => 'Remplacement',
        'replacement_comment' => 'Fournit une valeur de remplacement optionnel pour ce placeholder. '
            . 'Le placeholder correspondant sera remplacé par cette valeur dans l\'URL cible',
        'permanent' => '301 - Permanente',
        'temporary' => '302 - Temporaire',
        'see_other' => '303 - See Other', // TODO
        'not_found' => '404 - Page non trouvée',
        'gone' => '410 - Gone', // TODO
        'enabled' => 'Activée',
        'none' => 'none', // TODO
        'enabled_comment' => 'Actionnez ce switch pour activer la redirection.',
        'priority' => 'Priorité',
        'hits' => 'Hits',
        'return_to_redirects' => 'Retour à la liste des redirections',
        'return_to_categories' => 'Return to categories list', // TODO
        'delete_confirm' => 'Êtes-vous sûr?',
        'created_at' => 'Créé à',
        'modified_at' => 'Modifié à',
        'system_tip' => 'Redirection générée par le système',
        'user_tip' => 'Redirection générée par l\'utilisateur',
        'type' => 'Type',
        'last_used_at' => 'Dernière utilisation à',
        'and_delete_log_item' => 'And delete selected log items', // TODO
        'category' => 'Category', // TODO
        'categories' => 'Categories', // TODO
        'name' => 'Name', // TODO
        'date_time' => 'Date & Time', // TODO
        'date' => 'Date', // TODO
        'truncate_confirm' => 'Are you sure you want to delete ALL records?', // TODO
        'truncating' => 'Deleting...', // TODO
    ],
    'list' => [
        'no_records' => 'Il n\'y a pas de redirections dans cette vue.',
        'switch_is_enabled' => 'Activée',
        'switch_system' => 'Redirections du système',
    ],
    'scheduling' => [
        'from_date' => 'Disponible depuis',
        'from_date_comment' => 'La date à laquelle cette redirection sera disponible. Optionnel',
        'to_date' => 'Disponible jusqu\'à',
        'to_date_comment' => 'La date d\'expiration de cette redirection. Optionnel',
        'scheduling_comment' => 'Ici vous pouvez spécifier la période durant laquelle la redirection sera disponible. '
            . 'Toutes sortes de combinaisons de dates sont possible.',
    ],
    'test' => [
        'test_comment' => 'S\'il vous plaît, testez votre redirection avant de l\'enregistrer.',
        'input_path' => 'Chemin d\'entrée',
        'input_path_comment' => 'Le chemin d\'entrée à tester. Ex. /old-blog/category/123',
        'input_path_placeholder' => '/chemin/a/tester',
        'test_date' => 'Date de test',
        'test_date_comment' => 'Si vous avez planifiés cette redirections, '
            . 'vous pouvez la tester à une date spécifique.',
        'testing' => 'Test en cours...',
        'run_test' => 'Lancer le test',
        'no_match_label' => 'Désolé, aucune correspondance',
        'no_match' => 'Pas de correspondance trouvée',
        'match_success_label' => 'Nous avons une correspondance',
    ],
    'test_lab' => [
        'section_test_lab_comment' => 'TestLab allows you to mass test your redirects.', // TODO
        'test_lab_label' => 'Include in TestLab', // TODO
        'test_lab_enable' => 'Flick this switch to allow testing this redirect in the TestLab.', // TODO
        'test_lab_path_label' => 'Test Path', // TODO
        'test_lab_path_comment' => 'This path will be used when performing tests. '
            . 'Replace placeholders with real values.', // TODO
        'start_tests' => 'Start Tests', // TODO
        'start_tests_description' => 'Press the \'Start tests\' button to begin.', // TODO
        'edit' => 'Edit', // TODO
        'exclude' => 'Exclude', // TODO
        'exclude_confirm' => 'Are you sure want to exclude this redirect from TestLab?', // TODO
        'exclude_indicator' => 'Excluding redirect from TestLab', // TODO
        're_run' => 'Re-run', // TODO
        're_run_indicator' => 'Running tests, please wait...', // TODO
        'loop' => 'Loop', // TODO
        'match' => 'Match', // TODO
        'response_http_code' => 'Response HTTP code', // TODO
        'response_http_code_should_be' => 'Response HTTP code should be one of:', // TODO
        'redirect_count' => 'Redirect count', // TODO
        'final_destination' => 'Final Destination', // TODO
        'no_redirects' => 'No redirects have been marked with TestLab enabled. '
            . 'Please enable the option \'Include in TestLab\' when editing a redirect.', // TODO
        'test_error' => 'An error occurred when testing this redirect.', // TODO
        'flash_test_executed' => 'Test has been executed.', // TODO
        'flash_redirect_excluded' => 'Redirect has been excluded from TestLab and will not show up on next test run.', // TODO
        'result_request_failed' => 'Could not execute request.', // TODO
        'redirects_followed' => 'Number of redirects followed: :count (limited to :limit)', // TODO
        'not_determinate_destination_url' => 'Could not determine final destination URL.', // TODO
        'no_destination_url' => 'No final destination URL.', // TODO
        'final_destination_is' => 'Final destination is: :destination', // TODO
        'possible_loop' => 'Possible redirect loop!', // TODO
        'no_loop' => 'No redirect loop detected.', // TODO
        'not_match_redirect' => 'Did not match any redirect.', // TODO
        'matched' => 'Matched', // TODO
        'redirect' => 'redirect', // TODO
        'matched_not_http_code' => 'Matched redirect, but response HTTP code did not match! '
            . 'Expected :expected but received :received.', // TODO
        'matched_http_code' => 'Matched redirect, response HTTP code :code.', // TODO
    ],
    'statistics' => [
        'hits_per_day' => 'Redirect hits per day', // TODO
        'click_on_chart' => 'Click on the chart to enable zooming and dragging.', // TODO
        'requests_redirected' => 'Requests redirected', // TODO
        'all_time' => 'all time', // TODO
        'active_redirects' => 'Active redirects', // TODO
        'redirects_this_month' => 'Redirects this month', // TODO
        'previous_month' => 'previous month', // TODO
        'latest_redirected_requests' => 'Latest redirected request', // TODO
        'redirects_per_month' => 'Redirects per month', // TODO
        'no_data' => 'No data', // TODO
        'top_crawlers_this_month' => 'Top :top crawlers this month', // TODO
        'top_redirects_this_month' => 'Top :top redirects this month', // TODO
    ],
    'title' => [
        'import' => 'Import',
        'export' => 'Export',
        'redirects' => 'Gestion des redirections',
        'create_redirect' => 'Création de redirection',
        'edit_redirect' => 'Edition de redirection',
        'categories' => 'Manage categories', // TODO
        'create_category' => 'Create category', // TODO
        'edit_category' => 'Edit category', // TODO
        'view_redirect_log' => 'View redirect log', // TODO
        'statistics' => 'Statistics', // TODO
        'test_lab' => 'TestLab (beta)', // TODO
    ],
    'buttons' => [
        'add' => 'Add', // TODO
        'from_request_log' => 'From Request log', // TODO
        'new_redirect' => 'Ajouter',
        'create_redirects' => 'Create redirects', // TODO
        'delete' => 'Supprimer',
        'enable' => 'Activer',
        'disable' => 'Désactiver',
        'reorder_redirects' => 'Réordonner',
        'export' => 'Exporter',
        'import' => 'Importer',
        'categories' => 'Categories', // TODO
        'new_category' => 'New category', // TODO
        'reset_statistics' => 'Reset statistics', // TODO
        'logs' => 'Redirect log', // TODO
        'empty_redirect_log' => 'Empty redirect log', // TODO
    ],
    'tab' => [
        'tab_general' => 'Général',
        'tab_requirements' => 'Conditions',
        'tab_test' => 'Test',
        'tab_scheduling' => 'Planification',
        'tab_test_lab' => 'TestLab', // TODO
        'tab_advanced' => 'Advanced', // TODO
    ],
    'flash' => [
        'success_created_redirects' => 'Successfully created :count redirects', // TODO
        'static_page_redirect_not_supported' => 'This redirect cannot be modified. Plugin RainLab.Pages is required.', // TODO
        'truncate_success' => 'Successfully deleted all records', // TODO
        'delete_selected_success' => 'Successfully deleted selected records', // TODO
    ],
];
