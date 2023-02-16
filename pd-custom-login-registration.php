<?php
/*
 * Plugin Name: PD Custom Login Registration Flow
 * Version: 1.0.0
 * Description: This plugin will help you to implement custom registration and login forms and secure the wordpress
 * Plugin URI: https://pradeepdabane.com/pd-custom-login-registration
 * Author: Pradeep Dabane
 * Author URI: https://pradeepdabane.com
 * Text Domain: pd-custom-logreg
 * Domain Path: /languages
 * License: GPLv3
 * License URI: http://www.gnu.org/licenses/gpl-3.0
*/


if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

define( 'PREFIX', 'pdCustomLogReg');
define( 'PDCLOGREG_DIR', plugin_dir_path( __FILE__ ));
define( 'PDCLOGREG_NAME', plugin_basename( __FILE__ ));
define( 'PDCLOGREG_URL', plugin_dir_url( __FILE__ ));
define ('PDCLOGREG_VER','1.0.0');
/*
required files..
 */
require PDCLOGREG_DIR . 'pd_reset_pw_mail.php';


class PdCustomLogReg_Plugin {
    /** 
* Initializes the plugin. 
* 
* To keep the initialization fast, only add filter and action 
* hooks in the constructor. 
*/
    public function __construct() {
        
        // add_filter( 'body_class', 'am_acf_form_class' );
        add_action( 'get_header', array($this, 'pdCustomLogReg_acf_form_head'), 1 );
        add_shortcode( 'pdCustomLogReg-registration-form', array( $this, 'registration_form' ) );
        // add_action( 'login_form_register', array( $this, 'redirect_to_pdcustomlogreg' ) );
        add_action( 'login_form_register', array( $this, 'do_register_user' ) );
        add_action( 'wp_print_footer_scripts', array( $this, 'add_captcha_js_to_footer' ) );
        //add_filter( 'lostpassword_redirect', 'redirect_after_password_reset' );
        
    }

    // function redirect_after_password_reset( $lostpassword_redirect ) {
    //     return home_url('signin');
    // }

    //* Add custom body class to the head
    // function am_acf_form_class( $classes ) {
    //     $classes[] = 'acf-form-front';
    //     return $classes;
    // }

    /** 
    * An action function used to include the reCAPTCHA JavaScript file 
    * at the end of the page. 
    */
    public function add_captcha_js_to_footer() {
        echo "<script src='https://www.google.com/recaptcha/api.js'></script>";
    }

    /**
     * ACF frontend form header for adding extra fields using ACF plugin
     * 
     */
    private function pdCustomLogReg_acf_form_head() {
        if ( !is_admin() )
        acf_form_head();
    }

    /** 
    * A shortcode for rendering the new user registration form. 
    * 
    * @param array $attributes Shortcode attributes. 
    * @param string $content The text content for shortcode. Not used. 
    * 
    * @return string The shortcode output 
    */
    public function registration_form( $attributes, $content = null ) {
        // Parse shortcode attributes 
        $default_attributes = array( 
            'show_title' => false,
            'redirect_after_success' => '',
            'field_groups' => array(),
            'fields' => array()
        );
        $attributes = shortcode_atts( $default_attributes, $attributes );
        if ( is_user_logged_in() ) {
            return __( 'You are already signed in.', 'pd-custom-logreg' );
        } elseif ( ! get_option( 'users_can_register' ) ) {
            return __( 'Registering new users is currently not allowed.', 'pd-custom-logreg' );
        } else {

            // Retrieve possible errors from request parameters 
            $attributes['errors'] = array();
            if ( isset( $_REQUEST['register-errors'] ) ) {
                $error_codes = explode( ',', $_REQUEST['register-errors'] );
                foreach ( $error_codes as $error_code ) {
                    $attributes['errors'] []= $this->get_error_message( $error_code );
                }
            }


            if(!empty($attributes['field_groups'])){
                $attributes['field_groups'] = explode( ',', $attributes['field_groups'] );
            }

            if(!empty($attributes['fields'])){
                $attributes['fields'] = explode( ',', $attributes['fields'] );
            }

            // print_r($attributes['fields']);
            // exit;

            return $this->get_template( 'registration-form', $attributes );
        }
    }

