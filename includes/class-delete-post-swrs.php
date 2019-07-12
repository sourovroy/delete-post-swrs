<?php
/**
 * The core plugin class.
 * @since      1.0
 * @package    delete-post-swrs
 * @subpackage delete-post-swrs/includes
 * @author     Soruov Roy <sourovfci@gmail.com>
 */
if( class_exists( 'Delete_Post_Swrs' ) ) {
	return;
}

class Delete_Post_Swrs
{
	// Property
	private static $instance = null;

	// Construct
	private function __construct()
	{
		// Admin Menu
		add_action('admin_menu', array($this, 'delete_post_settings_menu'));

		// Plugin action link
		add_filter('plugin_action_links_' . DPS_BASENAME, array($this, 'plugin_action_links'));

		// Add CSS amd JS
		add_action('admin_enqueue_scripts', [$this, 'script_file_link']);

		//AJAX Hook
		add_action('wp_ajax_swrs_delete_post_count', [$this, 'delete_post_count_ajax']);
		add_action('wp_ajax_swrs_delete_post_goto', [$this, 'delete_post_goto']);
	}// End of __construct

	// Settings menu for delete post
	public function delete_post_settings_menu()
	{
		add_options_page('Delete Post', 'Delete Post', 'manage_options', 'delete-post-swrs', array($this, 'setting_page_output'));
	}// End of delete_post_settings_menu

	private function __clone()
	{
        // Stopping Clonning of Object
    }// End of __clone

    private function __wakeup()
    {
        // Stopping unserialize of object
    }// End of __wakeup

    // Singleton Instance
    public static function getInstance()
    {
    	if( self::$instance == null ){
    		self::$instance = new Delete_Post_Swrs();
    	}
    	return self::$instance;
    }// End of getInstance

    // HTML output of settings page
    public function setting_page_output()
    {
    	?>
		<div class="wrap">
			<h1><?php _e('Delete Posts', 'delete-post-swrs'); ?></h1>
			<div class="dps-left-column">
				<form action="" method="post" id="dps-form">
					<table class="form-table dps-form-table">
						<tbody>
							<tr>
								<th scope="row">
									<label><?php _e('Select Post Type', 'delete-post-swrs'); ?></label>
								</th>
								<td>
									<select name="select_post_type">
										<?php
											$post_types = get_post_types([], 'objects');
											foreach ($post_types as $post_type):
										?>
										<option value="<?php echo $post_type->name; ?>"><?php echo $post_type->labels->name; ?></option>
										<?php endforeach; ?>
									</select>
									<p class="dps-desc">Choose post type.</p>
								</td>
							</tr>
							<tr>
								<th scope="row">
									<label><?php _e('Quantity of Post', 'delete-post-swrs'); ?></label>
								</th>
								<td>
									<select name="post_quantity">
										<option value="all"><?php _e('All', 'delete-post-swrs'); ?></option>
										<option value="50">50</option>
										<option value="100">100</option>
										<option value="200">200</option>
										<option value="500">500</option>
										<option value="1000">1000</option>
									</select>
									<p class="dps-desc">Choose how many post you want to delete.</p>
								</td>
							</tr>
							<tr>
								<th scope="row">
									<label><?php _e('Delete Type', 'delete-post-swrs'); ?></label>
								</th>
								<td>
									<select name="delete_type">
										<option value="trash"><?php _e('Move to trash', 'delete-post-swrs'); ?></option>
										<option value="permanent"><?php _e('Permanent delete', 'delete-post-swrs'); ?></option>
									</select>
								</td>
							</tr>
						</tbody>
					</table>
					<input type="hidden" name="_nonce" value="<?php echo wp_create_nonce('DeletePostNonce'); ?>">
					<p class="submit">
						<input type="button" id="start-delete" class="button button-primary" value="<?php _e('Process Delete', 'delete-post-swrs'); ?>">
					</p>
				</form>
			</div>
			<div class="dps-right-column">
				<div class="dps-loading">
					<span class="spinner"></span>
				</div>

				<div id="dps-show-result"></div>

				<?php
					// $count_pages = wp_count_posts('post');
					// print_r($count_pages);
				 ?>
			</div>
			<div class="clear"></div>
		</div>
    	<?php
    }// End of setting_page_output

