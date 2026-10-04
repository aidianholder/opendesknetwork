<?php
/**
 * "Join the network" applications.
 *
 * The odn/join-form block (main site only) posts here. Each application is
 * saved as a private odn_application post on the main site, visible to super
 * admins only, and emailed to the network inbox.
 *
 * @package ODN_Network
 */

defined( 'ABSPATH' ) || exit;

// Where applications are emailed. Override with the odn_join_recipient site option.
const ODN_JOIN_DEFAULT_RECIPIENT = 'opendesknetwork@gmail.com';

// Submissions allowed per IP address per hour.
const ODN_JOIN_RATE_LIMIT = 5;

/**
 * Form fields: name => [label, type, required].
 *
 * @return array<string,array>
 */
function odn_join_fields() {
	return array(
		'name'      => array( __( 'Your name', 'odn' ), 'text', true ),
		'email'     => array( __( 'Email', 'odn' ), 'email', true ),
		'subdomain' => array( __( 'Preferred subdomain', 'odn' ), 'text', false ),
		'beat'      => array( __( 'Beat or coverage area', 'odn' ), 'text', true ),
		'location'  => array( __( 'Where you report from', 'odn' ), 'text', false ),
		'links'     => array( __( 'Links to your work', 'odn' ), 'textarea', false ),
		'message'   => array( __( 'Tell us about your plans for your desk', 'odn' ), 'textarea', true ),
	);
}

/**
 * Registers the private applications post type on the main site.
 */
function odn_register_application_post_type() {
	if ( ! is_main_site() ) {
		return;
	}

	register_post_type(
		'odn_application',
		array(
			'labels'          => array(
				'name'          => __( 'Applications', 'odn' ),
				'singular_name' => __( 'Application', 'odn' ),
				'edit_item'     => __( 'Application', 'odn' ),
				'search_items'  => __( 'Search applications', 'odn' ),
				'not_found'     => __( 'No applications yet.', 'odn' ),
			),
			'public'          => false,
			'show_ui'         => true,
			'show_in_rest'    => false,
			'menu_icon'       => 'dashicons-id',
			'menu_position'   => 26,
			'supports'        => array( 'title', 'editor' ),
			'capability_type' => 'post',
			'capabilities'    => array(
				'create_posts'       => 'do_not_allow',
				'edit_posts'         => 'manage_network',
				'edit_others_posts'  => 'manage_network',
				'delete_posts'       => 'manage_network',
				'delete_others_posts' => 'manage_network',
				'read_private_posts' => 'manage_network',
				'publish_posts'      => 'manage_network',
			),
			'map_meta_cap'    => true,
		)
	);
}
add_action( 'init', 'odn_register_application_post_type' );

/**
 * Renders odn/join-form.
 *
 * @return string
 */
