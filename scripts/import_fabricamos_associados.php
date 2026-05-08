<?php

if ($argc < 3) {
    fwrite(STDERR, "Usage: php import_fabricamos_associados.php <wp-load.php> <json-file> [--match-dictionary] [--skip-wp-plugins] [--credentials-output=/path/to/file.csv] [--reset-existing-passwords] [--user-role=author]\n");
    exit(1);
}

$wpLoadPath = $argv[1];
$jsonPath = $argv[2];
$options = parse_cli_options(array_slice($argv, 3));

if (! file_exists($wpLoadPath)) {
    fwrite(STDERR, "wp-load.php not found: {$wpLoadPath}\n");
    exit(1);
}

if (! file_exists($jsonPath)) {
    fwrite(STDERR, "JSON file not found: {$jsonPath}\n");
    exit(1);
}

if (! empty($options['skip_wp_plugins']) && ! defined('WP_INSTALLING')) {
    define('WP_INSTALLING', true);
}

require $wpLoadPath;

if ($options['match_dictionary'] && ! class_exists('Fabricamos_Native')) {
    fwrite(STDERR, "Fabricamos_Native plugin is not loaded.\n");
    exit(1);
}

$raw = file_get_contents($jsonPath);
if ($raw === false) {
    fwrite(STDERR, "Unable to read JSON file.\n");
    exit(1);
}

$companies = json_decode($raw, true);
if (! is_array($companies)) {
    fwrite(STDERR, "Invalid JSON payload.\n");
    exit(1);
}

$fabricamos = $options['match_dictionary'] ? Fabricamos_Native::instance() : null;
$substanceIndex = $options['match_dictionary'] ? build_substance_index() : array();
$credentialsWriter = open_credentials_output($options['credentials_output']);

$created = 0;
$updated = 0;
$usersCreated = 0;
$usersUpdated = 0;
$passwordsGenerated = 0;
$matchedSubstances = 0;
$unmatchedSubstances = 0;
$rowsImported = 0;

