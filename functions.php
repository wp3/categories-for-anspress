<?php


/**
 * Output question categories
 * @param  array $args
 * @return string
 */
function ap_question_categories_html($args = array()) {

	$defaults  = array(
		'question_id'   => get_the_ID(),
		'list'           => false,
		'tag'           => 'span',
		'class'         => 'question-categories',
		'label'         => __( 'Categories', 'categories-for-anspress' ),
		'echo'          => false,
	);

	if ( ! is_array( $args ) ) {
		$defaults['question_id'] = $args;
		$args = $defaults;
	} else {
		$args = wp_parse_args( $args, $defaults );
	}

	$cats = get_the_terms( $args['question_id'], 'question_category' );

	if ( $cats ) {
		$o = '';
		if ( $args['list'] ) {
			$o = '<ul class="'.$args['class'].'">';
			foreach ( $cats as $c ) {
				$o .= '<li><a href="'.esc_url( get_term_link( $c ) ).'" title="'.$c->description.'">'. $c->name .'</a></li>';
			}
			$o .= '</ul>';

		} else {
			$o = $args['label'];
			$o .= '<'.$args['tag'].' class="'.$args['class'].'">';
			foreach ( $cats as $c ) {
				$o .= '<a href="'.esc_url( get_term_link( $c ) ).'" title="'.$c->description.'">'. $c->name .'</a>';
			}
			$o .= '</'.$args['tag'].'>';
		}
		if ( $args['echo'] ) {
			echo $o; }

		return $o;
	}

}


function ap_category_details() {

	$var = get_query_var( 'question_category' );

	$category = get_term_by( 'slug', $var, 'question_category' );

	echo '<div class="clearfix">';
	echo '<h3><a href="'.get_category_link( $category ).'">'. $category->name .'</a></h3>';
	echo '<div class="ap-taxo-meta">';
	echo '<span class="count">'. $category->count .' '.__( 'Questions', 'categories-for-anspress' ).'</span>';
	echo '<a class="aicon-rss feed-link" href="' . get_term_feed_link( $category->term_id, 'question_category' ) . '" title="Subscribe to '. $category->name .'" rel="nofollow"></a>';
	echo '</div>';
	echo '</div>';

	echo '<p class="desc clearfix">'. $category->description .'</p>';

	$child = get_terms( array( 'taxonomy' => 'question_category' ), array( 'parent' => $category->term_id, 'hierarchical' => false, 'hide_empty' => false ) );

	if ( $child ) :
		echo '<ul class="ap-child-list clearfix">';
		foreach ( $child as $key => $c ) :
			echo '<li><a class="taxo-title" href="'.get_category_link( $c ).'">'.$c->name.'<span>'.$c->count.'</span></a>';
			echo '</li>';
			endforeach;
		echo'</ul>';
	endif;
}
function ap_sub_category_list($parent) {
	$categories = get_terms( array( 'taxonomy' => 'question_category' ), array( 'parent' => $parent, 'hide_empty' => false ) );

	if ( $categories ) {
		echo '<ul class="ap-sub-taxo ap-ul-inline clearfix">';
		foreach ( $categories as $cat ) {
			echo '<li><a href="'.get_category_link( $cat ).'">' .$cat->name.'<span>'.$cat->count.'</span></a></li>';
		}
		echo '</ul>';
	}
}

function ap_question_have_category($post_id = false) {
	if ( ! $post_id ) {
		$post_id = get_the_ID(); }

	$categories = wp_get_post_terms( $post_id, 'question_category' );
	if ( ! empty( $categories ) ) {
		return true; }

	return false;
}


/**
 * Check if anspress categories page
 * @return boolean
 * @since  1.0
 */
if ( ! function_exists( 'is_question_categories' ) ) {
	function is_question_categories() {
		if ( ap_get_categories_slug() == get_query_var( 'ap_page' ) ) {
			return true;
		}

		return false;
	}
}

if ( ! function_exists( 'is_question_category' ) ) {
	function is_question_category() {
		if ( 'category' == get_query_var( 'ap_page' ) ) {
			return true; }

		return false;
	}
}

function ap_category_sorting() {
	$args = array(
		'hierarchical'      => true,
		'hide_if_empty'     => true,
		'number'            => 10,
	);

	$terms = get_terms( 'question_category', $args );
	$selected = isset( $_GET['ap_cat_sort'] ) ? (int) $_GET['ap_cat_sort'] : '';
	if ( $terms ) {
		echo '<div class="ap-dropdown">';
			echo '<a id="ap-sort-anchor" class="ap-dropdown-toggle'.($selected != '' ? ' active' : '').'" href="#">'.__( 'Category', 'categories-for-anspress' ).'</a>';
			echo '<div class="ap-dropdown-menu">';
		foreach ( $terms as $t ) {
			echo '<li '.($selected == $t->term_id ? 'class="active" ' : '').'><a href="#" data-value="'.$t->term_id.'">'. $t->name .'</a></li>';
		}
			echo '<input name="ap_cat_sort" type="hidden" value="'.$selected.'" />';
			echo '</div>';
		echo '</div>';
	}
}

function ap_get_category_image($term_id) {
	$option = get_option( 'ap_cat_'.$term_id );
	$color = isset( $option['ap_color'] ) && $option['ap_color'] != '' ? ' style="background:'.$option['ap_color'].'"' : ' style="background:#333"';
	if ( isset( $option['ap_image']['url'] ) && $option['ap_image']['url'] != '' ) {
		echo '<img class="ap-category-image" src="'.$option['ap_image']['url'].'" />';
	} elseif ( isset( $option['ap_icon'] ) && $option['ap_icon'] != '' ) {
		echo '<span class="ap-category-icon '.$option['ap_icon'].'"'.$color.'></span>';
	} else {
		echo '<span class="ap-category-icon apicon-category"'.$color.'></span>';
	}

}

/**
 * Slug for categories page
 * @return string
 */
function ap_get_categories_slug() {
	$slug = ap_opt('categories_page_slug');
	$slug = sanitize_title( $slug );

	if(empty($slug)){
		$slug = 'categories';
	}
	/**
	 * FILTER: ap_categories_slug
	 */
	return apply_filters( 'ap_categories_slug', $slug );
}

/**
 * Slug for category page
 * @return string
 */
function ap_get_category_slug() {
	$slug = ap_opt('category_page_slug');
	$slug = sanitize_title( $slug );

	if(empty($slug)){
		$slug = 'category';
	}
	/**
	 * FILTER: ap_category_slug
	 */
	return apply_filters( 'ap_category_slug', $slug );
}
