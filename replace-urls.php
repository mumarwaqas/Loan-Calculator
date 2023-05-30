<?php
    // check user capabilities
    if ( ! current_user_can( 'manage_options' ) ) {
    	wp_die( __('You do not have sufficient permissions to access this page.') );
    }

	if ( !function_exists( 'calc_update_urls' ) ) 
	{
		function calc_update_urls($options, $oldurl, $newurl)
		{
			global $wpdb;
			$results = array();
			$queries = array(
							'content' =>		array("UPDATE $wpdb->posts SET post_content = replace(post_content, %s, %s)",  __('Content Items (Posts, Pages, Custom Post Types, Revisions)','system-configuration') ),
							'excerpts' =>		array("UPDATE $wpdb->posts SET post_excerpt = replace(post_excerpt, %s, %s)", __('Excerpts','system-configuration') ),
							'attachments' =>	array("UPDATE $wpdb->posts SET guid = replace(guid, %s, %s) WHERE post_type = 'attachment'",  __('Attachments','system-configuration') ),
							'links' =>			array("UPDATE $wpdb->links SET link_url = replace(link_url, %s, %s)", __('Links','system-configuration') ),
							'custom' =>			array("UPDATE $wpdb->postmeta SET meta_value = replace(meta_value, %s, %s)",  __('Custom Fields','system-configuration') ),
							'guids' =>			array("UPDATE $wpdb->posts SET guid = replace(guid, %s, %s)",  __('GUIDs','system-configuration') )
						);
			foreach($options as $option)
			{
				if( $option == 'custom' )
				{
					$n = 0;
					$row_count = $wpdb->get_var( "SELECT COUNT(*) FROM $wpdb->postmeta" );
					$page_size = 10000;
					$pages = ceil( $row_count / $page_size );
					
					for( $page = 0; $page < $pages; $page++ ) 
					{
						$current_row = 0;
						$start = $page * $page_size;
						$end = $start + $page_size;
						$pmquery = "SELECT * FROM $wpdb->postmeta WHERE meta_value <> ''";
						$items = $wpdb->get_results( $pmquery );
						foreach( $items as $item )
						{
							$value = $item->meta_value;
							if( trim($value) == '' )
							{
								continue;
							
								$edited = calc_unserialize_replace( $oldurl, $newurl, $value );
							
								if( $edited != $value )
								{
									$fix = $wpdb->query("UPDATE $wpdb->postmeta SET meta_value = '".$edited."' WHERE meta_id = ".$item->meta_id );
									if( $fix )
										$n++;
								}
							}
						}
					}
					$results[$option] = array($n, $queries[$option][1]);
				}
				else
				{
					$result = $wpdb->query( $wpdb->prepare( $queries[$option][0], $oldurl, $newurl) );
					$results[$option] = array($result, $queries[$option][1]);
				}
			}
			return $results;			
		}
	}
	if ( !function_exists( 'calc_unserialize_replace' ) ) 
	{
		function calc_unserialize_replace( $from = '', $to = '', $data = '', $serialised = false ) {
			try {
				if ( false !== is_serialized( $data ) ) 
				{
					$unserialized = unserialize( $data );
					$data = calc_unserialize_replace( $from, $to, $unserialized, true );
				}
				elseif ( is_array( $data ) ) 
				{
					$_tmp = array( );
					foreach ( $data as $key => $value ) 
					{
						$_tmp[ $key ] = calc_unserialize_replace( $from, $to, $value, false );
					}
					$data = $_tmp;
					unset( $_tmp );
				}
				else 
				{
					if ( is_string( $data ) )
						$data = str_replace( $from, $to, $data );
				}
				if ( $serialised )
					return serialize( $data );
			} catch( Exception $error ) {

			}
			return $data;
		}
	}

	if( isset( $_POST['calc_settings_submit'] ) && !isset( $_POST['update_links'] ) )
	{
		if(isset($_POST['oldurl']) && isset($_POST['newurl']))
		{
			if(function_exists('esc_attr'))
			{
				$oldurl = esc_attr(trim($_POST['oldurl']));
				$newurl = esc_attr(trim($_POST['newurl']));
			}
			else
			{
				$oldurl = attribute_escape(trim($_POST['oldurl']));
				$newurl = attribute_escape(trim($_POST['newurl']));
			}
		}
		echo '<div id="message" class="error fade"><p><strong>'.__('ERROR','calc-system-configuration').' - '.__('Your URLs have not been updated.','calc-system-configuration').'</p></strong><p>'.__('Please select at least one checkbox.','calc-system-configuration').'</p></div>';
	}
	elseif( isset( $_POST['calc_settings_submit'] ) )
	{
		$update_links = $_POST['update_links'];
		if(isset($_POST['oldurl']) && isset($_POST['newurl']))
		{
			if(function_exists('esc_attr'))
			{
				$oldurl = esc_attr(trim($_POST['oldurl']));
				$newurl = esc_attr(trim($_POST['newurl']));
			}
			else
			{
				$oldurl = attribute_escape(trim($_POST['oldurl']));
				$newurl = attribute_escape(trim($_POST['newurl']));
			}
		}

		if(($oldurl && $oldurl != 'http://www.oldurl.com' && trim($oldurl) != '') && ($newurl && $newurl != 'http://www.newurl.com' && trim($newurl) != ''))
		{
			$results = calc_update_urls($update_links, $oldurl, $newurl);
			$empty = true;

			$resultstring = '';
			foreach($results as $result)
			{
				$empty = ($result[0] != 0 || $empty == false)? false : true;
				$resultstring .= '<br/><strong>'.$result[0].'</strong> '.$result[1];
			}
			if( $empty )
			{
			?>
			<div id="message" class="error fade">
				<p><strong><?php _e('ERROR: Something may have gone wrong.','calc-system-configuration'); ?></strong><br/><?php _e('Your URLs have not been updated.','calc-system-configuration'); ?></p>
			</div>
			<?php
			}
			else
			{
			?>
			<div id="message" class="updated fade">
				<p><strong><?php _e('Success! Your URLs have been updated.','calc-system-configuration'); ?></strong></p><p><?php _e('Results','calc-system-configuration'); ?><?php echo $resultstring; ?></p>
			</div>
			<?php
			}
			?>
		<?php
		}
		else
		{
			echo '<div id="message" class="error fade"><p><strong>'.__('ERROR','calc-system-configuration').' - '.__('Your URLs have not been updated.','calc-system-configuration').'</p></strong><p>'.__('Please enter values for both the old url and the new url.','calc-system-configuration').'</p></div>';
		}
	}