function odn_render_join_form() {
	if ( ! is_main_site() ) {
		return '';
	}

	$status  = isset( $_GET['odn_join'] ) ? sanitize_key( wp_unslash( $_GET['odn_join'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$wrapper = get_block_wrapper_attributes( array( 'id' => 'join-form' ) );

	if ( 'sent' === $status ) {
		return sprintf(
			'<div %s><div class="odn-join__notice is-success" role="status"><h2>%s</h2><p>%s</p></div></div>',
			$wrapper,
			esc_html__( 'Thanks — we got it.', 'odn' ),
			esc_html__( 'Someone from the network will be in touch by email.', 'odn' )
		);
	}

	$notice = '';
	if ( 'invalid' === $status ) {
		$notice = esc_html__( 'Please fill in every required field with a valid email address.', 'odn' );
	} elseif ( 'limited' === $status ) {
		$notice = esc_html__( 'Too many submissions from your connection. Please try again in an hour.', 'odn' );
	} elseif ( 'failed' === $status ) {
		$notice = esc_html__( 'Something went wrong sending your application. Please try again, or email us directly.', 'odn' );
	}

	$rows = '';
	foreach ( odn_join_fields() as $key => list( $label, $type, $required ) ) {
		$id    = 'odn-join-' . $key;
		$attrs = sprintf( 'id="%1$s" name="odn_%2$s"%3$s', esc_attr( $id ), esc_attr( $key ), $required ? ' required' : '' );

		if ( 'textarea' === $type ) {
			$input = sprintf( '<textarea %s rows="%d" maxlength="5000"></textarea>', $attrs, 'message' === $key ? 6 : 3 );
		} else {
			$extra = 'subdomain' === $key ? ' pattern="[a-z0-9-]{2,40}" placeholder="yourname"' : '';
			$input = sprintf( '<input type="%s" %s maxlength="200"%s />', esc_attr( $type ), $attrs, $extra );
		}

		$rows .= sprintf(
			'<p class="odn-join__field odn-join__field--%1$s"><label for="%2$s">%3$s%4$s</label>%5$s</p>',
			esc_attr( $key ),
			esc_attr( $id ),
			esc_html( $label ),
			$required ? ' <span class="odn-join__required" aria-hidden="true">*</span>' : '',
			$input
		);
	}

	return sprintf(
		'<div %1$s>%2$s<form class="odn-join" method="post" action="%3$s">
			<input type="hidden" name="action" value="odn_join" />
			<p class="odn-join__hp" aria-hidden="true"><label>Leave this empty <input type="text" name="odn_website" tabindex="-1" autocomplete="off" /></label></p>
			%4$s
			<p class="odn-join__submit wp-block-button"><button type="submit" class="wp-block-button__link wp-element-button">%5$s</button></p>
		</form></div>',
		$wrapper,
		$notice ? '<p class="odn-join__notice is-error" role="alert">' . $notice . '</p>' : '',
		esc_url( admin_url( 'admin-post.php' ) ),
		$rows,
		esc_html__( 'Send application', 'odn' )
	);
}

/**
 * Handles a submitted application.
 *
 * No nonce: the form is public and served from the page cache, so a nonce would
 * go stale. Spam is held off by a honeypot field and a per-IP rate limit.
 */
function odn_handle_join() {
	// phpcs:disable WordPress.Security.NonceVerification.Missing
	$back = home_url( '/join/' );
	$ref  = wp_get_referer();
	if ( $ref && wp_parse_url( $ref, PHP_URL_HOST ) === wp_parse_url( home_url(), PHP_URL_HOST ) ) {
		$back = strtok( $ref, '?#' );
	}
	$redirect = function ( $status ) use ( $back ) {
		wp_safe_redirect( add_query_arg( 'odn_join', $status, $back ) . '#join-form', 303 );
		exit;
	};

	if ( ! is_main_site() ) {
		$redirect( 'failed' );
	}

	// Bots fill the honeypot; pretend it worked.
	if ( ! empty( $_POST['odn_website'] ) ) {
		$redirect( 'sent' );
	}

	$ip_key = 'odn_join_' . md5( isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '' );
	$count  = (int) get_transient( $ip_key );
	if ( $count >= ODN_JOIN_RATE_LIMIT ) {
		$redirect( 'limited' );
	}

	$data = array();
	foreach ( odn_join_fields() as $key => list( $label, $type, $required ) ) {
		$raw   = isset( $_POST[ 'odn_' . $key ] ) ? wp_unslash( $_POST[ 'odn_' . $key ] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$value = 'textarea' === $type ? sanitize_textarea_field( $raw ) : sanitize_text_field( $raw );
		$value = mb_substr( $value, 0, 'textarea' === $type ? 5000 : 200 );
		if ( 'email' === $type ) {
			$value = sanitize_email( $value );
		}
		if ( $required && ( '' === $value || ( 'email' === $type && ! is_email( $value ) ) ) ) {
			$redirect( 'invalid' );
		}
		$data[ $key ] = $value;
	}
	// phpcs:enable

	set_transient( $ip_key, $count + 1, HOUR_IN_SECONDS );

	$body = '';
	foreach ( odn_join_fields() as $key => list( $label ) ) {
		$body .= $label . ":\n" . ( '' !== $data[ $key ] ? $data[ $key ] : '—' ) . "\n\n";
	}

	$post_id = wp_insert_post(
		array(
			'post_type'    => 'odn_application',
			'post_status'  => 'private',
			'post_title'   => $data['name'] . ' — ' . $data['beat'],
			'post_content' => $body,
		)
	);

	$recipient = get_site_option( 'odn_join_recipient', ODN_JOIN_DEFAULT_RECIPIENT );
	$subject   = sprintf( '[Open Desk Network] Application from %s', $data['name'] );
	$headers   = array( sprintf( 'Reply-To: %s <%s>', str_replace( array( '<', '>', '"', ',' ), '', $data['name'] ), $data['email'] ) );
	$footer    = $post_id ? "\n--\nSaved on the network: " . admin_url( 'post.php?post=' . $post_id . '&action=edit' ) . "\n" : '';
	$mailed    = wp_mail( $recipient, $subject, $body . $footer, $headers );

	$redirect( $post_id || $mailed ? 'sent' : 'failed' );
}
add_action( 'admin_post_nopriv_odn_join', 'odn_handle_join' );
add_action( 'admin_post_odn_join', 'odn_handle_join' );