    /** 
    * Handles the registration of a new user. 
    * 
    * Used through the action hook "login_form_register" activated on wp-login.php 
    * when accessed through the registration action. 
    */
    public function do_register_user() {
        if ( 'POST' == $_SERVER['REQUEST_METHOD'] && isset( $_REQUEST['pdcustomlogreg'] ) && $_REQUEST['pdcustomlogreg']) {
            $redirect_url = (!empty($_POST['redirect_to'])) ? $_POST['redirect_to'] : '';
            $redirect_after_success = (!empty($_POST['redirect_after_success'])) ? home_url( $_POST['redirect_after_success'] ) : home_url();

            if ( ! get_option( 'users_can_register' ) ) {
                // Registration closed, display error 
                $redirect_url = add_query_arg( 'register-errors', 'closed', $redirect_url );
            } else {
                $email = $_POST['email'];
                $first_name = sanitize_text_field( $_POST['first_name'] );
                $last_name = sanitize_text_field( $_POST['last_name'] );
                $result = $this->register_user( $email, $first_name, $last_name );
                if ( is_wp_error( $result ) ) {
                    // Parse errors into a string and append as parameter to redirect 
                    $errors = join( ',', $result->get_error_codes() );
                    $redirect_url = add_query_arg( 'register-errors', $errors, $redirect_url );
                } else {

                    if(isset($_POST['acf'])){
                        foreach($_POST['acf'] as $field => $value){
                            update_field( $field, $value, 'user_'.$result );
                        }
                    }
                    // Success, redirect to login page. 
                    $redirect_url = $redirect_after_success;
                    $redirect_url = add_query_arg( 'registered', 'true', $redirect_url );
                }
            }
            wp_redirect( $redirect_url );
            exit;
        }
    }

    /** 
    * Validates and then completes the new user signup process if all went well. 
    * 
    * @param string $email The new user's email address 
    * @param string $first_name The new user's first name 
    * @param string $last_name The new user's last name 
    * 
    * @return int|WP_Error The id of the user that was created, or error if failed. 
    */
    private function register_user( $email, $first_name, $last_name ) {
        $errors = new WP_Error();
        // Email address is used as both username and email. It is also the only 
        // parameter we need to validate 
        if ( ! is_email( $email ) ) {
            $errors->add( 'email', $this->get_error_message( 'email' ) );
            return $errors;
        }
        if ( username_exists( $email ) || email_exists( $email ) ) {
            $errors->add( 'email_exists', $this->get_error_message( 'email_exists') );
            return $errors;
        }
        // Generate the password so that the subscriber will have to check email... 
        $password = wp_generate_password( 12, false );
        $user_data = array(
            'user_login'    => $email,
            'user_email'    => $email,
            'user_pass'     => $password,
            'first_name'    => $first_name,
            'last_name'     => $last_name,
            'nickname'      => $first_name,
            //'role' => 'sales-representative'
        );
        $user_id = wp_insert_user( $user_data );

        do_action( 'pdclogreg_after_register_user', $user_id );
        //wp_new_user_notification( $user_id, $password );

        return $user_id;
    }

    /** 
    * Redirects the user to the custom registration page instead 
    * of wp-login.php?action=register. 
    */
    public function redirect_to_pdcustomlogreg() {
        if ( 'GET' == $_SERVER['REQUEST_METHOD'] ) {
            if ( is_user_logged_in() ) {
                $this->redirect_logged_in_user();
            } else {
                wp_redirect( home_url( 'member-register' ) );
            }
            exit;
        }
    }

    /** 
    * Finds and returns a matching error message for the given error code. 
    * 
    * @param string $error_code The error code to look up. 
    * 
    * @return string An error message. 
    */
    private function get_error_message( $error_code ) {
        switch ( $error_code ) {
            case 'empty_username':
                return __( 'You do have an email address, right?', 'personalize-login' );
            case 'empty_password':
                return __( 'You need to enter a password to login.', 'personalize-login' );
            case 'invalid_username':
                return __(
                    "We don't have any users with that email address. Maybe you used a different one when signing up?",
                    'personalize-login'
                );
            case 'incorrect_password':
                $err = __(
                    "The password you entered wasn't quite right. <a href='%s'>Did you forget your password</a>?",
                    'personalize-login'
                );
                return sprintf( $err, wp_lostpassword_url() );

            // Registration errors 
            case 'email':
                return __( 'The email address you entered is not valid.', 'personalize-login' );
            case 'email_exists':
                return __( 'An account exists with this email address.', 'personalize-login' );
            case 'closed':
                return __( 'Registering new users is currently not allowed.', 'personalize-login' );

            default:
                break;
        }
        
        return __( 'An unknown error occurred. Please try again later.', 'personalize-login' );
    }

    /** 
    * Renders the contents of the given template to a string and returns it. 
    * 
    * @param string $template_name The name of the template to render (without .php) 
    * @param array $attributes The PHP variables for the template 
    * 
    * @return string The contents of the template. 
    */
    private function get_template( $template_name, $attributes = null ) {
        if ( ! $attributes ) {
            $attributes = array();
        }
        ob_start();
        do_action( 'personalize_login_before_' . $template_name );
        require( PDCLOGREG_DIR.'templates/' . $template_name . '.php');
        do_action( 'personalize_login_after_' . $template_name );
        $html = ob_get_contents();
        ob_end_clean();
        return $html;
    }
    
}
// Initialize the plugin 
$PdCustomLogReg_Plugin = new PdCustomLogReg_Plugin();