    // Add new link in plugin list
    public function plugin_action_links($links)
    {
		$action_links = array(
			'settings' => '<a href="' . admin_url('options-general.php?page=delete-post-swrs') . '" aria-label="' . esc_attr__( 'View Delete Post settings', 'delete-post-swrs' ) . '">' . esc_html__( 'Settings', 'delete-post-swrs' ) . '</a>'
		);
		return array_merge($action_links, $links);
    }// End of plugin_action_links

    //Link CSS amd JS file
    public function script_file_link($hook)
    {
    	if('settings_page_delete-post-swrs' == $hook){
	    	wp_enqueue_style('dps-style', DPS_ROOT_URL . 'asstes/css/style.css', array(), null);

	    	wp_enqueue_script('dps-script', DPS_ROOT_URL . 'asstes/js/scripts.js', array(), null, true);

			wp_localize_script( 'dps-script', 'DPS_OBJ', ['ajax_url' => esc_url(admin_url('admin-ajax.php'))]);
    	}
    }// End of script_file_link

    //AJAX Hook function
    public function delete_post_count_ajax()
    {
    	parse_str($_POST['delete_data'], $delete_data_array);
    	$delete_data = array_map('sanitize_text_field', $delete_data_array);

    	if(wp_verify_nonce($delete_data['_nonce'], 'DeletePostNonce')){

    		$count_obj = wp_count_posts($delete_data['select_post_type']);
    		$process_array = [
    			'publish' => $count_obj->publish,
    			'draft' => $count_obj->draft,
    			'trash' => $count_obj->trash,
    			'inherit' => $count_obj->inherit,
    			'total_post' => $count_obj->publish + $count_obj->draft + $count_obj->inherit,
    			'error' => null
    		];

    		$final_array = array_merge($process_array, $delete_data);
    		echo wp_json_encode($final_array);
    	}else{
    		$wrong_person = ['error' => 'Do you want to hack me?'];
    		echo wp_json_encode($wrong_person);
    	}
    	wp_die();
    }// End of delete_post_count_ajax

    //Final delete posts
    public function delete_post_goto()
    {
    	$delete_data_array = $_POST['delete_data'];
    	$delete_data = array_map('sanitize_text_field', $delete_data_array);

    	if( wp_verify_nonce($delete_data['_nonce'], 'DeletePostNonce') ){ //Check nonce is correct or not

    		$per_page = ( isset($delete_data['prePage']) ) ? $delete_data['prePage'] : 10;
    		$force_delete = ($delete_data['delete_type'] == 'permanent') ? true : false;

    		$arg = array(
    			'post_type' => $delete_data['select_post_type'],
    			'posts_per_page' => $per_page,
    			'offset'=> 0,
    			'post_status' => 'any'
    		);
			$deleteID = '';
    		$your_posts = get_posts($arg);
    		if($your_posts){
				sleep(1); // Delay execution for 1 second

    			if( $delete_data['select_post_type'] == 'attachment' ){ //Check post type is media or not
    				foreach( $your_posts as $myproduct ){
				        wp_delete_attachment($myproduct->ID, true);
				        $deleteID .= $myproduct->ID. ', ';
				    }
    			}else{
    				foreach( $your_posts as $myproduct ){
				        wp_delete_post( $myproduct->ID, $force_delete);
				        $deleteID .= $myproduct->ID. ', ';
				    }
    			}// End if

			    echo wp_json_encode([
			    	'error' => null,
			    	'deleted' => count($your_posts),
			    	'deleted_ids' => $deleteID
			    ]);
    		}else{
	    		echo wp_json_encode(['error' => 'Something wrong here.']);
	    	}// End if

	    	wp_reset_query();

    	}else{
    		echo wp_json_encode(['error' => 'Do you want to hack me?']);
    	}// End if

    	wp_die();
    }// End of delete_post_goto

}// End of class

// Instantiate Class
Delete_Post_Swrs::getInstance();
