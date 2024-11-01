<?php
class FONE_currentcategories_Widget extends WP_Widget {
    public function __construct() {
        parent::__construct(
			'my_custom_widget', __('F1 Current Subcategories', 'fone_current_subcategories'), array(
			'customize_selective_refresh' => true,
			)
        );
        add_action('wp_enqueue_scripts', array($this, 'FONE_current_scripts'));
    }
    public function FONE_current_scripts() {
		wp_enqueue_style('FONE_current_style', plugins_url( 'assets/FONE_current_style.css', __FILE__ ), array(),'0.1',false);
    }
    public function widget($args, $instance) {
        extract($args);
		echo $before_widget;
        $parentid = get_queried_object_id();
		$term = get_term( $parentid );
		if (!isset($term->name)){return;}
		$category_name = $term->name;
		$category_id = $term->term_id;
		$title = $category_name;
		$current_term_id = '';
		$subcategories = FONE_currsubcat($parentid);
		if (!$subcategories){
			$current_term_id = $term->term_id;
			if ($term->parent>0){
				$termparent = get_term( $term->parent );
				$title = $termparent->name; 
				$category_id = $termparent->term_id;
				$subcategories = FONE_currsubcat($term->parent);
			}
		}
        if (!empty($subcategories) && trim($category_name)!='') {
			echo '<div class="FONE_currentsubcategories wp_widget_plugin_box">';
			if ($title) { echo $before_title . '<a href="'.get_term_link($category_id,"product_cat").'">'.$title.'</a>'.$after_title;}
            echo "<ul class='FONE_currentsubcategory'>";
            foreach ($subcategories as $key => $subcategory) {
				if ($current_term_id==$subcategory->term_id){$current = "style='font-weight:bold;'";} else {$current = "style='color:rgba(102,102,102,0.7);'";}
                echo "<li class='FONE_currentsubcategory_parent FONE_currentsubcategory $subcategory->slug'>"
                . "<a class='FONE_currlink' {$current} href='" . get_category_link($subcategory->term_id) . "'>{$subcategory->name}</a>";
                echo "</li>";
            }
            echo "</ul>";
			echo '</div>';
        }
    }
}
add_action('widgets_init', 'FONE_currentcategories_fn');
function FONE_currentcategories_fn() {
    register_widget('FONE_currentcategories_Widget');
}
function FONE_currsubcat($parent) {
	$cat_args = array('parent' => $parent,'hide_empty' => true,);
	$terms = get_terms('product_cat', $cat_args);
	return $terms;
}


//https://www.businessbloomer.com/woocommerce-visual-hook-guide-single-product-page/
//https://www.businessbloomer.com/woocommerce-visual-hook-guide-archiveshopcat-page/
//add_action( 'woocommerce_before_main_content', 'FONE_tf_display_products_per_subcategory',20);
add_action( 'woocommerce_archive_description', 'FONE_tf_display_products_per_subcategory', 10 );
//https://www.scratchcode.io/add-products-per-page-dropdown-in-woocommerce/
//add_action('woocommerce_before_shop_loop', 'FONE_tf_display_products_per_subcategory', 25);
function FONE_tf_display_products_per_subcategory() {
	$parentid = get_queried_object_id();
	$term = get_term( $parentid );
	if (!isset($term->name)){return;}
	$category_name = $term->name;
	$category_id = $term->term_id;
	$title = $category_name;
	$current_term_id = '';
	$subcategories = FONE_currsubcat($parentid);
	if (!$subcategories){
		$current_term_id = $term->term_id;
		if ($term->parent>0){
			$termparent = get_term( $term->parent );
			$title = $termparent->name; 
			$category_id = $termparent->term_id;
			$subcategories = FONE_currsubcat($term->parent);
		}
	}
	if (!empty($subcategories) && trim($category_name)!='') {
		//if (strtoupper(get_current_theme())!='FLATSOME'){echo '<div style="height:40px;"></div>';}
		echo '<div class="woocommerce-percategory" style="height:25px;top:-25px;position:relative;">';
		echo '<select onchange="if (this.value) window.location.href=this.value" style="height:43px;max-width:236px;margin-bottom:5px;">';
			//if ($title) { echo $before_title . '<a href="'.get_term_link($category_id,"product_cat").'">'.$title.'</a>'.$after_title;}
			echo "<option value='".get_term_link($category_id,"product_cat")."'>".$title."</option>";
			foreach ($subcategories as $key => $subcategory) {
				if ($current_term_id==$subcategory->term_id){$selected = "selected";} else {$selected = "";}
				echo "<option ".$selected." value='".get_category_link($subcategory->term_id)."'>- $subcategory->name</option>";
			}
		echo '</select>';
		echo '</div>';
	}
}