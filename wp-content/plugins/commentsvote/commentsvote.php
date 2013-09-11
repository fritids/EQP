<?php
/*
Plugin Name: Comments Vote
Plugin URI: http://www.bivings.com
Description: This plugin enables website users to vote comments up or down.
Author: The Bivings Group
Version: 1.0
Author URI: http://www.bivings.com/
*/

/*  Copyright 2008-2009 The Bivings Group (email : commentsvote@bivings.com)

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
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

register_activation_hook( __FILE__, 'commentsvote_install' );

require_once( dirname( __FILE__) . '/commentsvote_inc.php' );

add_action( 'admin_menu', 'commentsvote_admin_menu' );
add_action( 'wp_head', 'commentsvote_js_header' );

//----------------------------------------------------------------------

function commentsvote_admin_menu()
{
	add_options_page( 'Comments Vote', 'Comments Vote', 8, dirname( __FILE__ ) . '/commentsvote_admin.php' );
}

function commentsvote_js_header()
{
	global $commentsvotePath, $commentsvoteEnable;
	
	//Only fully enable the plugin when we're on a front-end page; the admin section doesn't call this function
	add_filter( 'get_comment_author_link', 'commentsvotePanel' );
	add_filter( 'comment_text', 'commentsvoteContent' );
	
	$commentsvotePath = $wp_object_cache->cache[ 'options' ][ 'alloptions' ][ 'siteurl' ] . '/wp-content/plugins/commentsvote';
	
	wp_print_scripts( array( 'sack' ) );
	
	?>
	
	<link href="<?php echo $commentsvotePath; ?>/style.css" rel="stylesheet" type="text/css" />
	
	<script type="text/javascript">
	
	function votecomment( commentID, vote )
	{
		var mysack = new sack( "<?php echo $commentsvotePath; ?>/commentsvote_ajax.php" );

		mysack.method = 'POST';
		
		mysack.setVar( 'vc_comment', commentID );
		mysack.setVar( 'vc_vote', vote );
		
		mysack.onError	= function() { alert( 'Voting error.' ) };
		mysack.onCompletion = function() { finishVote( commentID, eval( '(' + this.response + ')' )); }
		
		mysack.runAJAX();
	}
	
	function finishVote( commentID, response )
	{
		var currentVote	= response.votes;
		
		var vote_span_class	= '';
		var message = response.message;
		
		message	+= '<br />&nbsp;';

		if( currentVote > 0 )
		{
			currentVote	= '+' + currentVote;
			
			vote_span_class	= 'commentsvote_positive';
		}
		else if( currentVote < 0 )
		{
			vote_span_class	= 'commentsvote_negative';
		}
		else
		{
			currentVote	= '';
		}

		document.getElementById( 'commentsvote_span_' + commentID ).className = vote_span_class;

		document.getElementById( 'commentsvote_span_' + commentID ).innerHTML = currentVote;

		document.getElementById( 'commentsvote_results_div_' + commentID ).innerHTML = message;
	}
	
	</script>
	
	<?php
}

function commentsvotePanel($author=false)
{
	global $commentsvoteOptions, $commentsvotePath;
	global $comment;
	
	if( !isset( $commentsvoteOptions ) )
		$commentsvoteOptions = CVGetOptions();
	
	$votes = CVGetCommentVote($comment->comment_ID);
	$comment->_cv_vote = $votes;
	
	if( $votes == 0 || !$commentsvoteOptions[ 'display_rating' ] ) {
		$class = '';
		$votes = '';
	} else if($votes < 0)
		$class = 'commentsvote_negative';
	else {
		$class = 'commentsvote_positive';
		
		$votes = '+' . $votes;
	}
	
//	$results = '<div id="commentsvote_results_div_' . $comment->comment_ID . '"></div>
//                    <a href="javascript:void(0);" onclick="votecomment( ' . $comment->comment_ID . ', -1 );" class="aproved-comment" title="Apoyar comentario">Si</a>
//                    <a href="javascript:void(0);" onclick="votecomment( ' . $comment->comment_ID . ', 1 );" class="desaproved-comment" title="Desaprobar comentario">No</a>
//	
//	<span class="' . $class . '" id="commentsvote_span_' . $comment->comment_ID . '">' . $votes . '</span>
//	
//	<a href="javascript:void(0);" onclick="votecomment( ' . $comment->comment_ID . ', -1 );" title="Vote -1"><img src="' . $commentsvotePath . '/images/vote_down.jpg" alt="Vote -1" border="0" /></a>
//	
//	<a href="javascript:void(0);" onclick="votecomment( ' . $comment->comment_ID . ', 1 );" title="Vote +1"><img src="' . $commentsvotePath . '/images/vote_up.jpg" alt="Vote +1" border="0" /></a>' . $author;
        
        $results = '<li><a href="javascript:void(0);" onclick="votecomment( ' . $comment->comment_ID . ', 1 );" class="aproved-comment" title="Apoyar comentario">Si</a></li>';
        $results .= '<li><a href="javascript:void(0);" onclick="votecomment( ' . $comment->comment_ID . ', -1 );" class="desaproved-comment" title="Desaprobar comentario">No</a></li>';
        $results .= '<li><span class="' . $class . '" id="commentsvote_span_' . $comment->comment_ID . '">' . $votes . '</span></li>';
        $results .= '<li><div id="commentsvote_results_div_' . $comment->comment_ID . '"></div></li>';
	return $results;
}

function commentsvoteContent($content)
{
	global $commentsvoteOptions;
	global $comment;
	
	if( !isset( $commentsvoteOptions ) )
		$commentsvoteOptions = CVGetOptions();
	
	$hideComment = ($commentsvoteOptions[ 'threshold' ] > 0 && ( $comment->_cv_vote <= -$commentsvoteOptions[ 'threshold' ] ) );
	
	if( $hideComment ) {
		$content = '<a href="javascript:void(0);" onclick="if(document.getElementById(\'hidden_comment_' . $comment->comment_ID . '\').style.display==\'block\'){document.getElementById(\'hidden_comment_' . $comment->comment_ID . '\').style.display=\'none\';}else{document.getElementById(\'hidden_comment_' . $comment->comment_ID . '\').style.display=\'block\';}">(click to show comment)</a><div id="hidden_comment_' . $comment->comment_ID . '" style="display:none;"><p>' . $content . '</p></div>';
	}
	
	return $content;
}

function commentsvote_install()
{
	global $wpdb;
	
	$dbVersion = get_option('commentsvote_db_version');
	
	if( $dbVersion < 1 )
	{
		$cvTable = $wpdb->prefix . 'commentsvote';
		$wpdb->query( "CREATE TABLE $cvTable (
			id INT UNSIGNED NOT NULL AUTO_INCREMENT,
			commentID INT UNSIGNED NOT NULL,
			userID INT UNSIGNED NOT NULL,
			vote TINYINT(1) NOT NULL,
			ip INT UNSIGNED NOT NULL,
			voteTime INT UNSIGNED NOT NULL,
			UNIQUE KEY (id),
			INDEX (commentID, userID),
			INDEX (commentID, ip)
			)"
		);
		
		add_option( 'commentsvote_db_version', 1 );
		add_option( 'commentsvote_display_rating', 1 );
		add_option( 'commentsvote_threshold', 0 );
		add_option( 'commentsvote_require_login', 0 );
	}
}

?>
