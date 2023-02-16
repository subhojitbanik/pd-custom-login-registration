<?php

//        do_action( 'pdclogreg_after_register_user', $user_id );

function pdlogreg_send_reset_pw_link($user_id){

    $headers = array('Content-Type: text/html; charset=UTF-8');

    $user = get_userdata( $user_id );
    $key = get_password_reset_key( $user );
    $reset_link = network_site_url( "wp-login.php?action=rp&key=$key&login=" . rawurlencode( $user->user_login ), 'login' );
    if ( is_wp_error( $key ) ) {
        return;
    }
    $switched_locale = switch_to_locale( get_user_locale( $user ) );
    /* translators: %s: User login. */
    $title  = "<p>".sprintf( __( 'Username: %s' ), $user->user_login ) . "</p>\r\n\r\n";
    $title .= "<p>".__( 'Click or visit the following link to set your password' ) . "</p>\r\n\r\n";
    $title .= '<a href="'.$reset_link.'">Reset Your Password</a><br/>';
    $title .= "<p>".__( 'To sign in ' ) . "</p>\r\n\r\n";

    $title .= "<a href='".home_url()."/signin/' style ='display:inline-block;background:#134478;color:#ffffff;font-family:Ubuntu, Helvetica, Arial, sans-serif;font-size:13px;font-weight:normal;line-height:120%;margin:0;text-decoration:none;text-transform:none;padding:10px 25px;mso-padding-alt:0px;border-radius:3px;'>Click Here</a>\r\n";

    $subject = 'Registration Successful!';
    $user_mail = $user->user_email;
    $msg = pdlogreg_reset_pw_mail_template($title, $subject);

    wp_mail($user_mail,$subject,$msg,$headers);
}
add_action('pdclogreg_after_register_user','pdlogreg_send_reset_pw_link',10,1);


