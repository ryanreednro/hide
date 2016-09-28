<?php
	if ( isset( $_POST['restricted_options_submitted'] ) && $_POST['restricted_options_submitted'] == 'submitted' ) { ?>
		<div id="message" class="updated fade"><p><strong><?php _e( 'Your settings have been saved.', 'efyp' ); ?></strong></p></div>
	<?php }

function tab_restricted() {
?>
<h3 class="heading">Secret Product Categories (Restricted Categories)</h3>

<table class="form-table">
	  <tbody>
	    <tr valign="top">
		<th class="titledesc" scope="row">
		<label for="product_categories"><?php _e( 'Secret Product categories', 'woocommerce' ); ?></label>
		<img class="help_tip" data-tip='<?php _e( 'The categories are NOT accessible to the general public.', 'woocommerce' ); ?>' src="<?php echo WC()->plugin_url(); ?>/assets/images/help.png" height="16" width="16" /></p>
		</th>
		<td class="forminp forminp-select">
		<select id="rest_product_categories" name="rest_product_categories[]" style="width: 50%;"  class="wc-enhanced-select" multiple="multiple" data-placeholder="<?php esc_attr_e( 'Any category', 'woocommerce' ); ?>">
					<?php
						// $category_ids = (array) get_post_meta( $post->ID, 'rest_product_categories', true ); // removed
						$rst_category_ids = get_option('rest_product_categories');
						$categories   = get_terms( 'product_cat', 'orderby=name&hide_empty=0' );

						if ( $categories ) foreach ( $categories as $cat ) {
							echo '<option value="' . esc_attr( $cat->term_id ) . '"' . selected( in_array( $cat->term_id, $rst_category_ids ), true, false ) . '>' . esc_html( $cat->name ) . '</option>';
						}
					?>
				</select>
		</td>
	  </tr>
	  <tr>
		<th class="titledesc" scope="row">Secret Category IDs
		<img class="help_tip" data-tip='<?php _e( 'Secret Category Ids. For reference Only.', 'woocommerce' ); ?>' src="<?php echo WC()->plugin_url(); ?>/assets/images/help.png" height="16" width="16" /></p>
		</th>
		<td>
			<div style="width:350px;border:1px dashed black;padding:5px;">
			<?php 

			// NO LONGER USED - RETURNS RAW ARRAY OF ACTIVE CATEGORIES
			// print_r($rst_category_ids);

			// RETURN ACTIVE CATEGORY IDS
			$rst_act_ids = '';

				if ( !empty($rst_category_ids) ) {
				foreach( $rst_category_ids as $rst_category_ids){
					$rst_act_ids .= $rst_category_ids . ',&nbsp;';
				}
				}
			$rst_active_ids = rtrim($rst_act_ids, ',&nbsp;');
			echo $rst_active_ids;
			// END DISPLAY ACTIVE CATEGORY IDS
?>
			</div>			
		</td>
	    </tr>
	<tr valign="top">
	<th class="titledesc" scope="row">
		<label for="rest_user_roles"><?php _e( 'User Roles with Access', 'woocommerce' ); ?></label>
		<img class="help_tip" data-tip='<?php _e( 'The are the roles that CAN access the restricted categories.', 'woocommerce' ); ?>' src="<?php echo WC()->plugin_url(); ?>/assets/images/help.png" height="16" width="16" /></p>
		</th>
		<td class="forminp forminp-select">
<select id="rest_user_roles" name="rest_user_roles[]" style="width: 50%;"  class="wc-enhanced-select" multiple="multiple" data-placeholder="<?php esc_attr_e( 'Any role', 'woocommerce' ); ?>">
<?php
		global $wp_roles;
		$rest_role_ids = get_option('rest_user_roles');

//if ( ! isset( $wp_roles ) ) {

		$wp_roles = new WP_Roles();
		$roles = $wp_roles->get_names();
//}
		foreach ($roles as $role_value => $role_name) {
		echo '<option value="' . esc_attr( $role_value ) . '"' . selected( in_array( $role_value, $rest_role_ids ), true, false ) . '">' . $role_name . '</option>';
// org		echo '<option value="' . esc_attr( $role_name ) . '"' . selected( in_array( $role_name, $rest_role_ids ), true, false ) . '">' . $role_name . '</option>';

  	}
?>
</select>
		</td>
	</tr>
	  </tbody>
	</table>

	<div class="explain">
	Only categories selected above will be RESTRICTED. RESTRICTED means that users cannnot access categories or products within a category unless their ROLE is authorized.
	</div>


<table class="form-table">
	<tbody>
<!-- OPTION -->
<tr valign="top">
<th scope="row">
<label for="text_lower_price"><?php _e( 'Reserved', 'wc_lower_price' ); ?></label>
<img class="help_tip" data-tip='<?php _e( 'Reserved for future use.', 'woocommerce' ); ?>' src="<?php echo WC()->plugin_url(); ?>/assets/images/help.png" height="16" width="16" /></p>

</th>
<td>
	<textarea class="regular-text valid" rows="3" cols="45" name="rest_test_option" readonly><?php echo stripslashes( get_option('rest_test_option') ); ?></textarea>


</td>
</tr>
</table>

<?php 
}
// **************************************TAB ABOUT**************************************************
function tab_about() {
?>
<h3 class="heading">About</h3>

<p><span style="font-weight:bold;text-decoration:underline;">Version:</span> <?php echo (get_option( 'rest_cat_ver' )); ?><br />
<span style="font-weight:bold;text-decoration:underline;">Designed By:</span> Ryan Reed<br />
<span style="font-weight:bold;text-decoration:underline;">Last Updated By:</span> Ryan Reed<br />
<span style="font-weight:bold;text-decoration:underline;">Last Update:</span> Dec 6, 2015</p>

<div style="width:50%;">
<p>Restricted Categories is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.</p>
 
<p>Restricted Categories is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.</p>
 
<p>You should have received a copy of the GNU General Public License
along with Restricted Categories. If not, please contact the author.</p>
</div>



<div class="next-option">
	 		<div class="cont_wrapz">
					<div class="desc_left">
					<label for="rest_debug"><b><?php _e( 'Enable Debug Mode:', 'rest' ); ?></b></label>
					</div> 

					<div class="right_stuff">
					<?php if ( get_option( 'rest_debug' ) == 'enabled') { ?>
					<input type="radio" name="rest_debug" value="enabled" class="input-radio efyp-radio" checked="yes" />
					<label for="rest_debug"><?php _e( 'Enabled', 'rest' ); ?></label><br />
					<input type="radio" name="rest_debug" value="disabled" class="input-radio efyp-radio" />
					<label for="rest_debug"><?php _e( 'Disabled', 'rest' ); ?></label><br />
					<?php } else { ?>
					<input type="radio" name="rest_debug" value="enabled" class="input-radio efyp-radio" />
					<label for="rest_debug"><?php _e( 'Enabled', 'rest' ); ?></label><br />
					<input type="radio" name="rest_debug" value="disabled" class="input-radio efyp-radio" checked="yes" />
					<label for="rest_debug"><?php _e( 'Disabled', 'rest' ); ?></label><br />
					<?php } ?>

					</div>  <!-- right_stuff -->
				 </div>  <!-- cont_wrap -->

			<div class="clear"></div>
			<div class="explain"><span class="description">
			When enabled, debug mode shows key information on the edit product pages and edit order pages. Useful for troubleshooting which variables are set.</span>
			
<?php 
$rest_debug_yes = get_option( 'rest_debug' );
if ( $rest_debug_yes == 'enabled' ) {
echo '<br /><span style="color:red;">Current Setting: ' . get_option( 'rest_debug' ) . '</span>'; 
}
?>



			</div>
			<div class="clear"></div>

	</div>






<?php
}

function tab_authorized() {

?>
<h3 class="heading">Choose Who Has Access</h3>

<table class="form-table">
	<tbody>
	
	</tbody>
</table>
<?php	



}