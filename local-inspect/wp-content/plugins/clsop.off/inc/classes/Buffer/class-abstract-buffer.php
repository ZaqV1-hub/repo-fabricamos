<?php
/**
 * Source file was changed by CloudLinux on Wed Jul 02 14:54:30 2025 +0000
 */
namespace WP_Rocket\Buffer;

use WP_Rocket\Logger\Logger;

/**
 * Handle page cache and optimizations.
 *
 * @since  3.3
 * @author Grégory Viguier
 */
abstract class Abstract_Buffer {

	/**
	 * Process identifier used by the logger.
	 *
	 * @var    string
	 * @since  3.3
	 * @access protected
	 * @author Grégory Viguier
	 */
	protected $process_id;

	/**
	 * Instance of the Tests class.
	 *
	 * @var    Tests
	 * @since  3.3
	 * @access protected
	 * @author Grégory Viguier
	 */
	protected $tests;

	/**
	 * Constructor.
	 *
	 * @since  3.3
	 * @access public
	 * @author Grégory Viguier
	 *
	 * @param Tests $tests Tests instance.
	 */
	public function __construct( Tests $tests ) {
		$this->tests = $tests;
	}

	/** ----------------------------------------------------------------------------------------- */
	/** PROCESS ================================================================================= */
	/** ----------------------------------------------------------------------------------------- */

	/**
	 * Launch the process if the tests succeed.
	 * This should be the first thing to use after initializing the class.
	 *
	 * @since  3.3
	 * @access public
	 * @see    $this->tests->can_init_process()
	 * @author Grégory Viguier
	 */
	abstract public function maybe_init_process();

	/**
	 * Process the page buffer if the 2nd set of tests succeed.
	 * It should be used like this:
	 *     ob_start( [ $this, 'maybe_process_buffer' ] );
	 *
	 * @since  3.3
	 * @access public
	 * @see    $this->tests->can_process_buffer()
	 * @author Grégory Viguier
	 *
	 * @param  string $buffer The buffer content.
	 * @return string         The buffered content
	 */
	abstract public function maybe_process_buffer( $buffer );

	/** ----------------------------------------------------------------------------------------- */
	/** LOG ===================================================================================== */
	/** ----------------------------------------------------------------------------------------- */

	/**
	 * Log the last test "error".
	 *
	 * @since  3.3
	 * @access protected
	 * @author Grégory Viguier
	 */
	protected function log_last_test_error() {
		$error = $this->tests->get_last_error();

		$this->log( $error['message'], $error['data'] );
	}

	/**
	 * Comment the last test "error".
	 *
	 * @param string|null $buffer Html code.
	 *
	 * @note CL.
	 * @access protected
	 */
	protected function comment_last_test_error( &$buffer = '' ) {
		$error = $this->tests->get_last_error();

		$message = $error['message'];

		if ( ! empty( $message ) && ( ! defined( 'REST_REQUEST' ) || ! REST_REQUEST ) ) {
			if ( array_key_exists( 'accelerate_wp_did_debug', $GLOBALS ) && true === $GLOBALS['accelerate_wp_did_debug'] ) {
				return;
			}

			$GLOBALS['accelerate_wp_did_debug'] = true; // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound

			$comment = PHP_EOL . '<!-- AccelerateWP Debug: ' . htmlspecialchars( $message ) . ' -->';

			if ( ! empty( $buffer ) ) {
				$buffer .= $comment;
			} else {
				add_action(
					'wp_footer',
					function () use ( $comment ) {
						echo $comment; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					},
					999
				);
			}
		}
	}

	/**
	 * Log events.
	 *
	 * @since  3.3
	 * @access protected
	 * @author Grégory Viguier
	 *
	 * @param string $message A message to log.
	 * @param array  $data    Related data.
	 * @param string $type    Event type to log. Possible values are 'info', 'error', and 'debug' (default).
	 */
	protected function log( $message, $data = [], $type = 'debug' ) {
		$data = array_merge(
			[
				$this->get_process_id(),
				'request_uri' => $this->tests->get_raw_request_uri(),
			],
			$data
		);

		if ( isset( $data['cookies'] ) ) {
			$data['cookies'] = Logger::remove_auth_cookies( $data['cookies'] );
		}

		switch ( $type ) {
			case 'info':
				Logger::info( $message, $data );
				break;
			case 'error':
				Logger::error( $message, $data );
				break;
			default:
				Logger::debug( $message, $data );
		}
	}

	/**
	 * Get the process identifier.
	 *
	 * @since  3.3
	 * @access public
	 * @author Grégory Viguier
	 *
	 * @return string
	 */
	public function get_process_id() {
		return $this->process_id . ' - Thread #' . Logger::get_thread_id();
	}

	/** ----------------------------------------------------------------------------------------- */
	/** VARIOUS TOOLS =========================================================================== */
	/** ----------------------------------------------------------------------------------------- */

	/**
	 * Tell if the page content is HTML.
	 *
	 * @since  3.3
	 * @access protected
	 * @author Grégory Viguier
	 *
	 * @param  string $buffer The buffer content.
	 * @return bool
	 */
	protected function is_html( $buffer ) {
		return preg_match( '/<\/html>/i', $buffer );
	}
}
