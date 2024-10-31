<?php


class ops_email
{

    /* STATIC */
    var $logo = '';
    var $logo_width = '190px';
    var $logo_height = 'auto';
    var $footer_message = 'With passion from Off Page SEO.';
    var $site_url = '';
    var $link_color = '#00a0d2';
    var $email_from_name = 'Off Page SEO plugin';
    var $email_from = '';

    /* DYNAMIC */
    var $body = '';

    /* EMAIL */
    var $email_attachments = [];

    function __construct()
    {
        $this->logo = plugins_url('off-page-seo/img/logo.png');
        $this->site_url = get_admin_url() . 'admin.php?page=ops_dashboard';
        $home_url = get_home_url();
        $parsed_home_url = parse_url($home_url);

        $this->email_from = 'noreply@' . $parsed_home_url['host'];

    }

    function example()
    {
        ob_start();
        ?>

        <?php
        $message = ob_get_contents();
        ob_end_clean();
        $email = new ops_email();
        $email->set_body($message);
        $email->send_email('to', 'Subject');
    }

    function send_email($to, $subject, $type = 'email', $type_id = false)
    {
        $message = $this->get_email_body();
        $headers[] = 'MIME-Version: 1.0' . "\n";
        $headers[] = 'Content-type: text/html; charset=utf-8' . "\n";
        $headers[] = "X-Mailer: PHP \n";
        $headers[] = 'From: ' . $this->email_from_name . ' <' . $this->email_from . '>' . "\n";
        // customer
        $mail = wp_mail($to, $subject, $message, $headers, $this->email_attachments);

        if (is_array($to)) {
            $to = implode(',', $to);
        }

        if ($mail == true) {
            if (class_exists('NT_Log')) NT_Log::create_log_entry('admin', 'info', 'Email sent: ' . $to . ', subject: ' . $subject, $type, $type_id);
            return true;
        } else {
            if (class_exists('NT_Log')) NT_Log::create_log_entry('admin', 'error', 'Error sending email to: ' . $to . ', subject: ' . $subject, $type, $type_id);
            return false;
        }
    }

