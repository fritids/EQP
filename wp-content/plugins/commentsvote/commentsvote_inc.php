<?php
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

function CVGetCommentVote($commentID)
{
	global $wpdb;
	
	$cvTable = $wpdb->prefix . 'commentsvote';
	
	$votes = $wpdb->get_var( $wpdb->prepare("SELECT SUM(vote) AS votes FROM $cvTable WHERE commentID = %d", $commentID));
	
	if( !isset( $votes ) ) //there is no vote
		return 0;
	else
		return (int)$votes;
}

function CVGetOptions()
{
	return array(
		'display_rating' => get_option( 'commentsvote_display_rating' ),
		'threshold' => get_option( 'commentsvote_threshold' ),
		'require_login' => get_option( 'commentsvote_require_login' )
	);
}

?>
