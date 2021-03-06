<?php
class SuggestTags extends Plugin
{
	/**
	 * Add the required javascript to the publish page
	 * @param Theme $theme The admin theme instance
	 **/
	public function action_header( $theme )
	{
	//	if( $theme->page == 'publish' ) {
			Stack::add( 'header_javascript', Site::get_url( 'vendor' ) . "/multicomplete.js", 'multicomplete', array( 'jquery.ui' ) );
			$url = '"' . URL::get( 'ajax', array( 'context' => 'auto_tags' ) ) . '"';
			$script = <<< HEADER_JS
$(document).ready(function(){
	$("#tags").multicomplete({source: $url,
		minLength: 1
	});
});
HEADER_JS;
			Stack::add( 'header_javascript',  $script, 'tags_auto', array( 'jquery', 'multicomplete' ) );
	//	}
	}

	/**
	 * Respond to Javascript callbacks
	 * The name of this method is action_ajax_ followed by what you passed to the context parameter above.
	 */
	public function action_ajax_auto_tags( $handler )
	{
		$selected = array();
		if( isset( $handler->handler_vars['selected'] ) ) {
			$selected = $handler->handler_vars['selected'];
		}
		if( isset( $handler->handler_vars['term'] ) && MultiByte::strlen( $handler->handler_vars['term'] ) ) {
			$tags = Tags::vocabulary()->get_search( $handler->handler_vars['term'], 'term_display ASC' );
		}
		else {
			$tags = Tags::vocabulary()->get_tree();
		}

		$resp = array();
		foreach ( $tags as $tag ) {
			$resp[] = array(
			    'label' => $tag->term_display,
			    'value' => MultiByte::strpos( $tag->term_display, ',' ) === false ? $tag->term_display : $tag->tag_text_searchable
			);
		}
		$resp = array_diff($resp, $selected );
		// Send the response
		echo json_encode( $resp );
	}

}
?>
