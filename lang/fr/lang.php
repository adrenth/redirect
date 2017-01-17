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
    'redirect' => [
        'redirect' => 'Redirect',
        'from_url' => 'Chemin source',
        'from_url_placeholder' => '/source/path',
        'from_url_comment' => 'Le chemin source à trouver',
        'to_url' => 'Chemin cible ou URL',
        'to_url_placeholder' => '/chemin/cible or http://cible.url',
        'to_url_comment' => 'Le chemin cible ou l\'url vers laquelle rediriger.',
        'to_url_required_if' => 'Le chemin cible ou l\'url est requis',
        'cms_page_required_if' => 'Veuillez spécifier une CMS Page de destination',
        'static_page_required_if' => 'Veuillez spécifier une Static Page de destination',
        'match_type' => 'Type de correspondance',
        'exact' => 'Exacte',
        'placeholders' => 'Placeholders',
        'target_type' => 'Type de cible',
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
    ],
    'flash' => [
        'success_created_redirects' => 'Successfully created :count redirects', // TODO
        'static_page_redirect_not_supported' => 'This redirect cannot be modified. Plugin RainLab.Pages is required.', // TODO
        'truncate_success' => 'Successfully deleted all records', // TODO
        'delete_selected_success' => 'Successfully deleted selected records', // TODO
    ],
];