foreach ($companies as $item) {
    $company = canonical_company_name(normalize_text((string) array_get($item, 'company', '')));
    if ($company === '') {
        continue;
    }

    $processes = normalize_text_list((array) array_get($item, 'processes', array()));
    $origins = normalize_text_list((array) array_get($item, 'origins', array()));
    $catalogItems = normalize_catalog_items((array) array_get($item, 'catalog_items', array()));
    $compiledSubstances = normalize_text_list((array) array_get($item, 'substances', array()));

    if (empty($compiledSubstances) && ! empty($catalogItems)) {
        $compiledSubstances = derive_compiled_substances_from_catalog_items($catalogItems);
    }

    $associateStatus = normalize_text((string) array_get($item, 'associate', 'Associado'));
    $responsibleName = normalize_text((string) array_get($item, 'responsible_name', ''));
    $responsiblePhone = normalize_text((string) array_get($item, 'responsible_phone', ''));
    $responsibleEmail = normalize_text((string) array_get($item, 'responsible_email', ''));
    $sourceWorkbook = normalize_text((string) array_get($item, 'source_workbook', ''));
    $sourceSheet = normalize_text((string) array_get($item, 'source_sheet', ''));
    $sourceUpdatedLabel = normalize_text((string) array_get($item, 'source_updated_label', ''));

    $editorAccount = ensure_editor_account(
        $responsibleName,
        $responsibleEmail,
        $responsiblePhone,
        $options['user_role'],
        $options['reset_existing_passwords']
    );

    if ($editorAccount['status'] === 'created') {
        $usersCreated++;
    } elseif ($editorAccount['status'] === 'updated' || $editorAccount['status'] === 'password_reset') {
        $usersUpdated++;
    }

    $manufacturerId = find_manufacturer_by_title($company);
    $isNew = false;

    if ($manufacturerId === 0) {
        $manufacturerId = wp_insert_post(array(
            'post_type' => 'fabricante',
            'post_status' => 'publish',
            'post_title' => $company,
            'post_content' => '',
            'post_author' => $editorAccount['user_id'] > 0 ? $editorAccount['user_id'] : 0,
        ), true);

        if (is_wp_error($manufacturerId)) {
            fwrite(STDERR, "Failed to create manufacturer {$company}: {$manufacturerId->get_error_message()}\n");
            continue;
        }

        $manufacturerId = (int) $manufacturerId;
        $created++;
        $isNew = true;
    } else {
        $currentAuthorId = (int) get_post_field('post_author', $manufacturerId);
        wp_update_post(array(
            'ID' => $manufacturerId,
            'post_title' => $company,
            'post_status' => 'publish',
            'post_author' => $editorAccount['user_id'] > 0 ? $editorAccount['user_id'] : $currentAuthorId,
        ));
        $updated++;
    }

    deactivate_duplicate_manufacturers($manufacturerId, $company);

    update_post_meta($manufacturerId, 'fab_associate_status', $associateStatus);
    update_post_meta($manufacturerId, 'fab_processo', implode(' / ', $processes));
    update_post_meta($manufacturerId, 'fab_origem', implode(' / ', $origins));
    update_post_meta($manufacturerId, 'fab_compiled_substances', array_values(array_unique($compiledSubstances)));
    update_post_meta($manufacturerId, 'fab_catalog_items', $catalogItems);
    sync_post_meta_text($manufacturerId, 'fab_responsavel_nome', $responsibleName);
    sync_post_meta_text($manufacturerId, 'fab_responsavel_telefone', $responsiblePhone);
    sync_post_meta_text($manufacturerId, 'fab_responsavel_email', $responsibleEmail);
    sync_post_meta_text($manufacturerId, 'fab_contact_name', '');
    sync_post_meta_text($manufacturerId, 'fab_phone', '');
    sync_post_meta_text($manufacturerId, 'fab_email', '');
    sync_post_meta_text($manufacturerId, 'fab_site', '');
    sync_post_meta_text($manufacturerId, 'fab_source_workbook', $sourceWorkbook);
    sync_post_meta_text($manufacturerId, 'fab_source_sheet', $sourceSheet);
    sync_post_meta_text($manufacturerId, 'fab_source_updated_label', $sourceUpdatedLabel);

    if ($editorAccount['user_id'] > 0) {
        update_post_meta($manufacturerId, 'fab_editor_user_id', $editorAccount['user_id']);
        sync_post_meta_text($manufacturerId, 'fab_editor_username', $editorAccount['username']);
        sync_post_meta_text($manufacturerId, 'fab_editor_email', $responsibleEmail);
    } else {
        delete_post_meta($manufacturerId, 'fab_editor_user_id');
        delete_post_meta($manufacturerId, 'fab_editor_username');
        delete_post_meta($manufacturerId, 'fab_editor_email');
    }

    $manufacturerLogin = ensure_manufacturer_login_credentials(
        $manufacturerId,
        $responsibleEmail,
        $editorAccount['generated_password'],
        $editorAccount['user_id'],
        $options['reset_existing_passwords']
    );

    if ($manufacturerLogin['generated_password'] !== '') {
        $passwordsGenerated++;
        write_credentials_row($credentialsWriter, array(
            'company' => $company,
            'responsible_name' => $responsibleName,
            'email' => $manufacturerLogin['email'],
            'username' => $editorAccount['username'],
            'password' => $manufacturerLogin['generated_password'],
            'status' => $manufacturerLogin['status'],
            'user_id' => (string) $editorAccount['user_id'],
        ));
    }

    $matchedIds = array();
    if ($options['match_dictionary']) {
        foreach ($compiledSubstances as $substanceName) {
            $matchedId = match_substance_post_id($substanceName, $substanceIndex, $fabricamos);
            if ($matchedId > 0) {
                $matchedIds[] = $matchedId;
                $matchedSubstances++;
            } else {
                $unmatchedSubstances++;
            }
        }
    }

    $matchedIds = array_values(array_unique(array_map('absint', $matchedIds)));
    if (function_exists('update_field')) {
        update_field('field_fab_substances', $matchedIds, $manufacturerId);
    } else {
        delete_post_meta($manufacturerId, 'fab_substances');
        update_post_meta($manufacturerId, 'fab_substances', $matchedIds);
    }

    if ($isNew) {
        echo "CREATED|{$manufacturerId}|{$company}|substances=" . count($compiledSubstances) . "|matched=" . count($matchedIds) . "|user=" . $editorAccount['status'] . PHP_EOL;
    } else {
        echo "UPDATED|{$manufacturerId}|{$company}|substances=" . count($compiledSubstances) . "|matched=" . count($matchedIds) . "|user=" . $editorAccount['status'] . PHP_EOL;
    }

    $rowsImported++;
}