/**
 * email template
 */


 function pdlogreg_reset_pw_mail_template($title, $subject){
    ob_start();
        ?>
        <!doctype html>
        <html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
    
            <head>
                <title>
                </title>
                <!--[if !mso]><!-->
                <meta http-equiv="X-UA-Compatible" content="IE=edge">
                <!--<![endif]-->
                <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1">
                <style type="text/css">
                    #outlook a {
                    padding: 0;
                    }
        
                    body {
                    margin: 0;
                    padding: 0;
                    -webkit-text-size-adjust: 100%;
                    -ms-text-size-adjust: 100%;
                    }
        
                    table,
                    td {
                    border-collapse: collapse;
                    mso-table-lspace: 0pt;
                    mso-table-rspace: 0pt;
                    }
        
                    img {
                    border: 0;
                    height: auto;
                    line-height: 100%;
                    outline: none;
                    text-decoration: none;
                    -ms-interpolation-mode: bicubic;
                    }
        
                    p {
                    display: block;
                    margin: 13px 0;
                    }
                </style>
                <!--[if mso]>
                        <noscript>
                        <xml>
                        <o:OfficeDocumentSettings>
                        <o:AllowPNG/>
                        <o:PixelsPerInch>96</o:PixelsPerInch>
                        </o:OfficeDocumentSettings>
                        </xml>
                        </noscript>
                        <![endif]-->
                <!--[if lte mso 11]>
                        <style type="text/css">
                        .mj-outlook-group-fix { width:100% !important; }
                        </style>
                        <![endif]-->
                <!--[if !mso]><!-->
                <link href="https://fonts.googleapis.com/css?family=Ubuntu:300,400,500,700" rel="stylesheet" type="text/css">
                <style type="text/css">
                    @import url(https://fonts.googleapis.com/css?family=Ubuntu:300,400,500,700);
                </style>
                <!--<![endif]-->
                <style type="text/css">
                    @media only screen and (min-width:480px) {
                    .mj-column-per-100 {
                        width: 100% !important;
                        max-width: 100%;
                    }
                    }
                </style>
                <style media="screen and (min-width:480px)">
                    .moz-text-html .mj-column-per-100 {
                    width: 100% !important;
                    max-width: 100%;
                    }
                </style>
                <style type="text/css">
                    @media only screen and (max-width:480px) {
                    table.mj-full-width-mobile {
                        width: 100% !important;
                    }
        
                    td.mj-full-width-mobile {
                        width: auto !important;
                    }
                    }
                </style>
            </head>
    
            <body style="word-spacing:normal;">
            <div style="">
                <!--[if mso | IE]><table align="center" border="0" cellpadding="0" cellspacing="0" class="" style="width:600px;" width="600" ><tr><td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;"><![endif]-->
                <div style="margin:0px auto;max-width:600px;">
                <table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="width:100%;">
                    <tbody>
                    <tr>
                        <td style="direction:ltr;font-size:0px;padding:20px 0;text-align:center;">
                        <!--[if mso | IE]><table role="presentation" border="0" cellpadding="0" cellspacing="0"><tr><td class="" style="vertical-align:top;width:600px;" ><![endif]-->
                        <div class="mj-column-per-100 mj-outlook-group-fix" style="font-size:0px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:100%;">
                            <table border="0" cellpadding="0" cellspacing="0" role="presentation" style="vertical-align:top;" width="100%">
                            <tbody>
                                <tr>
                                <td align="center" style="font-size:0px;padding:10px 25px;word-break:break-word;">
                                    <table border="0" cellpadding="0" cellspacing="0" role="presentation" style="border-collapse:collapse;border-spacing:0px;">
                                    <tbody>
                                        <tr>
                                        <td style="width:200px;">
                                            <img height="auto" src="https://fastgrades.net/wp-content/uploads/2022/01/cropped-logo.png" style="border:0;display:block;outline:none;text-decoration:none;height:auto;width:100%;font-size:13px;" width="200" />
                                        </td>
                                        </tr>
                                    </tbody>
                                    </table>
                                </td>
                                </tr>
                                <tr>
                                <td align="center" style="font-size:0px;padding:10px 25px;word-break:break-word;">
                                    <p style="border-top:solid 4px #042D59;font-size:1px;margin:0px auto;width:100%;">
                                    </p>
                                                                <!--[if mso | IE]><table align="center" border="0" cellpadding="0" cellspacing="0" style="border-top:solid 4px #042D59;font-size:1px;margin:0px auto;width:550px;" role="presentation" width="550px" ><tr><td style="height:0;line-height:0;"> &nbsp;
                                        </td></tr></table><![endif]-->
                                </td>
                                </tr>
                                <tr>
                                <td align="center" style="font-size:0px;padding:10px 25px;word-break:break-word;">
                                    <div style="font-family:helvetica;font-size:32px;font-weight:700;line-height:1;text-align:center;color:#134478;">Registration Successful! <br/> Login Details</div>
                                </td>
                                </tr>
                                <tr>
                                <td align="center" style="font-size:0px;padding:10px 25px;word-break:break-word;">
                                    <div style="font-family:Ubuntu, Helvetica, Arial, sans-serif;font-size:18px;line-height:1;text-align:center;color:#000000;"> <?php echo $title; ?></div>
                                </td>
                                </tr>
    
                            </tbody>
                            </table>
                        </div>
                        <!--[if mso | IE]></td></tr></table><![endif]-->
                        </td>
                    </tr>
                    </tbody>
                </table>
                </div>
                <!--[if mso | IE]></td></tr></table><![endif]-->
            </div>
            </body>
    
        </html>
        
        <?php
    return ob_get_clean();
}


function pdlogreg_no_admin_access() {
    $redirect = isset( $_SERVER['HTTP_REFERER'] ) ? $_SERVER['HTTP_REFERER'] : home_url( '/sales-dashboard/' );
    global $current_user;
    $user_roles = $current_user->roles;
    $user_role = array_shift($user_roles);
    if($user_role === 'sales-representative'){
        exit( wp_redirect( home_url( '/sales-dashboard/' ) ) );
    }
}

add_action( 'admin_init', 'pdlogreg_no_admin_access', 100 );

function redirect_after_password_reset() {

    // Check if have submitted


    if( isset($_GET['action'] ) && $_GET['action'] == 'resetpass' ) {
        wp_redirect( home_url('signin') );
        exit;
    }
}
add_action('after_password_reset', 'redirect_after_password_reset');