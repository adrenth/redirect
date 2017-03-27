<?php

return [
    'plugin' => [
        'name' => 'Ompekning',
        'description' => 'Hantera ompekningar',
    ],
    'permission' => [
        'access_redirects' => [
            'label' => 'Hantera ompekningar',
            'tab' => 'Ompekningar',
        ],
    ],
    'navigation' => [
        'menu_label' => 'Ompekningar',
        'menu_description' => 'Hantera ompekningar',
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
        'redirect' => 'Ompekning',
        'from_url' => 'Anropsadress',
        'from_url_placeholder' => '/source/path',
        'from_url_comment' => 'Anropsadressen att matcha',
        'from_scheme' => 'Source scheme', // TODO
        'from_scheme_comment' => 'Force match on scheme. If HTTP is selected <u>http://domain.com/path</u> will '
            . 'match and <u>https://domain.com/path</u> does not match.', // TODO
        'to_url' => 'Måladress',
        'to_url_placeholder' => '/absolute/path, relative/path eller http://target.url',
        'to_url_comment' => 'Måladressen eller url:en att ompeka till',
        'to_url_required_if' => 'Måladressen är obligatorisk',
        'to_scheme' => 'Target scheme', // TODO
        'to_scheme_comment' => 'Target scheme will be forced to HTTP or HTTPS '
            . 'or choose AUTOMATIC to use the default scheme of the website.', // TODO
        'scheme_auto' => 'Automatic', // TODO
        'input_path_placeholder' => '/input/path', // TODO
        'cms_page_required_if' => 'Ange en CMS-adress att ompeka till',
        'static_page_required_if' => 'Ange en Sidor-sida att ompeka till',
        'match_type' => 'Matcha typ',
        'exact' => 'Exakt',
        'placeholders' => 'Platshållare',
        'target_type' => 'Måltyp',
        'target_type_none' => 'Not applicable', // TODO
        'target_type_path_or_url' => 'Mål eller url',
        'target_type_cms_page' => 'CMS-sida',
        'target_type_static_page' => 'Sidor-sida',
        'status_code' => 'HTTP-statuskod',
        'sort_order' => 'Sorteringsordning',
        'requirements' => 'Krav',
        'requirements_comment' => 'Ange krav för varje platshållare',
        'placeholder' => 'Platshållare',
        'placeholder_comment' => 'Platshållarens namn (inklusive måsvingar) som hittas i anropsadressfältet. Ex. {category} eller {id}',
        'requirement' => 'Krav',
        'requirement_comment' => 'Ange kraven i regular expression-syntax. Ex. [0-9]+ eller [a-zA-Z]+.',
        'requirements_prompt' => 'Lägg till krav',
        'replacement' => 'Ersättning',
        'replacement_comment' => 'Ange en valfritt ersättningsvärde för denna platshållare. Den matchade platshållaren kommer att ersättas av detta värde i måladressen',
        'permanent' => '301 - Permanent',
        'temporary' => '302 - Tillfällig',
        'see_other' => '303 - See Other', // TODO
        'not_found' => '404 - Ofunnen',
        'gone' => '410 - Gone', // TODO
        'enabled' => 'Aktiv',
        'none' => 'none', // TODO
        'enabled_comment' => 'Slå på för att aktivera ompekningen',
        'priority' => 'Prioritet',
        'hits' => 'Träffar',
        'return_to_redirects' => 'Återvänd till ompekningar',
        'return_to_categories' => 'Återvänd till kategorier',
        'delete_confirm' => 'Är du säker?',
        'created_at' => 'Skapad',
        'modified_at' => 'Ändrad',
        'system_tip' => 'Systemskapad ompekning',
        'user_tip' => 'Andvändargenererad ompekning',
        'type' => 'Typ',
        'last_used_at' => 'Användes senast',
        'and_delete_log_item' => 'Och radera valda log-filer',
        'category' => 'Kategori',
        'categories' => 'Kategorier',
        'name' => 'Namn',
        'date_time' => 'Date & Time', // TODO
        'date' => 'Date', // TODO
        'truncate_confirm' => 'Are you sure you want to delete ALL records?', // TODO
        'truncating' => 'Deleting...', // TODO

    ],
    'list' => [
        'no_records' => 'Det finns inga ompekningar i denna vy',
        'switch_is_enabled' => 'Aktiva',
        'switch_system' => 'Systemompekningar',
    ],
    'scheduling' => [
        'from_date' => 'Aktiv från',
        'from_date_comment' => 'Det datum då denna ompekning blir aktiv. Kan lämnas tomt',
        'to_date' => 'Aktiv tills',
        'to_date_comment' => 'Det datum då demma ompekning blir inaktiv. Kan lämas tomt',
        'scheduling_comment' => 'Här kan du ange ett tidsspann då denna ompekning ska vara aktiv. Alla typer av datumkombinationer är möjliga',
    ],
    'test' => [
        'test_comment' => 'Var god testa din ompekning innan du sparar den',
        'input_path' => 'Ange sökväg',
        'input_path_comment' => 'Sökvägen du vill testa. Ex. /old-blog/category/123',
        'input_path_placeholder' => '/input/path',
        'test_date' => 'Testdatum',
        'test_date_comment' => 'Om du har schemalagt denna ompekning kan du testa det här',
        'testing' => 'Testar...',
        'run_test' => 'Kör test',
        'no_match_label' => 'Sorry, ingen matchning!',
        'no_match' => 'Ingen matchning hittades!',
        'match_success_label' => 'Vi har en matchning!',
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
        'import' => 'Importera',
        'export' => 'Exportera',
        'redirects' => 'Hantera ompekningar',
        'create_redirect' => 'Skapa ompekning',
        'edit_redirect' => 'Ändra ompekning',
        'categories' => 'Hantera kategorier',
        'create_category' => 'Skapa kategori',
        'edit_category' => 'Ändra kategori',
        'view_redirect_log' => 'View redirect log', // TODO
        'statistics' => 'Statistics', // TODO
        'test_lab' => 'TestLab (beta)', // TODO
    ],
    'buttons' => [
        'add' => 'Skapa',
        'from_request_log' => 'Från anropslogg',
        'new_redirect' => 'Ny ompekning',
        'create_redirects' => 'Skapa ompekning',
        'delete' => 'Radera',
        'enable' => 'Aktivera',
        'disable' => 'Inaktivera',
        'reorder_redirects' => 'Omsortera',
        'export' => 'Exportera',
        'import' => 'Importera',
        'categories' => 'Kategorier',
        'new_category' => 'Ny kategori',
        'reset_statistics' => 'Återställ statistik',
        'logs' => 'Redirect log', // TODO
        'empty_redirect_log' => 'Empty redirect log', // TODO
    ],
    'tab' => [
        'tab_general' => 'Allmänt',
        'tab_requirements' => 'Krav',
        'tab_test' => 'Test',
        'tab_scheduling' => 'Schemalägg',
        'tab_test_lab' => 'TestLab', // TODO
        'tab_advanced' => 'Advanced', // TODO
    ],
    'flash' => [
        'success_created_redirects' => 'Skapade :count ompekningar',
        'static_page_redirect_not_supported' => 'This redirect cannot be modified. Plugin RainLab.Pages is required.', // TODO
        'truncate_success' => 'Successfully deleted all records', // TODO
        'delete_selected_success' => 'Successfully deleted selected records', // TODO
    ],
];