close_credentials_output($credentialsWriter);

echo "SUMMARY|rows={$rowsImported}|created={$created}|updated={$updated}|users_created={$usersCreated}|users_updated={$usersUpdated}|passwords_generated={$passwordsGenerated}|matched_substances={$matchedSubstances}|unmatched_substances={$unmatchedSubstances}" . PHP_EOL;

function parse_cli_options($args)
{
    $options = array(
        'match_dictionary' => false,
        'skip_wp_plugins' => false,
        'credentials_output' => null,
        'reset_existing_passwords' => false,
        'user_role' => 'author',
    );

    foreach ($args as $arg) {
        if ($arg === '--match-dictionary') {
            $options['match_dictionary'] = true;
            continue;
        }

        if ($arg === '--skip-wp-plugins') {
            $options['skip_wp_plugins'] = true;
            continue;
        }

        if ($arg === '--reset-existing-passwords') {
            $options['reset_existing_passwords'] = true;
            continue;
        }

        if (starts_with($arg, '--credentials-output=')) {
            $options['credentials_output'] = substr($arg, strlen('--credentials-output='));
            continue;
        }

        if (starts_with($arg, '--user-role=')) {
            $options['user_role'] = substr($arg, strlen('--user-role='));
            continue;
        }
    }

    return $options;
}

function canonical_company_name($title)
{
    if (normalize_title_lookup($title) === 'cristalia produtos quimicos farmaceutico ltda.') {
        return 'CRISTÁLIA PRODUTOS QUÍMICOS FARMACEUTICOS Ltda.';
    }

    return $title;
}

function manufacturer_title_aliases($title)
{
    $canonical = canonical_company_name($title);
    $aliases = array($canonical);

    if ($canonical === 'CRISTÁLIA PRODUTOS QUÍMICOS FARMACEUTICOS Ltda.') {
        $aliases[] = 'CRISTÁLIA PRODUTOS QUÍMICOS FARMACĘUTICO Ltda.';
        $aliases[] = 'CRISTÁLIA PRODUTOS QUÍMICOS FARMACÊUTICO Ltda.';
    }

    return array_values(array_unique($aliases));
}

