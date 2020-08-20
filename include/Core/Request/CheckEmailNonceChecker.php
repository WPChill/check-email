<?php namespace EmailLog\Core\Request;

use EmailLog\Core\Loadie;
use EmailLog\Core\UI\Page\EmailLogListPage;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

/**
 * Check nonce for all Check Email Log requests.
 */
class CheckEmailNonceChecker implements Loadie {

	public function load() {
		add_action( 'admin_init', array( $this, 'check_nonce' ) );
	}

	public function check_nonce() {
		if ( ! isset( $_POST['el-action'] ) && ! isset( $_REQUEST['action'] ) && ! isset( $_REQUEST['action2'] ) ) {
			return;
		}

		if ( isset( $_POST['el-action'] ) ) {
			$action = sanitize_text_field( $_POST['el-action'] );

			if ( ! isset( $_POST[ $action . '_nonce' ] ) ) {
				return;
			}

			if ( ! wp_verify_nonce( $_POST[ $action . '_nonce' ], $action ) ) {
				return;
			}
		}

		if ( isset( $_REQUEST['action'] ) || isset( $_REQUEST['action2'] ) ) {
			$action = sanitize_text_field( $_REQUEST['action'] );

			if ( '-1' === $action ) {
				if ( ! isset( $_REQUEST['action2'] ) ) {
					return;
				}

				$action = sanitize_text_field( $_REQUEST['action2'] );
			}

			if ( strpos( $action, 'el-log-list-' ) !== 0 ) {
				return;
			}

			if ( ! isset( $_REQUEST[ EmailLogListPage::LOG_LIST_ACTION_NONCE_FIELD ] ) ) {
				return;
			}

			if ( ! wp_verify_nonce( $_REQUEST[ EmailLogListPage::LOG_LIST_ACTION_NONCE_FIELD ], EmailLogListPage::LOG_LIST_ACTION_NONCE ) ) {
				return;
			}
		}

		do_action( 'el_action', $action, $_REQUEST );
		do_action( $action, $_REQUEST );
	}
}