<?php

return [
    'plugin' => [
        'name' => 'Redirect',
        'description' => 'Administra facilmente las redirecciones',
    ],
    'permission' => [
        'access_redirects' => [
            'label' => 'Redirecciones',
            'tab' => 'Redirecciones',
        ],
    ],
    'navigation' => [
        'menu_label' => 'Redirecciones',
        'menu_description' => 'Administra las redirecciones',
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
        'redirect' => 'Redirección',
        'from_url' => 'Ruta de origen',
        'from_url_placeholder' => '/tu/ruta',
        'from_url_comment' => 'Ruta de origen a coincidir.',
        'from_scheme' => 'Source scheme', // TODO
        'from_scheme_comment' => 'Force match on scheme. If HTTP is selected <u>http://domain.com/path</u> will '
            . 'match and <u>https://domain.com/path</u> does not match.', // TODO
        'to_url' => 'Ruta destino o URL',
        'to_url_placeholder' => '/ruta/absoluta, ruta/relativa o http://destino.url', // changed since 2.0.6
        'to_url_comment' => 'Ruta destino o URL a la cual deseas redirigir.',
        'to_url_required_if' => 'El destino o la URL son requeridos',
        'to_scheme' => 'Target scheme', // TODO
        'to_scheme_comment' => 'Target scheme will be forced to HTTP or HTTPS '
            . 'or choose AUTOMATIC to use the default scheme of the website.', // TODO
        'scheme_auto' => 'Automatic', // TODO
        'input_path_placeholder' => '/input/path',
        'cms_page_required_if' => 'Por favor ingresa una página del CMS a la cual redirigir',
        'static_page_required_if' => 'Por favor ingresa una página estática a la cual deseas redirigir',
        'match_type' => 'Coincidir Tipo',
        'exact' => 'Exacto',
        'placeholders' => 'Marcadores',
        'target_type' => 'Tipo de destino',
        'target_type_none' => 'Not applicable', // TODO
        'target_type_path_or_url' => 'Ruta o URL',
        'target_type_cms_page' => 'Página CMS',
        'target_type_static_page' => 'Página estática',
        'status_code' => 'Código de estado HTTP',
        'sort_order' => 'Orden de clasificación',
        'requirements' => 'Requerimientos',
        'requirements_comment' => 'Proporciona los requerimientos para cada marcador.',
        'placeholder' => 'Marcador',
        'placeholder_comment' => 'El marcador (incluyendo las llaves) proporcionadas en el campo \'ruta de origen\'. '
            . 'Por ejemplo {category} o {id}',
        'requirement' => 'Requerimiento',
        'requirement_comment' => 'Proporciona el requerimiento con una expresión regular. Ej. [0-9]+ o [a-zA-Z]+.',
        'requirements_prompt' => 'Agregar nuevo requerimiento',
        'replacement' => 'Reemplazo',
        'replacement_comment' => 'Proporciona un valor de reemplazo para este marcador. '
            . 'El marcador coincidente será reemplazado con el valor de la URL destino.',
        'permanent' => '301 - Permanente',
        'temporary' => '302 - Temporal',
        'see_other' => '303 - Ver otro',
        'not_found' => '404 - No encontrado',
        'gone' => '410 - Ya no existe',
        'enabled' => 'Activado',
        'none' => 'none', // TODO
        'enabled_comment' => 'Activa este switch para activar esta redirección.',
        'priority' => 'Prioridad',
        'hits' => 'Visitas',
        'return_to_redirects' => 'Regresar al listado de redirecciones',
        'return_to_categories' => 'Regresar al listado de categorías',
        'delete_confirm' => '¿Estás seguro?',
        'created_at' => 'Creado el',
        'modified_at' => 'Modificado el',
        'system_tip' => 'Redirección generada por el sistema',
        'user_tip' => 'Redirección generada por el usuario',
        'type' => 'Tipo',
        'last_used_at' => 'Usada última vez',
        'and_delete_log_item' => 'y borra los elementos seleccionados', // since 2.0.3,
        'category' => 'Categoría',
        'categories' => 'Categorías',
        'name' => 'Nombre',
        'date_time' => 'Fecha y Hora',
        'date' => 'Fecha',
        'truncate_confirm' => '¿Estás seguro que deseas borrar todos los registros?',
        'truncating' => 'Borrando...',
    ],
    'list' => [
        'no_records' => 'No hay redirecciones en esta vista.',
        'switch_is_enabled' => 'Activado',
        'switch_system' => 'Redirecciones del sistema',
    ],
    'scheduling' => [
        'from_date' => 'Disponible desde',
        'from_date_comment' => 'La fecha en que esta redirección se activará puede ser omitida.',
        'to_date' => 'Disponible hasta',
        'to_date_comment' => 'La fecha limite de esta redirección, puede ser omitida.',
        'scheduling_comment' => 'Aquí puedes establecer el periodo que durara la redirección. '
            . 'Todo tipo de combinaciones de fechas es posible.',
    ],
    'test' => [
        'test_comment' => 'Por favor prueba tu redirección antes de guardar la ruta.',
        'input_path' => 'Ruta de entrada',
        'input_path_comment' => 'Ruta de entrada a probar Ej. /old-blog/category/123',
        'input_path_placeholder' => '/ruta/de/entrada',
        'test_date' => 'Fecha de prueba',
        'test_date_comment' => 'Si tu calendarizas esta redirección, tu puedes probar esta redirección en una fecha concreta.',
        'testing' => 'Probando...',
        'run_test' => 'Ejecutar prueba',
        'no_match_label' => 'Lo siento, no hay coincidencia!',
        'no_match' => '¡No se encontraron coincidencias!',
        'match_success_label' => 'Se encontró una coincidencia!',
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
        'import' => 'Importar',
        'export' => 'Exportar',
        'redirects' => 'Administrar las redirecciones',
        'create_redirect' => 'Crear una redirección',
        'edit_redirect' => 'Editar una redirección',
        'categories' => 'Administrar las categorías',
        'create_category' => 'Crear una categoría',
        'edit_category' => 'Modificar una categoría',
        'view_redirect_log' => 'Ver log de redirecciones',
        'statistics' => 'Estadísticas',
        'test_lab' => 'TestLab (beta)', // TODO
    ],
    'buttons' => [
        'add' => 'Agregar', // since 2.0.3
        'from_request_log' => 'Del log de peticiones', // since 2.0.3
        'new_redirect' => 'Nueva redirección', // changed since 2.0.3
        'create_redirects' => 'Crear redirecciones', // since 2.0.3
        'delete' => 'Borrar',
        'enable' => 'Activar',
        'disable' => 'Desactivar',
        'reorder_redirects' => 'Reordenar',
        'export' => 'Exportar',
        'import' => 'Importar',
        'categories' => 'Categorías',
        'new_category' => 'Nueva categoría',
        'reset_statistics' => 'Limpiar estadísticas',
        'logs' => 'Log de redirecciones',
        'empty_redirect_log' => 'Vaciar log de redirecciones',
    ],
    'tab' => [
        'tab_general' => 'General',
        'tab_requirements' => 'Requerimientos',
        'tab_test' => 'Probar',
        'tab_scheduling' => 'Calendarizar',
        'tab_test_lab' => 'TestLab', // TODO
        'tab_advanced' => 'Advanced', // TODO
    ],
    'flash' => [
        'success_created_redirects' => 'Se crearon con éxito :count redirecciones', // since 2.0.3
        'static_page_redirect_not_supported' => 'Esta redirección no puede ser modificada. El Plugin RainLab.Pages es requerido.',
        'truncate_success' => 'Todos los registros han sido borrados con éxito',
        'delete_selected_success' => 'Los registros seleccionados fueron borrados con éxito',
    ],
];
