<?php
/*
*
* All customizer helper function here.
*
*/


//Sanitize theme style
if ( ! function_exists( 'beshop_cat_select_sanitize' ) ) :
function beshop_cat_select_sanitize($value){ 
	$args=array(
		'orderby'    => 'count',
		'hide_empty' => 0,
		); 
	 $terms = get_terms( 'category',$args ); 
		
	$cat= array();
	$cat[]= 'all';
	if ( ! empty( $terms ) && ! is_wp_error( $terms ) ):
        foreach ($terms as $term) :
			$cat[] = $term->slug;
        endforeach;
	endif;
    if(!in_array($value, $cat)){
        $value = 'all';
    }
    return $value;
}
endif;


if ( ! function_exists( 'beshop_sanitize_image' ) ) :
function beshop_sanitize_image( $input ){
    /* default output */
    $output = '';
    /* check file type */
    $filetype = wp_check_filetype( $input );
    $mime_type = $filetype['type'];
    /* only mime type "image" allowed */
    if ( strpos( $mime_type, 'image' ) !== false ){
        $output = $input;
    }
    return $output;
}
endif;


//Sanitize numeric CSS values (e.g., font size, line height)
if ( ! function_exists( 'beshop_sanitize_css_numeric' ) ) :
function beshop_sanitize_css_numeric( $value ) {
    // Remove any non-numeric characters except period and minus sign
    $value = preg_replace( '/[^0-9.-]/', '', $value );
    
    // If empty or not numeric, return empty string
    if ( empty( $value ) || ! is_numeric( $value ) ) {
        return '';
    }
    
    // Convert to float and then to string to remove any extra characters
    return (string) floatval( $value );
}
endif;


// Sanitize CSS color values (hex, rgb, rgba, hsl, hsla)
if ( ! function_exists( 'beshop_sanitize_css_color' ) ) :
function beshop_sanitize_css_color( $color ) {
    // If empty, return empty string
    if ( empty( $color ) ) {
        return '';
    }
    
    // Remove any newlines, returns, tabs, and double spaces
    $color = str_replace( array( "\r", "\n", "\t", "  " ), ' ', $color );
    
    // Check if it's a hex color
    if ( preg_match( '/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/', $color ) ) {
        return $color;
    }
    
    // Check if it's an rgb/rgba color
    if ( preg_match( '/^rgba?\([0-9,.\s]+\)$/', $color ) ) {
        return $color;
    }
    
    // Check if it's an hsl/hsla color
    if ( preg_match( '/^hsla?\([0-9,.%\s]+\)$/', $color ) ) {
        return $color;
    }
    
    // If none of the above, return empty string
    return '';
}
endif;


// Sanitize CSS text values (font family, text alignment, etc.)
if ( ! function_exists( 'beshop_sanitize_css_text' ) ) :
function beshop_sanitize_css_text( $text ) {
    // If empty, return empty string
    if ( empty( $text ) ) {
        return '';
    }
    
    // Convert to string to handle null values
    $text = (string) $text;
    
    // Remove any newlines, returns, tabs, and double spaces
    $text = str_replace( array( "\r", "\n", "\t", "  " ), ' ', $text );
    
    // Remove any characters that could be used for CSS injection
    // Using proper Unicode ranges instead of \u notation
    $text = preg_replace( '/[^a-zA-Z0-9\s\-_,.()"\'\x{0080}-\x{9fff}]/u', '', $text );
    
    // Limit length to prevent excessive data
    if ( strlen( $text ) > 100 ) {
        $text = substr( $text, 0, 100 );
    }
    
    return $text;
}
endif;


//All sanitize function

//Sanitize sns position
if ( ! function_exists( 'beshop_sanitize_sns_position' ) ) :
function beshop_sanitize_sns_position($value){ 
    if(!in_array($value, array('left_search','right_search'))){
        $value = 'left_search';
    }
    return $value;
}
endif;




if ( ! function_exists( 'beshop_img_logo_on' ) ) :
function beshop_img_logo_on(){
    if (has_custom_logo()) {
        return true;
    }else{
    return false;
    }

}
endif;

//Sanitize sidebar options
if ( ! function_exists( 'beshop_sanitize_theme_font' ) ) :
function beshop_sanitize_theme_font($value){ 
    if(!in_array($value, array('Poppins','Noto Serif','Roboto','Open Sans','Lato','Montserrat','Crimson Text'))){
        $value = 'Poppins';
    }
    return $value;
}
endif;
//Sanitize sidebar options
if ( ! function_exists( 'beshop_sanitize_theme_head_font' ) ) :
function beshop_sanitize_theme_head_font($value){ 
    if(!in_array($value, array('Poppins','Noto Serif','Roboto','Open Sans','Lato','Montserrat','Crimson Text'))){
        $value = 'Noto Serif';
    }
    return $value;
}
endif;