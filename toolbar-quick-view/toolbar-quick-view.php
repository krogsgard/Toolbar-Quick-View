<?php
/*
Plugin Name: Toolbar Quick View
Plugin URI: http://krogsgard.com/toolbar-quick-view
Description: Adds a "View" menu to the toolbar with quick links to common admin pages. The links are identical to the "add new" menu in the toolbar, except that it goes to the appropriate top level admin pages. 
Version: 0.1
Author: Brian Krogsgard
Author URI: http://krogsgard.com/

 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU 
 * General Public License version 2, as published by the Free Software Foundation.  You may NOT assume 
 * that you can use any other version of the GPL.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without 
 * even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @author Brian Krogsgard <brian@krogsgard.com>
 * @copyright Copyright (c) 2012, Brian Krogsgard
 * @link http://krogsgard.com/toolbar-quick-view
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html

/**
 * Add "View" menu to the toolbar.
 *
 * @since 0.1
 */
 
add_action( 'admin_bar_menu', 'bk_toolbar_view_quicklinks_menu', 90 );

function bk_toolbar_view_quicklinks_menu( $wp_admin_bar ) {
	$actions = array();

	$veiwcpts = (array) get_post_types( array( 'show_in_admin_bar' => true ), 'objects' );

	if ( isset( $veiwcpts['post'] ) && current_user_can( $veiwcpts['post']->cap->edit_posts ) ) {
		$actions[ 'edit.php' ] = array( $veiwcpts['post']->labels->name, 'view-post' );
		unset( $veiwcpts['post'] );
	}

	if ( current_user_can( 'upload_files' ) )
		$actions[ 'upload.php' ] = array( _x( 'Media', 'view from admin bar' ), 'view-media' );

	if ( current_user_can( 'manage_links' ) )
		$actions[ 'link-manager.php' ] = array( _x( 'Links', 'view from admin bar' ), 'view-link' );

	if ( isset( $veiwcpts['page'] ) && current_user_can( $veiwcpts['page']->cap->edit_posts ) ) {
		$actions[ 'edit.php?post_type=page' ] = array( $veiwcpts['page']->labels->name, 'view-page' );
		unset( $veiwcpts['page'] );
	}

	// Add any additional custom post types.
	foreach ( $veiwcpts as $vcpt ) {
		if ( ! current_user_can( $vcpt->cap->edit_posts ) )
			continue;

		$key = 'edit.php?post_type=' . $vcpt->name;
		$actions[ $key ] = array( $vcpt->labels->name, 'view-' . $vcpt->name );
	}

	if ( current_user_can( 'create_users' ) || current_user_can( 'promote_users' ) )
		$actions[ 'users.php' ] = array( _x( 'Users', 'view from admin bar' ), 'view-user' );

	if ( ! $actions )
		return;

	$title = '<span class="view-label">' . _x( 'View', 'admin bar menu group label' ) . '</span>';

	$wp_admin_bar->add_node( array(
		'id'    => 'view-content',
		'title' => _x( 'View', 'admin bar menu group label' ),
		'href'  => admin_url( current( array_keys( $actions ) ) ),
		'meta'  => array(
			'title' => _x( 'View', 'admin bar menu group label' ),
		),
	) );

	foreach ( $actions as $link => $action ) {
		list( $title, $id ) = $action;

		$wp_admin_bar->add_node( array(
			'parent'    => 'view-content',
			'id'        => $id,
			'title'     => $title,
			'href'      => admin_url( $link )
		) );
	}
}

