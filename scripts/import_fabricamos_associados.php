<?php

declare(strict_types=1);

if ($argc < 3) {
    fwrite(STDERR, "Usage: php import_fabricamos_associados.php <wp-load.php> <json-file>\n");
    exit(1);
}

$wpLoadPath = $argv[1];
$jsonPath = $argv[2];

if (! file_exists($wpLoadPath)) {
    fwrite(STDERR, "wp-load.php not found: {$wpLoadPath}\n");
    exit(1);
}

if (! file_exists($jsonPath)) {
    fwrite(STDERR, "JSON file not found: {$jsonPath}\n");
    exit(1);
}

require $wpLoadPath;

if (! class_exists('Fabricamos_Native')) {
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

$fabricamos = Fabricamos_Native::instance();
$substanceIndex = build_substance_index();

$created = 0;
$updated = 0;
$matchedSubstances = 0;
$unmatchedSubstances = 0;
$rowsImported = 0;

foreach ($companies as $item) {
    $company = trim((string) ($item['company'] ?? ''));
    if ($company === '') {
        continue;
    }

    $processes = array_values(array_filter(array_map('trim', (array) ($item['processes'] ?? []))));
    $origins = array_values(array_filter(array_map('trim', (array) ($item['origins'] ?? []))));
    $compiledSubstances = array_values(array_filter(array_map('trim', (array) ($item['substances'] ?? []))));

    $manufacturerId = find_manufacturer_by_title($company);
    $isNew = false;

    if ($manufacturerId === 0) {
        $manufacturerId = wp_insert_post(array(
            'post_type' => 'fabricante',
            'post_status' => 'publish',
            'post_title' => $company,
            'post_content' => '',
        ), true);

        if (is_wp_error($manufacturerId)) {
            fwrite(STDERR, "Failed to create manufacturer {$company}: {$manufacturerId->get_error_message()}\n");
            continue;
        }

        $manufacturerId = (int) $manufacturerId;
        $created++;
        $isNew = true;
    } else {
        wp_update_post(array(
            'ID' => $manufacturerId,
            'post_title' => $company,
            'post_status' => 'publish',
        ));
        $updated++;
    }

    update_post_meta($manufacturerId, 'fab_associate_status', (string) ($item['associate'] ?? 'Associado'));
    update_post_meta($manufacturerId, 'fab_processo', implode(' / ', $processes));
    update_post_meta($manufacturerId, 'fab_origem', implode(' / ', $origins));
    update_post_meta($manufacturerId, 'fab_compiled_substances', array_values(array_unique($compiledSubstances)));

    // Keep imported companies without images for now.
    delete_post_meta($manufacturerId, 'fab_logo');
    delete_post_meta($manufacturerId, '_fab_logo');
    delete_post_meta($manufacturerId, 'fab_hero_image');
    delete_post_meta($manufacturerId, '_fab_hero_image');

    $matchedIds = array();
    foreach ($compiledSubstances as $substanceName) {
        $matchedId = match_substance_post_id($substanceName, $substanceIndex, $fabricamos);
        if ($matchedId > 0) {
            $matchedIds[] = $matchedId;
            $matchedSubstances++;
        } else {
            $unmatchedSubstances++;
        }
    }

    $matchedIds = array_values(array_unique(array_map('absint', $matchedIds)));
    if (function_exists('update_field')) {
        update_field('field_fab_substances', $matchedIds, $manufacturerId);
    } else {
        update_post_meta($manufacturerId, 'fab_substances', $matchedIds);
    }

    if ($isNew) {
        echo "CREATED|{$manufacturerId}|{$company}|substances=" . count($compiledSubstances) . "|matched=" . count($matchedIds) . PHP_EOL;
    } else {
        echo "UPDATED|{$manufacturerId}|{$company}|substances=" . count($compiledSubstances) . "|matched=" . count($matchedIds) . PHP_EOL;
    }

    $rowsImported++;
}

echo "SUMMARY|rows={$rowsImported}|created={$created}|updated={$updated}|matched_substances={$matchedSubstances}|unmatched_substances={$unmatchedSubstances}" . PHP_EOL;

function find_manufacturer_by_title(string $title): int
{
    $posts = get_posts(array(
        'post_type' => 'fabricante',
        'post_status' => array('publish', 'draft', 'pending', 'private'),
        'posts_per_page' => 1,
        'title' => $title,
        'orderby' => 'ID',
        'order' => 'ASC',
        'suppress_filters' => false,
    ));

    if (! empty($posts)) {
        return (int) $posts[0]->ID;
    }

    global $wpdb;
    $postId = $wpdb->get_var($wpdb->prepare(
        "SELECT ID FROM {$wpdb->posts} WHERE post_type = %s AND post_status IN ('publish','draft','pending','private') AND post_title = %s ORDER BY ID ASC LIMIT 1",
        'fabricante',
        $title
    ));

    return $postId ? (int) $postId : 0;
}

function build_substance_index(): array
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

function match_substance_post_id(string $name, array $index, Fabricamos_Native $fabricamos): int
{
    $normalized = normalize_lookup_value($name);
    if ($normalized === '') {
        return 0;
    }

    if (isset($index[$normalized])) {
        return (int) $index[$normalized];
    }

    $results = $fabricamos->search_substances($name, 10);
    if (empty($results)) {
        return 0;
    }

    foreach ($results as $post) {
        if (normalize_lookup_value($post->post_title) === $normalized) {
            return (int) $post->ID;
        }
    }

    if (count($results) === 1) {
        return (int) $results[0]->ID;
    }

    foreach ($results as $post) {
        $candidate = normalize_lookup_value($post->post_title);
        if ($candidate !== '' && (str_contains($candidate, $normalized) || str_contains($normalized, $candidate))) {
            return (int) $post->ID;
        }
    }

    return 0;
}

function normalize_lookup_value(string $value): string
{
    $value = wp_strip_all_tags($value);
    $value = remove_accents($value);
    $value = strtolower($value);
    $value = preg_replace('/[^a-z0-9]+/u', ' ', $value);
    $value = preg_replace('/\s+/u', ' ', (string) $value);
    return trim((string) $value);
}