function normalize_mojibake_lookup($value)
{
    $value = (string) $value;

    $map = array(
        'Ã¡' => 'a',
        'Ãà' => 'a',
        'Ãâ' => 'a',
        'Ãã' => 'a',
        'Ãä' => 'a',
        'Ãå' => 'a',
        'ÃÁ' => 'a',
        'ÃÀ' => 'a',
        'ÃÂ' => 'a',
        'ÃÃ' => 'a',
        'ÃÄ' => 'a',
        'ÃÅ' => 'a',
        'á' => 'a',
        'à' => 'a',
        'â' => 'a',
        'ã' => 'a',
        'ä' => 'a',
        'å' => 'a',
        'Á' => 'a',
        'À' => 'a',
        'Â' => 'a',
        'Ã' => 'a',
        'Ä' => 'a',
        'Å' => 'a',
        'Ã©' => 'e',
        'Ã¨' => 'e',
        'Ãê' => 'e',
        'Ãë' => 'e',
        'ÃÉ' => 'e',
        'ÃÈ' => 'e',
        'ÃÊ' => 'e',
        'ÃË' => 'e',
        'é' => 'e',
        'è' => 'e',
        'ê' => 'e',
        'ë' => 'e',
        'É' => 'e',
        'È' => 'e',
        'Ê' => 'e',
        'Ë' => 'e',
        'Ä™' => 'e',
        'Ä˜' => 'e',
        'Ãí' => 'i',
        'Ãì' => 'i',
        'Ãî' => 'i',
        'Ãï' => 'i',
        'ÃÍ' => 'i',
        'ÃÌ' => 'i',
        'ÃÎ' => 'i',
        'ÃÏ' => 'i',
        'í' => 'i',
        'ì' => 'i',
        'î' => 'i',
        'ï' => 'i',
        'Í' => 'i',
        'Ì' => 'i',
        'Î' => 'i',
        'Ï' => 'i',
        'Ãó' => 'o',
        'Ãò' => 'o',
        'Ãô' => 'o',
        'Ãõ' => 'o',
        'Ãö' => 'o',
        'ÃÓ' => 'o',
        'ÃÒ' => 'o',
        'ÃÔ' => 'o',
        'ÃÕ' => 'o',
        'ÃÖ' => 'o',
        'ó' => 'o',
        'ò' => 'o',
        'ô' => 'o',
        'õ' => 'o',
        'ö' => 'o',
        'Ó' => 'o',
        'Ò' => 'o',
        'Ô' => 'o',
        'Õ' => 'o',
        'Ö' => 'o',
        'Ãú' => 'u',
        'Ãù' => 'u',
        'Ãû' => 'u',
        'Ãü' => 'u',
        'ÃÚ' => 'u',
        'ÃÙ' => 'u',
        'ÃÛ' => 'u',
        'ÃÜ' => 'u',
        'ú' => 'u',
        'ù' => 'u',
        'û' => 'u',
        'ü' => 'u',
        'Ú' => 'u',
        'Ù' => 'u',
        'Û' => 'u',
        'Ü' => 'u',
        'Ãç' => 'c',
        'ÃÇ' => 'c',
        'ç' => 'c',
        'Ç' => 'c',
        'Ãñ' => 'n',
        'ÃÑ' => 'n',
        'ñ' => 'n',
        'Ñ' => 'n',
        'Â' => '',
        'â€™' => '',
        'â€œ' => '',
        'â€' => '',
    );

    return strtr($value, $map);
}

function normalize_title_lookup($value)
{
    $value = normalize_mojibake_lookup((string) $value);
    $value = strtolower(remove_accents((string) $value));
    $value = preg_replace('/\s+/u', ' ', $value);
    return trim((string) $value);
}

function find_manufacturer_by_title($title)
{
    $titles = manufacturer_title_aliases($title);
    $normalizedCandidates = array();

    foreach ($titles as $candidateTitle) {
        $normalizedCandidates[] = normalize_title_lookup($candidateTitle);
    }

    $normalizedCandidates = array_values(array_unique(array_filter($normalizedCandidates)));

    $posts = get_posts(array(
        'post_type' => 'fabricante',
        'post_status' => array('publish', 'draft', 'pending', 'private'),
        'posts_per_page' => -1,
        'orderby' => 'ID',
        'order' => 'ASC',
        'suppress_filters' => false,
    ));

    $bestId = 0;
    $bestScore = -1;

    foreach ($posts as $post) {
        $normalizedTitle = normalize_title_lookup($post->post_title);
        if ($normalizedTitle === '' || ! in_array($normalizedTitle, $normalizedCandidates, true)) {
            continue;
        }

        $score = 0;
        if (in_array((string) $post->post_title, $titles, true)) {
            $score += 100;
        }
        if ((int) get_post_thumbnail_id($post->ID) > 0) {
            $score += 50;
        }
        if ($post->post_status === 'publish') {
            $score += 10;
        }

        if ($bestId === 0 || $score > $bestScore || ($score === $bestScore && (int) $post->ID < $bestId)) {
            $bestId = (int) $post->ID;
            $bestScore = $score;
        }
    }

    return $bestId;
}