?>

	<div class="wrap">
		<h1 class="wp-heading-inline"><?php esc_html_e( get_admin_page_title(), 'calc-system-configuration' ); ?></h1>
		<p><?php printf(__("After moving a website, %s lets you fix old URLs in content, excerpts, links, and custom fields.",'calc-system-configuration'),'<strong>Update URLs</strong>'); ?></p>
		<p>
			<strong><?php _e('We recommed that you backup your website.','calc-system-configuration'); ?></strong>
			<br/><?php _e('You may need to restore it if incorrect URLs are entered in the fields below.','calc-system-configuration'); ?>
		</p>
		<hr class="wp-header-end">
		<h2><?php _e('Step'); ?> 1: <?php _e('Enter your URLs in the fields below','calc-system-configuration'); ?></h2>
		<form method="post" id="" action="<?php echo esc_html( admin_url( 'admin.php?page='.basename(__FILE__,".php") ) ); ?>">
		<?php wp_nonce_field('calc_submit','calc_nonce'); ?>
		<?php settings_fields( 'calc_replace_urls_misc' ); ?>
		<?php do_settings_sections( 'calc_replace_urls_misc' ); ?>
		<table class="form-table">
			<tbody>
				<tr>
					<th>
						<strong><?php _e('Old URL','calc-system-configuration'); ?></strong><br/><span class="description"><?php _e('Old Site Address','calc-system-configuration'); ?></span>
					</th>
					<td><input name="oldurl" type="text" id="oldurl" value="<?php echo (isset($oldurl) && trim($oldurl) != '')? $oldurl : 'http://www.oldurl.com'; ?>" class="regular-text" onfocus="if(this.value=='http://www.oldurl.com') this.value='';" onblur="if(this.value=='') this.value='http://www.oldurl.com';" /></td>
				</tr>
				<tr valign="middle">
					<th><strong><?php _e('New URL','calc-system-configuration'); ?></strong><br/><span class="description"><?php _e('New Site Address','calc-system-configuration'); ?></span></th>
					<td><input name="newurl" type="text" id="newurl" value="<?php echo (isset($newurl) && trim($newurl) != '')? $newurl : 'http://www.newurl.com'; ?>" class="regular-text" placeholder="http://www.newurl.com" onfocus="if(this.value=='http://www.newurl.com') this.value='';" onblur="if(this.value=='') this.value='http://www.newurl.com';" /></td>
				</tr>
			</tbody>
		</table>


		<h2><?php _e('Step'); ?> 2: <?php _e('Choose which URLs should be updated','calc-system-configuration'); ?>s</h2>

		<input name="update_links[]" type="checkbox" id="update_true" value="content" />
		<label for="update_true">
			<strong><?php _e('URLs in page content','calc-system-configuration'); ?></strong> (<?php _e('posts, pages, custom post types, revisions','calc-system-configuration'); ?>)</label>
		<br/>
		<input name="update_links[]" type="checkbox" id="update_true1" value="excerpts" />
		<label for="update_true1"><strong><?php _e('URLs in excerpts','calc-system-configuration'); ?></strong></label>
		<br/>
		<input name="update_links[]" type="checkbox" id="update_true2" value="links" />
		<label for="update_true2"><strong><?php _e('URLs in links','calc-system-configuration'); ?></strong></label>
		<br/>
		<input name="update_links[]" type="checkbox" id="update_true3" value="attachments" />
		<label for="update_true3"><strong><?php _e('URLs for attachments','calc-system-configuration'); ?></strong> (<?php _e('images, documents, general media','calc-system-configuration'); ?>)</label>
		<br/>
		<input name="update_links[]" type="checkbox" id="update_true4" value="custom" />
		<label for="update_true4"><strong><?php _e('URLs in custom fields and meta boxes','calc-system-configuration'); ?></strong></label>
		<br/>
		<input name="update_links[]" type="checkbox" id="update_true5" value="guids" />
		<label for="update_true"><strong><?php _e('Update ALL GUIDs','calc-system-configuration'); ?></strong> 
			<span class="description" style="color:#f00;"><?php _e('GUIDs for posts should only be changed on development sites.','calc-system-configuration'); ?></span> 
			<a href="http://codex.wordpress.org/Changing_The_Site_URL#Important_GUID_Note" target="_blank"><?php _e('Learn More.','calc-system-configuration'); ?></a></label>
		<?php submit_button( __( 'Update URLs NOW', 'calc-system-configuration' ), 'primary', 'calc_settings_submit', true); ?>
		</form>
	</div>

