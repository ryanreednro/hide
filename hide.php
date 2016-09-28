<?php
/*
Plugin Name: Woocommerce - Restricted Categories & Products
Plugin URI: http://reedwebservice.com
Description: Restricts categories and products to specific user roles. 
Version: 0.0.1
Author: Ryan Reed
Author URI: 
Requires at least: 4.2
Tested up to: 4.3
Text Domain: sosm
Domain Path: /languages/
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( !class_exists('EFYP_RESTRICT') ) {
    
    class EFYP_RESTRICT {
        
        protected static $instance;
        
        protected $user_can_access_category;
           
        public function setUserCan($user_can_access_category) {
                 
            $this->$user_can_access_category = $user_can_access_category;
        }
        
        public function getUserCan($user_can_access_category) {
            return $this->$user_can_access_category;
        }
        
        
        public function __construct() {

            add_action( 'init', array( $this, 'init' ) );
            
        }
        
        public function init() {

            add_action( 'admin_menu', array( $this, 'restrict_categories_menu_page' ) );
            add_action( 'admin_init', array( $this, 'register_restricted_settings' ) );
           	add_action( 'template_redirect', array( $this, 'rest_auth_user' ) );
            add_filter( 'woocommerce_product_categories_widget_dropdown_args', array( $this, 'rest_exclude_wc_widget_categories' ) );
            add_filter( 'woocommerce_product_categories_widget_args', array( $this, 'rest_exclude_wc_widget_categories' ) );
            add_filter( 'get_terms', array( $this, 'get_hidden_terms' ), 10, 3 );

        }
        
        public static function instance() {

            if ( is_null( self::$instance ) ) {
                self::$instance = new self();
            }
            return self::$instance;

        }
        
        public function restrict_categories_menu_page() {

            $page_title = 'Restricted';
            $menu_title = 'Restricted';
            $capability = 'manage_options';
            $menu_slug  = 'restricted_options';
            $function   = 'restricted_options_page';
            $position   = '13.5';

            $rest_settings_page = add_menu_page( 
                $page_title,
                $menu_title, 
                $capability, 
                $menu_slug, 
                $function, 
                plugins_url( 'restricted-categories/images/stop1.png' ), 
               	$position );	

        }
        
        public function register_restricted_settings() {

            register_setting( 'restricted_options_group', 'rest_categories' );
            register_setting( 'restricted_options_group', 'rest_product_categories' );
            register_setting( 'restricted_options_group', 'rest_category_ids' );
            register_setting( 'restricted_options_group', 'rest_test_option' );
            register_setting( 'restricted_options_group', 'rest_user_roles' );
            register_setting( 'restricted_options_group', 'rest_debug' );
            register_setting( 'restricted_options_group', 'rest_cat_ver' );

        }
        
        public function rest_auth_user() {
            
            global $post, $user_can_access_category;
            //global $product, $product_cat2, $user_can_access_category, $post, $cat; // added post and cat during debug

            $restd2 = get_option( 'rest_product_categories' );
            $rs_authorized = get_option( 'rest_user_roles' );
            $current_user = wp_get_current_user();
    
            $terms2 = get_the_terms( $post->ID, 'product_cat' );

                if ( !empty( $terms2 ) ) {

                    $categories_weare_in = array();
            
                    // If trying to access product page, generate all categories the product belongs to...
                    if ( is_product() ) {
                        foreach ($terms2 as $term) {
                            $product_cat2 = $term->term_id;
                            $categories_weare_in[] = $product_cat2 = $term->term_id;
                        }
                    // If NOT aaccessing a product page directly, get category and move on...    
                    } else {
            
                        foreach ($terms2 as $term) {
                            $product_cat2 = $term->term_id;
                            $the_name = $term->name; // this line not typical - added to give us category name as well as ID
                            break;
                        }
                    } 
                }

            $category_ids = get_option( 'rest_product_categories' );
            
            /*echo '<br /><br /><span style="color:black;font-weight:bold;text-decoration:underline;">Authorized User Roles ($rs_authorized):</span> <span style="color:red;font-weight:bold;">';
            print_r($rs_authorized);
            echo '<br />';
            print_r($current_user->roles);
            echo '<br />';
            print_r($category_ids);
            echo '</span>'; */
            
            
            if ( empty($rs_authorized) ) {
                //echo 'its empty';
                return;
            } else {
                //echo 'its not empty';
                if ((bool) array_intersect( $rs_authorized, $current_user->roles )) {
                    //echo '<br /><br />It in the array intersect. Current users role IS in authorized list.';
                    $user_can_access_category = "yes";
                } else {
                    $user_can_access_category = "no";
                    //echo '<br /><br /> NOT in intersect! Means current users role is NOT in authorized list. Well: ' . $user_can_access_category;
                    
			
                }
            }

            if ( !empty( $restd2 ) ) {
                //echo '<br />$restd2 is not empty<br />';
                //echo 'product_cat2: ' . $product_cat2 . '<br />Were in: ';
                //print_r($categories_weare_in);
                
                //if ( ((bool) array_intersect( $categories_weare_in, $category_ids ) ) && ( $user_can_access_category == "no" )) {
                if (( in_array( $product_cat2, $category_ids ) ) && ( $user_can_access_category == "no" )) {
                     //echo '<br /><span style="color:red;font-weight:bold;">The CURRENT category is within the list of RESTRICTED categories!</span>';
                    wp_redirect( site_url( '/' ));
                    exit();
					
                }
                
            } else {
                //echo '$restd2 is EMPTY';
            }
        
        
        } // rest_auth_user
            
        public function rest_exclude_wc_widget_categories( $cat_args ) {

        global $user_can_access_category;
        $rst_category_ids = get_option( 'rest_product_categories' );
            //echo 'hello ' . $user_can_access_category . ' goodbye';
            //print_r($rst_category_ids);
            if ( $user_can_access_category == "no" ) {
                //echo 'NOT ALLOWED';
                $cat_args['exclude'] = $rst_category_ids; // Insert the product category IDs you wish to exclude
                //	The Original Statement
                //	$cat_args['exclude'] = array('519'); // Insert the product category IDs you wish to exclude
                return $cat_args;

            } elseif ( $user_can_access_category == "yes" ) {
				//echo 'NO NO!';
                return $cat_args;
            }


        } // rest_exclude          
        
        
        function get_hidden_terms( $terms, $taxonomies, $args ) {

            global $user_can_access_category;
            $rst_category_ids = get_option( 'rest_product_categories' );
            $new_terms = array();
 
                if ( $user_can_access_category == "no" ) { 
 
                    // if a product category and on the shop page
                    if ( in_array( 'product_cat', $taxonomies ) && ! is_admin() && is_product_category() ) {
 
                        foreach ( $terms as $key => $term ) {
 
                            if ( ! in_array( $term->term_id, $rst_category_ids ) ) {
                                $new_terms[] = $term;
            
                            }
                
                        }
 
                        $terms = $new_terms;
                    }
 
                    return $terms;
    
                } elseif ( $user_can_access_category == "yes" ) {
        
                    return $terms;
        
                }
    
            return $terms;
        } // get_hidden_terms
        
    } // class
    
    
}