function deactivate_duplicate_manufacturers($primaryId, $title)
{
    $normalizedPrimary = normalize_title_lookup($title);
    if ($normalizedPrimary === '') {
        return;
    }

    $posts = get_posts(array(
        'post_type' => 'fabricante',
        'post_status' => array('publish', 'draft', 'pending', 'private'),
        'posts_per_page' => -1,
        'orderby' => 'ID',
        'order' => 'ASC',
        'suppress_filters' => false,
    ));

    foreach ($posts as $post) {
        if ((int) $post->ID === (int) $primaryId) {
            continue;
        }

        if (normalize_title_lookup($post->post_title) === $normalizedPrimary) {
            wp_update_post(array(
                'ID' => (int) $post->ID,
                'post_status' => 'draft',
            ));
        }
    }
}

function ensure_manufacturer_login_credentials($manufacturerId, $loginEmail, $preferredPassword, $userId, $resetExistingPasswords)
{
    $manufacturerId = (int) $manufacturerId;
    $email = sanitize_email((string) $loginEmail);
    $preferredPassword = (string) $preferredPassword;
    $userId = (int) $userId;

    if ($manufacturerId <= 0 || $email === '' || ! is_email($email)) {
        return array(
            'email' => '',
            'generated_password' => '',
            'status' => 'none',
        );
    }

    $currentEmail = sanitize_email((string) get_post_meta($manufacturerId, 'fab_login_email', true));
    $currentHash = (string) get_post_meta($manufacturerId, 'fab_login_password_hash', true);
    $password = $preferredPassword;
    $status = 'existing';

    if ($password === '' && ($resetExistingPasswords || $currentHash === '' || $currentEmail === '' || strcasecmp($currentEmail, $email) !== 0)) {
        $password = wp_generate_password(18, true, true);
        $status = 'generated';
    } elseif ($password !== '') {
        $status = $resetExistingPasswords ? 'password_reset' : 'created';
    }

    update_post_meta($manufacturerId, 'fab_login_email', $email);

    if ($password !== '') {
        update_post_meta($manufacturerId, 'fab_login_password_hash', wp_hash_password($password));
        update_post_meta($manufacturerId, 'fab_login_password_plain', $password);
        if ($userId > 0) {
            wp_set_password($password, $userId);
        }
    }

    return array(
        'email' => $email,
        'generated_password' => $password,
        'status' => $status,
    );
}

function ensure_editor_account(
    $responsibleName,
    $responsibleEmail,
    $responsiblePhone,
    $preferredRole,
    $resetExistingPasswords
) {
    $emptyResult = array(
        'user_id' => 0,
        'username' => '',
        'generated_password' => '',
        'status' => 'none',
    );

    if ($responsibleEmail === '') {
        return $emptyResult;
    }

    $email = sanitize_email($responsibleEmail);
    if (! is_email($email)) {
        fwrite(STDERR, "Invalid email for responsible contact: {$responsibleEmail}\n");
        return $emptyResult;
    }

    $role = resolve_user_role($preferredRole);
    $user = get_user_by('email', $email);

    if ($user instanceof WP_User) {
        $payload = array(
            'ID' => (int) $user->ID,
            'display_name' => $responsibleName !== '' ? $responsibleName : $user->display_name,
        );

        if ($role !== '' && empty($user->roles)) {
            $payload['role'] = $role;
        }

        wp_update_user($payload);
        if ($responsibleName !== '') {
            update_user_meta($user->ID, 'first_name', $responsibleName);
        }
        if ($responsiblePhone !== '') {
            update_user_meta($user->ID, 'fab_responsavel_telefone', $responsiblePhone);
        }

        $status = 'existing';
        $password = '';
        if ($resetExistingPasswords) {
            $password = wp_generate_password(18, true, true);
            wp_set_password($password, $user->ID);
            $status = 'password_reset';
        }

        return array(
            'user_id' => (int) $user->ID,
            'username' => (string) $user->user_login,
            'generated_password' => $password,
            'status' => $status,
        );
    }

    $username = generate_unique_username($email, $responsibleName);
    $password = wp_generate_password(18, true, true);

    $userId = wp_create_user($username, $password, $email);
    if (is_wp_error($userId)) {
        fwrite(STDERR, "Failed to create user {$email}: {$userId->get_error_message()}\n");
        return $emptyResult;
    }

    $userId = (int) $userId;
    if ($role !== '') {
        $wpUser = new WP_User($userId);
        $wpUser->set_role($role);
    }

    wp_update_user(array(
        'ID' => $userId,
        'display_name' => $responsibleName !== '' ? $responsibleName : $username,
    ));

    if ($responsibleName !== '') {
        update_user_meta($userId, 'first_name', $responsibleName);
    }
    if ($responsiblePhone !== '') {
        update_user_meta($userId, 'fab_responsavel_telefone', $responsiblePhone);
    }

    return array(
        'user_id' => $userId,
        'username' => $username,
        'generated_password' => $password,
        'status' => 'created',
    );
}

