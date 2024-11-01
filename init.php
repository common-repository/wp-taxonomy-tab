<?php
/*
Plugin Name: Wp Taxonomy Tab
Description: Responsive WordPress Terms Tab Plugin. Simple to show terms and its posts as tab view.
Version:     1.0
Author:      Anandaraj Balu
Author URI:	 https://profiles.wordpress.org/anand000
Text Domain: wptaxonomytab
Domain Path: /languages
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Copyright 2014-2017 Wp Taxonomy Tab Plugin

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

//Admin Style
add_action( 'admin_enqueue_scripts', 'wptTabPosts_AdminStyle' );
function wptTabPosts_AdminStyle() { 
	if ( 'wpttab_post' == get_post_type() ){
		wp_register_style( 'admin-style',  plugin_dir_url( __FILE__ ) . 'admin/css/style.css',false, '0.1' );
		wp_enqueue_style( 'admin-style' );
		wp_enqueue_script( 'admin-custom', plugin_dir_url( __FILE__ ) . 'admin/js/custom.js',array('jquery') );
		wp_enqueue_style( 'wp-color-picker');
		wp_enqueue_script( 'wp-color-picker');
		echo '<style>#message,.hide-if-no-js{display:none;}</style>';
	}
}
add_action( 'wp_enqueue_scripts', 'wptTabPosts_public_style' );
function wptTabPosts_public_style() {
	wp_register_style( 'open-sans',  'https://fonts.googleapis.com/css?family=Open+Sans:300i,400,400i,600,700,700i' );
	wp_enqueue_style( 'open-sans' );
	wp_register_style( 'wpt-style',  plugin_dir_url( __FILE__ ) . 'wpt-style.css',false, '0.1' );
	wp_enqueue_style( 'wpt-style' );
}
add_action( 'wp_footer', 'wptTabPosts_public_js_scripts' );
function wptTabPosts_public_js_scripts() {	
	wp_enqueue_script( 'tab-js',plugin_dir_url( __FILE__ ) .'wpt-tab.js',array('jquery'));
}
function wptTabPosts_excerpt_more( $more ) {
	return ' . . .';
}
add_filter( 'excerpt_more', 'wptTabPosts_excerpt_more' );

//CPT
add_action('init','WptTabPostsTaxonomy_Reg');
function WptTabPostsTaxonomy_Reg(){
	$singular = 'WPT Tab post';$plural = 'Tab posts';
	$labels = array( 'name' => $singular, 'singular_name' => $singular, 'add_new' => 'Add New '. $singular, 'add_new_item' => 'Add New', 'edit_item' => 'Edit '.$singular, 'new_item' => 'New '.$singular, 'all_items' => 'All '.$plural, 'view_item' => 'View', 'search_items' => 'Search', 'not_found' =>  'No '.$plural.' found', 'not_found_in_trash' => 'No '.$plural.' found in Trash', 'parent_item_colon' => '', 'menu_name' => $singular );
	
	$args = array( 'labels' => $labels, 'public' => true, 'publicly_queryable' => true, 'show_ui' => true,  'show_in_menu' => true, 'query_var' => true, 'rewrite' => array( 'slug' => 'wpttabpost_list' ), 'capability_type' => 'post', 'has_archive' => true, 'hierarchical' => false,'menu_position' => null,'supports' => array( 'title' ), 'menu_icon' => 'dashicons-list-view' );	
	register_post_type( 'wpttab_post', $args );
}
//Meta Box
add_action( 'add_meta_boxes', 'WptTabPosts_Meta', 10, 2 );
function WptTabPosts_Meta() {
    add_meta_box( 'tabpostmeta',__( 'WPT Taxonomy Tab' ),'WptTabPosts_Select','wpttab_post', 'normal','default' );
}
function WptTabPosts_Select($WpTabmetabox){ $all_items_post_types = get_post_types();$exist_posttypes = array("wpttab_post","page","attachment","revision","nav_menu_item","custom_css","customize_changeset"); 
$post_types = array_diff($all_items_post_types,$exist_posttypes);
?>
<div class="TabSelect">
	<div id="WPTabs" class="TabOption">
		<div class="title-tabs" id="">
			<div class="post-types">
				<!--Post Types Listing-->
				<p><strong>Post Type Equal To :</strong></p>			
				<select id="Wp_PostTypes" name="Wpmeta[select-post-type]" class="tab-input" Onchange="Wptabs_ShowTaxo(this.value);">
					<option value=""></option>
					<?php foreach($post_types as $type){?> 
					<option value="<?php echo $type; ?>"<?php if(get_post_meta( $WpTabmetabox->ID, 'select-post-type', true ) == $type){?>selected="selected"<?php }?> ><?php echo $type; ?>
					</option> <?php }?>
				</select>
				
				<!--Taxonomies Listing-->
				<div class="WpTabTaxonomy">					
					<?php 
					if(get_post_meta( $WpTabmetabox->ID, 'select-post-type', true )){					
						$PostType = get_post_meta( $WpTabmetabox->ID, 'select-post-type', true );
						$Alltaxonomies = get_object_taxonomies($PostType); 
						$Restric_taxo = array("post_tag","post_format");
						$taxonomies = array_diff($Alltaxonomies,$Restric_taxo);?>
						
						<div class="post-taxonomy">
							<p><strong>Taxonomy Equal To :</strong></p>
							<select id="tname" name="Wpmeta[select-post-tax]" class="tab-input" onchange="Wptabs_ShowTerms(this.value);">
								<option value=""></option>
								<?php foreach($taxonomies as $taxonomy){?> 
								<option value="<?php echo $taxonomy; ?>"<?php if(get_post_meta( $WpTabmetabox->ID, 'select-post-tax', true ) == $taxonomy){?>selected="selected"<?php }?> ><?php echo $taxonomy; ?>
								</option> <?php }?>
							</select>
						</div>
					<?php } ?>
				</div>				
			</div>
		</div>
	</div>
	<!--Setting-->
	<div class="TabSettings">
	<h2><strong>Settings</strong></h2>
		<table width="100%">
			<tr>
				<td>Post Per Page</td>
				<td><input type="number" class="tab-input" name="Wpmeta_number[num-post]" value="<?php echo get_post_meta( $WpTabmetabox->ID, 'num-post', true ); ?>"><span>( If '0' Show All Tabs )</span></td>
			</tr>
			<tr>
				<td>Post Order</td>
				<td><select class="tab-input" name="Wpmeta[order-post]">
					<option value="DESC" <?php if(get_post_meta( $WpTabmetabox->ID, 'order-post', true ) == 'DESC'){?>selected="selected"<?php }?>>DESC</option>
					<option value="ASC" <?php if(get_post_meta( $WpTabmetabox->ID, 'order-post', true ) == 'ASC'){?>selected="selected"<?php }?>>ASC</option>
					</select>
					<span>( Default Oredr is DESC )</span>
				</td>
			</tr>
		</table>
		<h2><strong>Typography</strong></h2>
		<table width="100%">
			<tr>
				<td>Tab Body Background Color</td>
				<td><input type="text" class="tab-input color-field" name="Wpmeta[tabcontent_bg]" value="<?php if(get_post_meta( $WpTabmetabox->ID, 'tabcontent_bg', true ) !=''){ echo get_post_meta( $WpTabmetabox->ID, 'tabcontent_bg', true );} ?>"></td>
			</tr>
			<tr>
				<td>Tab Body Text Color</td>
				<td><input type="text" class="tab-input color-field" name="Wpmeta[tabbody_color]" value="<?php if(get_post_meta( $WpTabmetabox->ID, 'tabbody_color', true ) !=''){echo get_post_meta( $WpTabmetabox->ID, 'tabbody_color', true ); }?>"></td>
			</tr>
			<tr>
				<td>Tab Title Background Color</td>
				<td><input type="text" class="tab-input color-field" name="Wpmeta[tabtitle_bg]" value="<?php if(get_post_meta( $WpTabmetabox->ID, 'tabtitle_bg', true ) !=''){ echo get_post_meta( $WpTabmetabox->ID, 'tabtitle_bg', true ); } ?>"></td>
			</tr>
			<tr>
				<td>Tab Title Text Color</td>
				<td><input type="text" class="tab-input color-field" name="Wpmeta[tabtxt_color]" value="<?php if(get_post_meta( $WpTabmetabox->ID, 'tabtxt_color', true )!=''){echo get_post_meta( $WpTabmetabox->ID, 'tabtxt_color', true );} ?>"></td>
			</tr>
			<tr>
				<td>Tab Title Active Background Color</td>
				<td><input type="text" class="tab-input color-field" name="Wpmeta[tabtitleactive_bg]" value="<?php if(get_post_meta( $WpTabmetabox->ID, 'tabtitleactive_bg', true ) !=''){ echo get_post_meta( $WpTabmetabox->ID, 'tabtitleactive_bg', true ); } ?>"></td>
			</tr>
			<tr>
				<td>Tab Title Text Active Color</td>
				<td><input type="text" class="tab-input color-field" name="Wpmeta[tabtxtact_color]" value="<?php if(get_post_meta( $WpTabmetabox->ID, 'tabtxtact_color', true ) !=''){ echo get_post_meta( $WpTabmetabox->ID, 'tabtxtact_color', true ); }?>"></td>
			</tr>
		</table>
	</div>
	<!--Short code-->
	<div class="wptabs_shortcode">
	<h2><strong>Shortcode</strong></h2>
	<table width="100%">
		<tr>
			<td><strong><code>[WptTabpost tab_id="<?php echo get_the_ID(); ?>"]</code></strong></td>
		</tr>
	</table>

	</div>
</div>
<?php	
wp_nonce_field(basename(__FILE__),'wpttabpost_nonce');
}
//Save post Meta
add_action( 'save_post', 'WptTabPosts_SaveMeta',10, 2 );
function WptTabPosts_SaveMeta($post_id){
	$is_autosave = wp_is_post_autosave( $post_id );
	$is_revision = wp_is_post_revision( $post_id ); 
	$is_valid_nonce = ( isset( $_POST['wpttabpost_nonce'] )  && wp_verify_nonce( $_POST['wpttabpost_nonce'], basename(__FILE__) ) ) ? 'true' : 'false';
	
	if( $is_autosave || $is_revision || !$is_valid_nonce ){
		return;
	}
	if(isset($_POST['Wpmeta'])){
		$values = $_POST['Wpmeta'];
		foreach($values as $key=>$value){
			$sanitize_value = sanitize_text_field($value);
			update_post_meta($post_id, $key, $sanitize_value);
		}		
	}else{
		$values = array('DESC');
		foreach($values as $value){
			update_post_meta($post_id, 'order-post', $value);
		}	
	}
	if(isset($_POST['Wpmeta_number'])){
		$values = $_POST['Wpmeta_number'];
		foreach($values as $key=>$value){
			$int_value = absint($value);
			update_post_meta($post_id, $key, $int_value);
		}
	}
}

//Wordpress Ajax
add_action('wp_ajax_taxonomiesResult','wptTabPosts_TaxonomyListing');
function wptTabPosts_TaxonomyListing(){
	if(isset($_POST['key_post'])){
		$PostType = sanitize_text_field($_POST['key_post']);
		$Alltaxonomies = get_object_taxonomies($PostType); 
		$Restric_taxo = array("post_tag","post_format");
		$taxonomies = array_diff($Alltaxonomies,$Restric_taxo);
		?>
		<div class="post-taxonomy">
			<p><strong>Taxonomy Equal To :</strong></p>
			<select id="tname" name="Wpmeta[select-post-tax]" class="tab-input" onchange="Wptabs_ShowTerms();">
				<option value=""></option>
				<?php foreach($taxonomies as $taxonomy){?> 
				<option value="<?php echo $taxonomy; ?>"<?php if(get_post_meta( $WpTabmetabox->ID, 'select-post-tax', true ) == $taxonomy){?>selected="selected"<?php }?> ><?php echo $taxonomy; ?>
				</option> <?php }?>
			</select>
		</div> 
		<?php
	}
	die();
}

//Short code
add_shortcode('WptTabpost', 'WptpostTab');

function WptpostTab($shortcode_id){ob_start();	
	global $post;
	$postID = $shortcode_id['tab_id'];
	$args = array( 'post_type' => 'wpttab_post');
	$loop = new WP_Query( $args );
	while ( $loop->have_posts() ) {$loop->the_post();
		$allpost_ids[] = get_the_ID();
	}
	$chk_arry = $allpost_ids;
	if (is_array($chk_arry)) {
		if($postID !='' && in_array($postID, $chk_arry)){	
			/*Post Type*/
			$wpt_posttype = get_post_meta( $postID, 'select-post-type', true );
			/*Taxonomy*/
			$wpt_taxonomy = get_post_meta( $postID, 'select-post-tax', true );
			/*Post Order*/
			$wpt_order = get_post_meta( $postID, 'order-post', true );  
			/*Numper of post*/
			$wpt_numof = get_post_meta( $postID, 'num-post', true ); ?>
			<style>
			.wpt-tabtitles-<?php echo $postID;?> li a,.wpt-pagination-<?php echo $postID;?> a{
			  background-color: <?php if(get_post_meta( $postID, 'tabtitle_bg', true )){ echo get_post_meta( $postID, 'tabtitle_bg', true );}else{ ?>#efefef<?php }?>;
			  color: <?php if(get_post_meta( $postID, 'tabtxt_color', true )){ echo get_post_meta( $postID, 'tabtxt_color', true );}else{ ?>#8e8e8e<?php }?>;
			}
			a.wpt-btn{
				color: <?php if(get_post_meta( $postID, 'tabbody_color', true )){ echo get_post_meta( $postID, 'tabbody_color', true );}else{ ?>#6e6e6e<?php }?>;
			}
			.wpt-tabtitles-<?php echo $postID;?> li a.active,.wpt-pagination-<?php echo $postID;?> .active, .wpt-pagination-<?php echo $postID;?> a:hover {
			  background-color:<?php if(get_post_meta( $postID, 'tabtitleactive_bg', true )){ echo get_post_meta( $postID, 'tabtitleactive_bg', true );}else{?>#616161<?php }?>;
			  border-color: #efefef;
			  color: <?php if(get_post_meta( $postID, 'tabtxtact_color', true )){ echo get_post_meta( $postID, 'tabtxtact_color', true );}else{ ?>#ffffff<?php }?> !important;
			}
			.wpt-maincontenttab-<?php echo $postID;?> {
			  background-color: <?php if(get_post_meta( $postID, 'tabcontent_bg', true )){ echo get_post_meta( $postID, 'tabcontent_bg', true );}else{ ?>#ffffff<?php }?>;
			  color: <?php if(get_post_meta( $postID, 'tabbody_color', true )){ echo get_post_meta( $postID, 'tabbody_color', true );}else{ ?>#6e6e6e<?php }?>;
			}
			</style>
			<!--Taxonomy Tab-->
			<?php $wpt_terms = get_terms( $wpt_taxonomy, array(  'hide_empty' => true ) ); 
			if(0<count($wpt_terms)){ ?>
			<div id="<?php echo 'parentID-'.$postID; ?>" class="wpt-tab-holder">
				<!--Start Tab Title-->
				<ul class="wpt-tabtitles wpt-tabtitles-<?php echo $postID;?>">			
				<?php foreach( $wpt_terms as $key => $wpt_term ){?>
					<li data-tab-id="datatab-<?php echo $wpt_term->term_id; ?>">
						<a onclick="TabDisplay(this);return false;" data-parent="<?php echo 'parentID-'.$shortcode_id['tab_id']; ?>" id="childTitleID-<?php echo $wpt_term->term_id; ?>" href="#wptt-<?php echo $wpt_term->term_id; ?>"><?php echo sanitize_text_field($wpt_term->name); ?></a>
					</li>
				<?php }wp_reset_postdata(); ?>
				</ul>
				<!--End Tab Title-->
				<div class="wpt-container">
					<div class="wpt-maincontenttab wpt-maincontenttab-<?php echo $postID;?>">
					<!--Start Tab Content-->
					<?php $wpt_terms = get_terms( $wpt_taxonomy, array(  'hide_empty' => true ) );
					foreach( $wpt_terms as $key => $wpt_term ){ ?>	
						<div class="wpt-contenttab" id="wptt-<?php echo $wpt_term->term_id; ?>">
							<?php	
							$args = array( 
								'posts_per_page' => -1,
								'post_type' => $wpt_posttype,
								'order' => $wpt_order, 
								'tax_query' => array(
									array('taxonomy' => $wpt_taxonomy,
									'field' => 'term_id',
									'terms' => $wpt_term->term_id))	
								);
							$termspost = get_posts( $args );
							$count = count($termspost);
							
							//Tab Pagination
							if($wpt_numof < $count && $wpt_numof !=0){ ?>
								<ul class="wpt-pagination wpt-pagination-<?php echo $postID; ?>">
								<li>Pagination :</li>
									<?php $i=1;
									foreach( $termspost as $key=>$post ) { setup_postdata($post);
									  if($key%$wpt_numof == 0){?><li><a id="position-<?php echo $key;?>" onclick="pagiNation(this);return false;" class="<?php if($key == 0){ ?>active<?php } ?>" data-mainparent="<?php echo 'parentID-'.$shortcode_id['tab_id']; ?>" href="#pagId-<?php echo get_the_ID();?>" data-parent="wptt-<?php echo $wpt_term->term_id; ?>"><?php echo ($key/$wpt_numof) + 1;?></a></li><?php }
									$i++;}  wp_reset_postdata();?>
								</ul>
							<?php } ?>
							<!--End Tab Pagination-->
							
							<div class="tab-sect">
							<?php						
							foreach( $termspost as $key=>$post ) { setup_postdata($post);?>
							<?php if($wpt_numof < $count && $wpt_numof !=0){ ?>
								<?php if($key%$wpt_numof==0){ ?><div id="pagId-<?php echo get_the_ID();?>" data-tab-id="<?php echo $key;?>" class="wpt-content <?php if($key == 0){echo 'wpt-active';}?>"><?php }?>
								<div class="wpt-inner-content">
									<h2><?php echo sanitize_text_field(get_the_title()); ?></h2>
									<p><?php echo sanitize_text_field(wptTabPosts_excerpt(40)); ?></p>
									<p><a class="wpt-btn wpt-btn-<?php echo $postID;?>" href="<?php the_permalink(); ?>">Read more</a></p>
								</div>
								<?php if($key%$wpt_numof==($wpt_numof -1) || $key+1==$count){ ?></div><?php }?>
							<?php }else{ ?>
								<div class="wpt-content-nopagination">
									<h2><?php echo sanitize_text_field(get_the_title()); ?></h2>
									<p><?php echo sanitize_text_field(wptTabPosts_excerpt(40)); ?></p>
									<p><a class="wpt-btn wpt-btn-<?php echo $postID;?>" href="<?php the_permalink(); ?>">Read more</a></p>
								</div>
							<?php } ?>
							
							<?php }wp_reset_postdata();?>
							</div>
						</div>					
					<?php }wp_reset_postdata(); ?>
					</div>
				</div>
				<!--End Tab Content-->
			</div>	
		<?php
			}else{
				echo '<h3>There is no tab to show.</h3>';
			}
		}else{
			echo '<h3>WPT Tab Post Missing (or) Invalid Post</h3>';
		}
	}
	return ob_get_clean(); 
}
function wptTabPosts_excerpt($limit) {
      $excerpt = explode(' ', get_the_excerpt(), $limit);
      if (count($excerpt)>=$limit) {
        array_pop($excerpt);
        $excerpt = implode(" ",$excerpt).' ...';
      } else {
        $excerpt = implode(" ",$excerpt);
      } 
      $excerpt = preg_replace('`\[[^\]]*\]`','',$excerpt);
      return $excerpt;
}
?>