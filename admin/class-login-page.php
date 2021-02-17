<?php

/**
 * Creates the submenu page for the plugin.
 *
 * @package LoginPage
 */

/**
 * Creates the submenu page for the plugin.
 *
 * Provides the functionality necessary for rendering the page corresponding
 * to the submenu with which this page is associated.
 *
 * @package    LoginPage
 * @subpackage Testpress_Lms/admin
 * @author     Testpress <support@testpress.in>
 */

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;

require( dirname( __FILE__ ) . '/../vendor/autoload.php' );

class LoginPage  extends AbstractMenuPage {

	private $plugin_name;
	private $error;

	public function __construct( $name, $plugin_name, $atts = array() ) {
		parent::__construct( $name, $atts );
		$this->plugin_name = $plugin_name;
		$this->error = new WP_Error();
	}

	public function onLoad() {
	
	}

	public function render() {
		require_once( 'partials/testpress-lms-login-page.php' );
	}


	private function has_valid_nonce() {

		if ( ! isset( $_POST['meta_nonce'] ) ) { 
			return false;
		}

		$field  = wp_unslash( $_POST['meta_nonce'] );
		$action = 'meta_form_nonce';

		return wp_verify_nonce( $field, $action );

	}

	public function handle_form_submission() {
		if ( $this->has_valid_nonce() ) {
            update_option( 'testpress_base_url', "https://{$_POST['testpress-subdomain']}.testpress.in/");

            $response = login_user();
            $data = json_decode( $response->getBody()->getContents());
			store_auth_token($data);
			store_testpress_private_key();
		} else {
			wp_die( __( 'Invalid nonce specified', $this->plugin_name ), __( 'Error', $this->plugin_name ), array(
				'response'  => 403,
				'back_link' => 'admin.php?page=' . $this->plugin_name,
			) );
		}
    }
    
    private function login_user() {
        $client   = new \GuzzleHttp\Client();
		$url = "https://{$_POST['testpress-subdomain']}.testpress.in/api/v2.3/auth-token/";

        try {
            $response = $client->request( 'POST', $url, [
                'json' => [ 'username' => $_POST['testpress-username'], 'password' => $_POST['testpress-password'] ],
            ] );
        }  catch (ClientException | RequestException $e) {
            $this->error->add('login_error', 'Your login or password is incorrect');
            $this->custom_redirect( "testpress-lms", $this->error->get_error_code() );
            exit;
        }
        return $response;
    }

    private function store_auth_token($data) {
        update_option( 'testpress_auth_token', $data->token);
    }

    private function store_testpress_private_key() {
        try {
            $settings_response = $this->get_request("https://{$_POST['testpress-subdomain']}.testpress.in/api/v2.3/admin/settings/");
        }  catch (ClientException | RequestException $e) {
            $this->error->add('login_error', 'This account is not admin account. Please enter admin account details.');
            $this->custom_redirect( "testpress-lms", "login_error" );
            exit;
        }

        $result = json_decode( $settings_response->getBody()->getContents() );
        update_option( 'testpress_private_key', $result->private_key);
        $this->custom_redirect( "testpress-products", "success", $_POST );
        exit;
    }

	private function get_request( $endpoint ) {
		$client   = new \GuzzleHttp\Client();
		$response = $client->request( 'GET', $endpoint, [
			'headers' => [
				'Authorization' => 'JWT ' . get_option( 'testpress_auth_token' )
			]
		] );

		return $response;
	}


	/**
	 * Redirect
	 *
	 * @since    1.0.0
	 */
	public function custom_redirect( $page, $status) {
		wp_redirect( esc_url_raw( add_query_arg( array(
			'status' => $status
		),
			admin_url( 'admin.php?page='. $page)
		) ) );
	}


	protected function getPageTitle() {
		return __( 'Testpress LMS', 'testpress-lms' );
	}

	protected function getMenuTitle() {
		return __( 'Testpress LMS', 'testpress-lms' );
	}
}