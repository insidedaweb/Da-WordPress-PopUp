<?php
/*
Plugin Name: IWB Mailchimp
Description: Manage your Mailchimp account
Version: 1.0
Author: Inside da Web
Author URI: http://www.insidedaweb.com/
*/

if ( !class_exists( 'MCAPI_iwb' ) )
{
	require_once 'inc/MCAPI.class.php';
}

add_action( 'admin_menu', 'iwbmc_settings' );

function iwbmc_settings(){
	register_setting( 'iwbmc_settings', 'iwbmc_api_key' );
	register_setting( 'iwbmc_settings', 'iwbmc_confirmation' );
	register_setting( 'iwbmc_settings', 'iwbmc_api_list');
}

add_action('wp_ajax_iwbmc_subscribe', 'iwbmc_subscribe_callback');

function iwbmc_subscribe_callback(){
	$output = iwbmc_subscribe( $_POST['email'], $_POST['name'], 'Test' );
	echo $output;
	die();
}

function iwbmc_subscribe( $email, $name, $list ){
	$iwbmc_api = new MCAPI_iwb( get_option( 'iwbmc_api_key' ) );
	$retval = $iwbmc_api->lists(array('list_name' => $list), 0, 1);
	$iwbmc_confirmation = true;
	if( get_option( 'iwbmc_confirmation' ) == 'on' ){
		$iwbmc_confirmation = false;
	}
	$merge_vars = array('FNAME'=>$name);
	foreach ($retval['data'] as $list){
		$retval_2 = $iwbmc_api->listSubscribe( $list['id'], $email, $merge_vars, 'html', $iwbmc_confirmation, false, true, false );
	}
	if ($iwbmc_api->errorCode){
		// return $iwbmc_api->errorMessage;
		return 0;
	} else {
	    return 1;
	}
}

add_action('admin_menu', 'iwbmc_add_sett_page');
function iwbmc_add_sett_page(){
	add_submenu_page( 'edit.php?post_type=da-popup', __( 'IWB Mailchimp', "iwbmc" ), __( 'IWB Mailchimp', "iwbmc" ), 'activate_plugins', 'iwbmc-settings', 'iwbmc_settings_page' );
}

function iwbmc_settings_page(){
	$iwbmc_api = new MCAPI_iwb( get_option( 'iwbmc_api_key' ) );
	?>
	<style>
	.iwbmc-stat-table{
		border-collapse: collapse;
	}
	.iwbmc-stat-table td, th{
		border: 1px solid #ccc;
		padding: 5px 10px;
	}
	.iwbmc-stat-table th{
		color: #888;
	}
	.iwbmc-line{
		border-bottom: 1px solid #ccc;
		margin: 20px 0;
	}
	</style>
	<div class="wrap">
		<?php screen_icon('options-general'); ?><h2><?php _e( 'IWB Mailchimp', "iwbmc" ); ?></h2>
		<form action="options.php" method="post">
			<?php settings_fields( 'iwbmc_settings' ); ?>
			<br>
			<label for="iwbmc_api_key">API Key: </label><input type="text" id="iwbmc_api_key" name="iwbmc_api_key" value="<?php echo esc_attr( get_option( 'iwbmc_api_key' ) ); ?>" style="width: 250px;">
			<label for="iwbmc_api_list">List Name: </label><input type="text" id="iwbmc_api_list" name="iwbmc_api_list" style="width: 250px;" value="<?php echo get_option('iwbmc_api_list'); ?>">
			<br><br>
			<input type="checkbox" id="iwbmc_confirmation" name="iwbmc_confirmation" <?php
			if( get_option( 'iwbmc_confirmation' ) == 'on' ){
				echo 'checked';
			}
			?>> <label for="iwbmc_confirmation">Don't send confirmation e-mail on subscribe</label>
			<br><br>
			<input type="submit" class="button-primary" value="<?php _e( "Update", "iwbmc" ); ?>" >
		</form>

		<div class="iwbmc-line"></div>
		
		<!-- LISTS -->

		<div class="iwbmc_lists">
			<h3><?php _e( "Lists", "iwbmc" ); ?></h3>
			<?php
			$retval = $iwbmc_api->lists();

			if ($iwbmc_api->errorCode){
				echo "Unable to load lists()!";
				echo "\n\tCode=".$api->errorCode;
				echo "\n\tMsg=".$api->errorMessage."\n";
			} else {
				echo "Number of your subscriber lists:".$retval['total']."<br><br>";
				echo '<table class="iwbmc-stat-table">';
				echo '<tr>';
				echo '<th>ID</th>';
				echo '<th>Name</th>';
				echo '<th>Members</th>';
				echo '<th>Unsubscribe</th>';
				echo '<th>Cleaned</th>';
				echo '</tr>';
				foreach ($retval['data'] as $list){
					echo '<tr>';
					echo '<td>'.$list['id'].'</td>';
					echo '<td>'.$list['name'].'</td>';
					echo '<td>'.$list['stats']['member_count'].'</td>';
					echo '<td>'.$list['stats']['unsubscribe_count'].'</td>';
					echo '<td>'.$list['stats']['cleaned_count'].'</td>';
					echo '</tr>';
				}
				echo '</table>';
			}
			?>
		</div>

		<div class="iwbmc-line"></div>

		<div class="iwbmc_lists">
			<h3><?php _e( "Members", "iwbmc" ); ?></h3>
			<?php
			$retval = $iwbmc_api->lists();

			if ($iwbmc_api->errorCode){
				echo "Unable to load lists()!";
				echo "\n\tCode=".$api->errorCode;
				echo "\n\tMsg=".$api->errorMessage."\n";
			} else {
				foreach ($retval['data'] as $list){
					echo '<h4>'.$list['name'].' ('.$list['stats']['member_count'].'):</h4>';
					if($list['stats']['member_count']>0){
						echo '<table class="iwbmc-stat-table">';
						echo '<tr>';
						echo '<th>Email</th>';
						echo '<th>Time</th>';
						echo '</tr>';
						$retval_2 = $iwbmc_api->listMembers($list['id'], 'subscribed', null, 0, 5000 );
						foreach($retval_2['data'] as $member){  
							echo '<tr>';
							echo '<td>'.$member['email'].'</td>';
							echo '<td>'.$member['timestamp'].'</td>';
							echo '</tr>';
						}
						echo '</table>';
					}else{
						echo 'Empty.';
					}
				} // end for each list
				
			}

			?>
		</div>
	</div> <!-- .wrap -->
	<?php
}