    function get_email_body()
    {
        ob_start();
        ?>
        <!doctype html>
        <html>
        <head>
            <meta name="viewport" content="width=device-width">
            <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
            <title></title>
            <style>
                /* -------------------------------------
                    INLINED WITH htmlemail.io/inline
                ------------------------------------- */
                /* -------------------------------------
                    RESPONSIVE AND MOBILE FRIENDLY STYLES
                ------------------------------------- */
                @media only screen and (max-width: 620px) {
                    table[class=body] h1 {
                        font-size: 28px !important;
                        margin-bottom: 10px !important;
                    }

                    table[class=body] p,
                    table[class=body] ul,
                    table[class=body] ol,
                    table[class=body] td,
                    table[class=body] span,
                    table[class=body] a {
                        font-size: 16px !important;
                    }

                    table[class=body] .wrapper,
                    table[class=body] .article {
                        padding: 10px !important;
                    }

                    table[class=body] .content {
                        padding: 0 !important;
                    }

                    table[class=body] .container {
                        padding: 0 !important;
                        width: 100% !important;
                    }

                    table[class=body] .main {
                        border-left-width: 0 !important;
                        border-radius: 0 !important;
                        border-right-width: 0 !important;
                    }

                    table[class=body] .btn table {
                        width: 100% !important;
                    }

                    table[class=body] .btn a {
                        width: 100% !important;
                    }

                    table[class=body] .img-responsive {
                        height: auto !important;
                        max-width: 100% !important;
                        width: auto !important;
                    }
                }

                /* -------------------------------------
                    PRESERVE THESE STYLES IN THE HEAD
                ------------------------------------- */
                @media all {
                    .ExternalClass {
                        width: 100%;
                    }

                    .ExternalClass,
                    .ExternalClass p,
                    .ExternalClass span,
                    .ExternalClass font,
                    .ExternalClass td,
                    .ExternalClass div {
                        line-height: 100%;
                    }

                    .apple-link a {
                        color: inherit !important;
                        font-family: inherit !important;
                        font-size: inherit !important;
                        font-weight: inherit !important;
                        line-height: inherit !important;
                        text-decoration: none !important;
                    }

                    .btn-primary table td:hover {
                        background-color: #34495e !important;
                    }

                    .btn-primary a:hover {
                        background-color: #34495e !important;
                        border-color: #34495e !important;
                    }
                }

                p {
                    font-size: 14px;
                    margin: 15px 0;
                    font-family: sans-serif;
                }

                table th {
                    font-weight: normal;
                }

                table th,
                table td {
                    padding: 5px 0;
                    text-align: left;
                    font-size: 14px;
                }

                a {
                    color: <?php echo $this->link_color ?>;
                    text-decoration: none;
                    font-weight: bold;
                }
            </style>
        </head>
        <body class="" style="background-color: #f6f6f6; font-family: sans-serif; -webkit-font-smoothing: antialiased; font-size: 14px; line-height: 1.4; margin: 0; padding: 0; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;">
        <table border="0" cellpadding="0" cellspacing="0" class="body" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%; background-color: #f6f6f6;">

            <tr>
                <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;">&nbsp;</td>
                <td class="container" style="font-family: sans-serif; font-size: 14px; vertical-align: top; display: block; Margin: 0 auto; max-width: 580px; padding: 10px; width: 580px;">
                    <div class="content" style="box-sizing: border-box; display: block; Margin: 0 auto; max-width: 580px; padding: 10px;">

                        <!-- START CENTERED WHITE CONTAINER -->
                        <span class="preheader" style="color: transparent; display: none; height: 0; max-height: 0; max-width: 0; opacity: 0; overflow: hidden; mso-hide: all; visibility: hidden; width: 0;">This is preheader text. Some clients will show this text as a preview.</span>
                        <table class="main" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;  border-radius: 3px;">
                            <tr>
                                <td class="container">
                                    <a href="<?php echo $this->site_url ?>" target="_blank">
                                        <img src="<?php echo $this->logo ?>" alt="Off Page SEO" style="width: <?php echo $this->logo_width ?>; height: <?php echo $this->logo_height ?>;">
                                    </a>
                                </td>
                            </tr>
                        </table>
                        <table class="main" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%; background: #ffffff; border-radius: 3px;">

                            <!-- START MAIN CONTENT AREA -->
                            <tr>
                                <td class="wrapper" style="font-family: sans-serif; font-size: 14px; vertical-align: top; box-sizing: border-box; padding: 20px;">
                                    <table border="0" cellpadding="0" cellspacing="0" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;">
                                        <tr>
                                            <td style="font-family: sans-serif; font-size: 14px; vertical-align: top; line-height: 1.4;">
                                                <?php echo $this->body; ?>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>

                            <!-- END MAIN CONTENT AREA -->
                        </table>

                        <!-- START FOOTER -->
                        <div class="footer" style="clear: both; Margin-top: 10px; text-align: center; width: 100%;">
                            <table border="0" cellpadding="0" cellspacing="0" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;">
                                <tr>
                                    <td class="content-block" style="font-family: sans-serif; vertical-align: top; padding-bottom: 10px; padding-top: 10px; font-size: 12px; color: #999999; text-align: center;">
                                        <span class="apple-link" style="color: #999999; font-size: 12px; text-align: center;">
                                            <?php echo $this->footer_message ?>
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <!-- END FOOTER -->

                        <!-- END CENTERED WHITE CONTAINER -->
                    </div>
                </td>
                <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;">&nbsp;</td>
            </tr>
        </table>
        </body>
        </html>

        <?php
        $html = ob_get_contents();
        ob_clean();
        return $html;
    }

    function set_body($body = '')
    {
        $this->body = $body;
    }

    function set_attachment($path)
    {
        $this->email_attachments[] = $path;
    }


}