function build_substance_index()
{
    $posts = get_posts(array(
        'post_type' => 'post',
        'post_status' => array('publish', 'draft', 'pending', 'private'),
        'posts_per_page' => -1,
        'orderby' => 'title',
        'order' => 'ASC',
    ));

    $index = array();
    foreach ($posts as $post) {
        $normalized = normalize_lookup_value($post->post_title);
        if ($normalized === '') {
            continue;
        }

        if (! isset($index[$normalized])) {
            $index[$normalized] = (int) $post->ID;
        }
    }

    return $index;
}

function match_substance_post_id($name, $index, $fabricamos)
{
    $normalized = normalize_lookup_value($name);
    if ($normalized === '') {
        return 0;
    }

    if (isset($index[$normalized])) {
        return (int) $index[$normalized];
    }

    if (method_exists($fabricamos, 'search_dictionary_substances')) {
        $results = $fabricamos->search_dictionary_substances($name, 10);
    } else {
        $results = $fabricamos->search_substances($name, 10);
    }

    if (empty($results)) {
        return 0;
    }

    foreach ($results as $post) {
        $postTitle = is_array($post) ? (isset($post['title']) ? $post['title'] : '') : $post->post_title;
        $postId = is_array($post) ? (isset($post['id']) ? (int) $post['id'] : 0) : (int) $post->ID;

        if (normalize_lookup_value($postTitle) === $normalized) {
            return $postId;
        }
    }

    if (count($results) === 1) {
        $first = $results[0];
        return is_array($first) ? (isset($first['id']) ? (int) $first['id'] : 0) : (int) $first->ID;
    }

    foreach ($results as $post) {
        $postTitle = is_array($post) ? (isset($post['title']) ? $post['title'] : '') : $post->post_title;
        $postId = is_array($post) ? (isset($post['id']) ? (int) $post['id'] : 0) : (int) $post->ID;
        $candidate = normalize_lookup_value($postTitle);
        if ($candidate !== '' && (contains_text($candidate, $normalized) || contains_text($normalized, $candidate))) {
            return $postId;
        }
    }

    return 0;
}

function normalize_lookup_value($value)
{
    $value = wp_strip_all_tags($value);
    $value = remove_accents($value);
    $value = strtolower($value);
    $value = preg_replace('/[^a-z0-9]+/u', ' ', $value);
    $value = preg_replace('/\s+/u', ' ', (string) $value);
    return trim((string) $value);
}

function normalize_text($value)
{
    $value = str_replace(array("\r", "\n"), ' ', $value);
    $value = preg_replace('/\s+/u', ' ', $value);
    return trim((string) $value);
}

function normalize_text_list($values)
{
    $normalized = array();
    foreach ($values as $value) {
        $text = normalize_text((string) $value);
        if ($text === '') {
            continue;
        }
        if (! in_array($text, $normalized, true)) {
            $normalized[] = $text;
        }
    }

    return $normalized;
}

function normalize_catalog_items($items)
{
    $normalized = array();

    foreach ($items as $item) {
        if (! is_array($item)) {
            continue;
        }

        $catalogItem = array(
            'insumo' => normalize_text((string) array_get($item, 'insumo', '')),
            'dcb' => normalize_text((string) array_get($item, 'dcb', '')),
            'inn' => normalize_text((string) array_get($item, 'inn', '')),
            'cas' => normalize_text((string) array_get($item, 'cas', '')),
            'ncm' => normalize_text((string) array_get($item, 'ncm', '')),
            'cbpf' => normalize_text((string) array_get($item, 'cbpf', '')),
            'validade' => normalize_text((string) array_get($item, 'validade', '')),
            'display_name' => normalize_text((string) array_get($item, 'display_name', '')),
        );

        if (implode('', $catalogItem) === '') {
            continue;
        }

        $normalized[] = $catalogItem;
    }

    return $normalized;
}

