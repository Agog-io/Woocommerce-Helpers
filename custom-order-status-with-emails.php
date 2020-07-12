<?php
/**

emails/customer-order-template.php

-----------------


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

<p><?php printf( esc_html__( 'Hi %s,', 'woocommerce' ), esc_html( $order->get_billing_first_name() ) ); ?></p>
<p><?php echo $email_text; ?></p>

<?php
if( $showdet ) do_action( 'woocommerce_email_order_details', $order, $sent_to_admin, $plain_text, $email );


if( $showprod )do_action( 'woocommerce_email_order_meta', $order, $sent_to_admin, $plain_text, $email );

echo $email_text2;

if( $showcust )do_action( 'woocommerce_email_customer_details', $order, $sent_to_admin, $plain_text, $email );

if ( $additional_content ) {
	echo wp_kses_post( wpautop( wptexturize( $additional_content ) ) );
}

do_action( 'woocommerce_email_footer', $email );

-----------------
Its the templates/emails/customer-completed-order.php file in woocommerce folder + some modifs
**/
//Custom funct to use more flexible email template.
function get_custom_order_email_html( $order, $heading = false, $mailer ,$emailtext ,$showdet = 1 , $showprod = 1 , $showcust = 1 , $emailtext2="" ) {
	$template = 'emails/customer-order-template.php';
	return wc_get_template_html( $template, array(
		'order'         => $order,
		'email_text'    => $emailtext,
		'email_text2'    => $emailtext2,
		'email_heading' => $heading,
		'sent_to_admin' => false,
		'plain_text'    => false,
		'email'         => $mailer,
		'showdet' => $showdet,
		'showprod'    => $showprod,
		'showcust'         => $showcust
	) );
}
//register the status.
add_action('init', 'register_custom_order_status', 10);
function register_custom_order_status()
{
    register_post_status('wc-customstatus', array(
        'label' => 'Cstm Status',
        'public' => true,
        'exclude_from_search' => false,
        'show_in_admin_all_list' => true,
        'show_in_admin_status_list' => true,
        'label_count' => _n_noop('Cstm Status <span class="count">(%s)</span>', 'Cstm Status <span class="count">(%s)</span>', 'woocommerce')
    ));

}
//add the status to menus.
add_filter('wc_order_statuses', 'custom_wc_order_statuses');
function custom_wc_order_statuses($order_statuses)
{
    $order_statuses['wc-customstatus']  = 'Cstm Status';
}

add_action('woocommerce_order_status_changed', 'status_custom_notification', 10, 4);
function status_custom_notification($order_id, $from_status, $to_status, $order)
{
	if ($order->has_status('customstatus')) {
		$mailer    = WC()->mailer();
        /*format the email*/
        $emailtext = "text" .  $order->get_id() . "text2";
		$emailtext2 = "text3";
		$subject   = "subjecttext";
        $content   = get_custom_order_email_html($order, $subject, $mailer, $emailtext , 1 , 1 ,1 , $emailtext2);
        $headers   = "Content-Type: text/html\r\n";
        /*send the email through wordpress*/
        $mailer->send($order->get_billing_email(), $subject, $content, $headers);
    }
}
?>