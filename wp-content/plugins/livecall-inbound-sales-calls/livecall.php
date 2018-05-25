<?php
/*
Plugin Name: LiveCall
Plugin URI: http://livecall.io/
Description: LiveCall enables you to talk by voice with people who are currently visiting your website.
Author: LiveCall
Version: 1.0.2
Author URI: http://livecall.io/
*/


function livecall_add_js() {
	if (!is_admin()) {
		$livecall_settings = get_option('livecall_settings');
		printf('<script async src="https://assets.livecall.io/accounts/%s/widget.js"></script>',
			$livecall_settings['livecall_id']);
	}
}
add_action('wp_footer', 'livecall_add_js', 25);

// PLUGIN OPTIONS
add_action('admin_menu', 'livecall_add_admin_menu');
add_action('admin_init', 'livecall_settings_init');


function livecall_add_admin_menu() {
	add_submenu_page('options-general.php', 'LiveCall', 'LiveCall', 'manage_options', 'livecall', 'livecall_options_page');
}

function livecall_settings_validation($input) {
	$input['livecall_id'] = str_replace(array('+', '-'), '', filter_var($input['livecall_id'], FILTER_SANITIZE_NUMBER_INT));

	return $input;
}

function livecall_settings_init() {
	register_setting('livecall_settings', 'livecall_settings', 'livecall_settings_validation');

	add_settings_section(
		'livecall_id_options',
		__( 'Basic plugin configuration', 'livecall' ),
		'livecall_id_section_callback',
		'livecall_settings'
	);

	add_settings_field(
		'livecall_id',
		__( 'Your Account ID', 'livecall' ),
		'livecall_id_render',
		'livecall_settings',
		'livecall_id_options'
	);
}


function livecall_id_render() {

	$options = get_option('livecall_settings');
	?>
	<input type="text" name="livecall_settings[livecall_id]" value="<?php echo $options['livecall_id']; ?>" pattern="^[0-9]+$">
	<?php
}


function livecall_id_section_callback() {
	echo __('To start using LiveCall on your WordPress-based site, you only have to enter your Account ID into the field below.', 'livecall' );
	echo '<h4>' . __('How to find the Account ID?', 'livecall') . '</h4>';
	echo '<ul style="list-style: circle; padding-left: 30px;">';
	echo '<li>' . ' <a href="https://app.livecall.io/registrations/new" target="_blank">' . __('Create a LiveCall account', 'livecall') . '</a> ' . __('or', 'livecall') . ' <a href="https://app.livecall.io/log-in" target="_blank">' . __('log in', 'livecall') . '</a> ' . __('if you already have one', 'livecall') . '</li>';
	echo '<li>' . __('Go to Settings', 'livecall') . '</li>';
	echo '<li>' . __('Find title: "Account #"', 'livecall') . '</li>';
	echo '<li>' . __('Next to it there is your Account ID (ex. for "Account #48" Account ID is 48)', 'livecall') . '</li>';
	echo '</ul>';
}


function livecall_options_page() {
?>
	<form action='options.php' method='post'>

		<h2><a href="http://livecall.io" title="LiveCall"><img src="<?php echo plugins_url('logo.svg', __FILE__); ?>" alt="LiveCall"></a></h2>

		<?php
			settings_fields('livecall_settings');
			do_settings_sections('livecall_settings');
			submit_button();
		?>

	</form>
<?php
}
?>