// function iwbmc_never_login_update( $email ){
// 	$list = "revendeurs"
// 	if( !is_object( $iwbmc_api ) ){
// 		$iwbmc_api = new MCAPI_iwb( get_option( 'iwbmc_api_key' ) );
// 	}
// 	$retval = $iwbmc_api->lists(array('list_name' => $list), 0, 1);
// 	$merge_vars = array("NEVERLOGIN"=>"1");

// 	foreach ($retval['data'] as $list){
// 		$retval_2 = $iwbmc_api->listUpdateMember( $list['id'], $email, $merge_vars );
// 	}
// 	if ($iwbmc_api->errorCode){
// 		return $iwbmc_api->errorMessage;
// 	} else {
// 	    return true;
// 	}
// }

// function iwbmc_user_exist_by_ID(){
// 	global $wpdb;
// 	$count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $wpdb->users WHERE ID = '$user_ID'"));
// 	if( $count == 1 ){
// 		return true;
// 	}else{
// 		return false;
// 	}
// }

// function iwbmc_never_login_check(){
// 	global $wpdb;
//     $reseler_table = $wpdb->prefix . "resellers";
//     $resellers = $wpdb->get_results( 'SELECT user_id, username FROM '.$reseler_table.' WHERE verified="1" AND user_id > 0');
//     foreach ( $resellers as $reseller ){
//     	if( iwbmc_user_exist_by_ID( $reseller->user_id ) ){
// 			$last_login = get_user_meta( $reseller->user_id, 'last_login', true );
// 			echo '<br>User '. $reseller->username.' is connected on: '.$last_login;
// 			if( false ){
// 				iwbmc_never_login_update( $email );
// 			}
// 		}
//     }
    
// }
// add_action("init", "iwbmc_never_login_check");

// add_action('admin_menu', 'iwbmc_add_sett_page');
// function iwbmc_add_sett_page(){
// 	add_submenu_page( 'options-general.php', __( 'IWB Mailchimp resellers', "iwbmc" ), __( 'IWB Mailchimp resellers', "iwbmc" ), 'activate_plugins', 'iwbmc-resellers', 'iwbmc_resellers' );
// }
// function iwbmc_resellers(){

// }
?>