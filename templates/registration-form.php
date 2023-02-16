<?php 

    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

    
    //Site Key : 6LdynxokAAAAAOJMowx9WFIE5Izz2DHf3BrpUlUE
    //Secret Key : 6LdynxokAAAAALSbeWqEd3l7R1C8OtriXlriV_Sc
    $attributes['recaptcha_site_key'] = '6LdI9TAkAAAAABbW_Kz_uDb1OUPnl27jy-IwkArv';
?>

<?php if ( count( $attributes['errors'] ) > 0 ) : ?>
    <?php foreach ( $attributes['errors'] as $error ) : ?>
        <p>
            <?php echo $error; ?>
        </p>
    <?php endforeach; ?>
<?php endif; ?>

<?php 
    // if ( count( $attributes['submited_data'] ) > 0 ) :
    //     $first_name = (isset($attributes['submited_data']['first_name'])) ? $attributes['submited_data']['first_name'] : '';
    //     $last_name = (isset($attributes['submited_data']['last_name'])) ? $attributes['submited_data']['last_name'] : '';
    //     $email = (isset($attributes['submited_data']['email'])) ? $attributes['submited_data']['email'] : '';
    // endif;
?>

<style>
    .acf-field input[type=number], .acf-field select{
        padding: 0.5rem 1rem;
    }
    p.form-row label {
        margin: 10px auto;
    }
    .acf-fields > .acf-field {
        border: none;
        margin: 5px auto;
        padding: 10px 12px;
    }
</style>




<div id="register-form" class="widecolumn">
    <?php if ( $attributes['show_title'] ) : ?>
        <h3><?php _e( 'Register', 'pd-custom-logreg' ); ?></h3>
    <?php endif; ?>
    <?php 
        if ($_GET['registered'] == 'true') {
            echo '<h4 style="color: green;">Registration Successful!</h4>';
        }
    ?>
    <span style="color: #fcb12b;font-weight: 600;">Note: For security, when you click on register below, we will send the password reset link to your registered email address. Please visit the link and set your password.</span>
    
    
    
    <form id="signupform" class="pdcustomlogreg registration" action="<?php echo wp_registration_url().'&pdcustomlogreg=true'; ?>" method="post">
        <p class="form-row first-name">
            <label for="first_name"><strong><?php _e( 'First name ', 'pd-custom-logreg' ); ?><span class="acf-required">*</span></strong></label>
            <input type="text" name="first_name" id="first-name" value="<?php //echo $first_name; ?>" required>
        </p>
        <p class="form-row last-name">
            <label for="last_name"><strong><?php _e( 'Last name ', 'pd-custom-logreg' ); ?><span class="acf-required">*</span></strong></label>
            <input type="text" name="last_name" id="last-name" value="<?php //echo $last_name; ?>" required>
        </p>
        <p class="form-row email">
            <label for="email"><strong><?php _e( 'Email ', 'pd-custom-logreg' ); ?><span class="acf-required">*</span></strong></label>
            <input type="text" name="email" id="email" value="<?php //echo $email; ?>" required>
        </p>
        <?php

        if(!empty($attributes['field_groups']) || !empty($attributes['fields'])){

            $options = array(
                'form' => false,
                'html_before_fields' => '<p class="form-row">',
                'html_after_fields' => '</p>',
            );

            if(!empty($attributes['field_groups'])){
                $options['field_groups'] = $attributes['field_groups'];
            }

            if(!empty($attributes['fields'])){
                $options['fields'] = $attributes['fields'];
            }

            acf_form( $options );

        }

        do_action( 'add_after_pdclogreg_registration_form' );

        ?>

        <?php if ( $attributes['recaptcha_site_key'] ) : ?>
            <div class="recaptcha-container">
                <div class="g-recaptcha" data-sitekey="<?php echo $attributes['recaptcha_site_key']; ?>"></div>
            </div>
        <?php endif; ?>

        <p class="signup-submit">
            <input type="hidden" name="redirect_to" id="redirect-to" value="<?php echo get_the_permalink(); ?>">
            <input type="hidden" name="redirect_after_success" id="redirect-after-success" value="<?php echo $attributes['redirect_after_success']; ?>">
            <input type="submit" name="submit" class="register-button"
                   value="<?php _e( 'Register', 'personalize-login' ); ?>"/>
        </p>

        <p class="form-row">
            <?php// _e( 'Note: For security, we will send the password reset link on your registered email address, please visit the link and set your password.', 'pd-custom-logreg' ); ?>
        </p>
    </form>
</div>

<script>
    jQuery('document').ready(function($){
        $('.is-required .acf-input input').prop('required',true);
    })
</script>