function efyp_restrict() {
	return EFYP_RESTRICT::instance();
}


// fire it up!
efyp_restrict();

/**
 * restricted_options_page function.
 *
 * WordPress Settings Page
 * @access public
 * @return void
 */

function restricted_options_page() {

	//Save the field values
	if ( isset( $_POST['restricted_options_submitted'] ) && $_POST['restricted_options_submitted'] == 'submitted' ) {
		foreach ( $_POST as $key => $value ) {
			if ( get_option( $key ) != $value ) {
			update_option( $key, $value );
			} else {
			add_option( $key, $value, '', 'no' );
			}
		}
	}
	?>

	<div class="wrap">
		<h2>Restricted Options</h2>

		<?php
		settings_errors();
                $active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'restricted';  
		?>

		<h2 class="nav-tab-wrapper">  
			<a href="?page=restricted_options&tab=restricted" class="nav-tab <?php echo $active_tab == 'restricted' ? 'nav-tab-active' : ''; ?>">Restricted</a>  
			<a href="?page=restricted_options&tab=authorized" class="nav-tab <?php echo $active_tab == 'authorized' ? 'nav-tab-active' : ''; ?>">Authorized</a>  
			<a href="?page=restricted_options&tab=reserved" class="nav-tab <?php echo $active_tab == 'reserved' ? 'nav-tab-active' : ''; ?>">Reserved</a>  
			<a href="?page=restricted_options&tab=settings" class="nav-tab <?php echo $active_tab == 'settings' ? 'nav-tab-active' : ''; ?>">Settings</a>  
		</h2> 

		<form method="post" action="" id="restricted_options">
		<input type="hidden" name="restricted_options_submitted" value="submitted">

		<?php
		settings_fields( 'restricted_options_group' );
		do_settings_sections( 'restricted_options_group' );

		//get the older values, wont work the first time
	        $options = get_option( 'restricted_options_group' );


		// Call in the HTML for the admin settings page
		include dirname(__FILE__) .'/settings.php';

			if ( $active_tab == 'restricted' ) {  
				tab_restricted();
			} else if ( $active_tab == 'authorized' ) {
				tab_authorized();
            		} else if ( $active_tab == 'settings' ) {
				tab_about();
		 	} else if( $active_tab == 'reserved' ) {
				tab_quick_ship();
            		}

		
 
?>

<?php
// testing - THIS SEEMS TO WORK 
/*        $lpd_settings   = get_option('restricted_categories_lp', array());
        $lpd_default    = array();


        if ( isset($_POST['rst_category_ids']) && is_array($_POST['rst_category_ids']) ) {
            if ( !empty($_POST['rst_category_ids']) ) {
                $rst_category_ids = $this->csv_to_array( $_POST['rst_category_ids'] );

                foreach ( $rest_product_categories as $product_id ) {
                    $lpd_default[] = $product_id;
                }
            }
        }
        $lpd_settings['rst_category_ids'] = array_filter( $lpd_default );
        update_option( 'restricted_categories_lp', $lpd_settings );

*/

 $default_rst    = array();
 if ( isset($_POST['rest_product_categories']) && is_array($_POST['rest_product_categories']) ) {
            if ( !empty($_POST['rest_product_categories']) ) {
                //$rest_product_categories = $this->csv_to_array( $_POST['rest_product_categories'] );

                foreach ( $rest_product_categories as $product_id ) {
                    $default_rst[] = $product_id;
                }
            }
        }



// Save Restricted Categories
			
/*
			if (( isset($_POST['restricted_options_submitted']) && ( $_POST['restricted_options_submitted'] == 'submitted' ))) {

				if ( is_null($_POST['rest_product_categories']) ) {
					$its_empty = array();
					echo 'EMPTY';
					update_option( 'rest_product_categories', $its_empty );

					} elseif ( isset($_POST['rst_category_ids']) && is_array($_POST['rst_category_ids']) ) {

					$update_categories = $_POST['rst_category_ids'];
					echo '<h1>We are updating</h1>';
					update_option( 'rest_product_categories', $update_categories );
				}
			}
*/
// Save Restricted Users
/*
			if (( isset($_POST['restricted_options_submitted']) && ( $_POST['restricted_options_submitted'] == 'submitted' ))) {

				if ( isset($_POST['rest_user_roles']) && is_array($_POST['rest_user_roles']) ) {
					$update_roles = $_POST['rest_user_roles'];
					echo '<h1>We are updating</h1>';
					update_option( 'rest_user_roles', $update_roles );

				}
			}
*/	
		// Save Changes
		submit_button();
?>
</form>
</div>

<?php
}