function derive_compiled_substances_from_catalog_items($catalogItems)
{
    $substances = array();

    foreach ($catalogItems as $item) {
        $displayName = normalize_text((string) array_get($item, 'display_name', ''));
        if ($displayName === '') {
            $displayName = normalize_text((string) array_get($item, 'inn', ''));
        }
        if ($displayName === '') {
            $displayName = normalize_text((string) array_get($item, 'insumo', ''));
        }
        if ($displayName === '') {
            continue;
        }
        if (! in_array($displayName, $substances, true)) {
            $substances[] = $displayName;
        }
    }

    return $substances;
}

function sync_post_meta_text($postId, $metaKey, $value)
{
    if ($value === '') {
        delete_post_meta($postId, $metaKey);
        return;
    }

    update_post_meta($postId, $metaKey, $value);
}

function resolve_user_role($preferredRole)
{
    $preferredRole = normalize_text($preferredRole);
    $roles = wp_roles()->roles;

    if ($preferredRole !== '' && isset($roles[$preferredRole])) {
        return $preferredRole;
    }

    foreach (array('author', 'subscriber') as $fallbackRole) {
        if (isset($roles[$fallbackRole])) {
            return $fallbackRole;
        }
    }

    return '';
}

function generate_unique_username($email, $responsibleName)
{
    $candidates = array();
    $localPart = strstr($email, '@', true);
    if ($localPart !== false) {
        $candidates[] = sanitize_user($localPart, true);
    }
    $candidates[] = sanitize_user($responsibleName, true);
    $candidates[] = sanitize_user(str_replace('@', '_', $email), true);

    foreach ($candidates as $candidate) {
        if ($candidate === '') {
            continue;
        }

        if (! username_exists($candidate)) {
            return $candidate;
        }

        for ($suffix = 2; $suffix <= 99; $suffix++) {
            $trial = $candidate . $suffix;
            if (! username_exists($trial)) {
                return $trial;
            }
        }
    }

    do {
        $fallback = 'fabricamos_' . wp_rand(10000, 99999);
    } while (username_exists($fallback));

    return $fallback;
}

function open_credentials_output($path)
{
    if ($path === null || $path === '') {
        return null;
    }

    $directory = dirname($path);
    if (! is_dir($directory)) {
        wp_mkdir_p($directory);
    }

    $handle = fopen($path, 'wb');
    if ($handle === false) {
        fwrite(STDERR, "Unable to open credentials output: {$path}\n");
        return null;
    }

    fputcsv($handle, array('company', 'responsible_name', 'email', 'username', 'password', 'status', 'user_id'));

    return array(
        'path' => $path,
        'handle' => $handle,
    );
}

function write_credentials_row($writer, $row)
{
    if ($writer === null) {
        return;
    }

    fputcsv($writer['handle'], array(
        array_get($row, 'company', ''),
        array_get($row, 'responsible_name', ''),
        array_get($row, 'email', ''),
        array_get($row, 'username', ''),
        array_get($row, 'password', ''),
        array_get($row, 'status', ''),
        array_get($row, 'user_id', ''),
    ));
}

function close_credentials_output($writer)
{
    if ($writer === null) {
        return;
    }

    fclose($writer['handle']);
    echo "CREDENTIALS_FILE|{$writer['path']}" . PHP_EOL;
}

function array_get($array, $key, $defaultValue)
{
    if (! is_array($array) || ! array_key_exists($key, $array)) {
        return $defaultValue;
    }

    return $array[$key];
}

function starts_with($text, $prefix)
{
    return strncmp($text, $prefix, strlen($prefix)) === 0;
}

function contains_text($text, $fragment)
{
    return $fragment !== '' && strpos($text, $fragment) !== false;
}
