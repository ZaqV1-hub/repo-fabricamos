<?php

//
// Configurations & Constants
//

const CLARITY_COLLECT_ENDPOINT = 'https://ai.clarity.ms/collect';
const CLARITY_COLLECT_PLUGIN_VERSION = 'WordPress-0.10.9';

const CLARITY_COLLECT_BATCH_KEY = 'clarity_collect_batch';
const CLARITY_COLLECT_BATCH_SIZE = 50;

/**
 * Collects and sends Clarity events in batches.
 */
function clarity_collect_event() {
    try {
        if (is_admin()) {
            return;
        }

        $clarity_project_id = get_option('clarity_project_id');
        $clarity_wp_site = get_option('clarity_wordpress_site_id');

        // Ensure required identifiers are present
        if (empty($clarity_project_id) || empty($clarity_wp_site)) {
            return;
        }

        // Prevent multiple event to be sent in the same session
        if (!empty($_SESSION['clarity_event_processed'])) {
            return;
        }

        // Mark the session as processed
        $_SESSION['clarity_event_processed'] = true;

        // Construct and buffer the collect event payload for batch sending
        $event = clarity_construct_collect_event($clarity_project_id);
        clarity_buffer_collect_event($event);
    }
    catch (Exception $e) {
        // Silently fail on any error
    }
}

add_action('init', 'clarity_collect_event');

/**
 * Buffers a collect event payload for batch sending.
 *
 * @param array $event The event payload to buffer.
 */
function clarity_buffer_collect_event($event) {
    global $wpdb;

    if (!$wpdb->ready) {
        return;
    }

    $batch = array();
    $shouldSendBatch = false;

    try {
        // Lock to prevent race conditions
        $wpdb->query('START TRANSACTION');

        // Fetch existing event batch
        $batch = get_option(CLARITY_COLLECT_BATCH_KEY, array());

        // Append the new payload to the batch
        $batch[] = $event;

        // If the batch size reached the maximum or the elapsed time exceeded the limit,
        // clear it from the database and send it after releasing the lock
        if (count($batch) >= CLARITY_COLLECT_BATCH_SIZE) {
            update_option(CLARITY_COLLECT_BATCH_KEY, array(), false);
            $shouldSendBatch = true;
        }
        // Otherwise, write the updated batch
        else {
            update_option(CLARITY_COLLECT_BATCH_KEY, $batch, false);
        }
    }
    finally {
        // Release the lock
        $wpdb->query('COMMIT');
    }

    // Send the batch if needed
    if ($shouldSendBatch) {
        clarity_send_collect_event_batch($batch);
    }
}

//
// Helper Functions
//

/**
 * Constructs the event payload for the collect endpoint.
 *
 * @param string $clarity_project_id The Clarity project ID.
 * @return array The constructed event payload.
 */
function clarity_construct_collect_event($clarity_project_id) {
    $envelope = array(
        'projectId' => $clarity_project_id,
        'sessionId' => wp_get_session_token(),
        'version'   => CLARITY_COLLECT_PLUGIN_VERSION,
    );

    $analytics = array(
        'time'   => time(),
        'ip'     => clarity_get_ip_address(),
        'ua'     => isset($_SERVER['HTTP_USER_AGENT']) ? sanitize_text_field($_SERVER['HTTP_USER_AGENT']) : 'Unknown',
        'url'    => home_url($_SERVER['REQUEST_URI']),
        'method' => isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'Unknown',
    );

    $payload = array(
        'envelope'  => $envelope,
        'analytics' => $analytics,
    );

    return $payload;
}

/**
 * Sends a batch of events to the Clarity collect endpoint.
 *
 * @param array $events The batch of events to send.
 */
function clarity_send_collect_event_batch($events) {
    $request = array(
        'body'     => json_encode($events),
        'headers'  => array('Content-Type' => 'application/json'),
        'blocking' => false,
        'timeout'     => '1',
        'redirection' => '5',
        'httpversion' => '1.0'
    );

    wp_remote_post(CLARITY_COLLECT_ENDPOINT, $request);
}

/**
 * Retrieves the client's IP address, excluding private and reserved ranges.
 */
function clarity_get_ip_address() {
    foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR') as $key) {
        if (empty($_SERVER[$key])) {
            continue;
        }

        foreach (explode(',', $_SERVER[$key]) as $ip) {
            $ip = trim($ip);

            if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                return $ip;
            }
        }
    }

    return 'Unknown';
}
