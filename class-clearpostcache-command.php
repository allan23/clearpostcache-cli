<?php

/**
 * Clear Post Cache
 * Sometimes you might need to clean all the post caches. Eg after doing a search and replace. Here's an easy way to do this without wiping the whole memcache.
 * @author Allan Collins <allan.collins@10up.com>
 * 
 */
class Clearpostcache_Command extends WP_CLI_Command {

	/**
	 * Queries posts, loops through results and then cleans each post cache.
	 * 
	 * ## OPTIONS
	 *
	 * [--<field>=<value>]
	 * : One or more args to pass to WP_Query.
	 *
	 * ## EXAMPLES
	 *
	 * wp clearpostcache
	 * wp clearpostcache --post_type=post
	 *
	 * @subcommand clearpostcache [--foo=<bar>]
	 */
	public function __invoke( $_, $assoc_args ) {

		$defaults		 = array(
			'posts_per_page' => 100,
			'post_status'	 => 'any',
			'fields'		 => 'ids',
			'paged'			 => 1
		);
		$query_args		 = array_merge( $defaults, $assoc_args );
		$query			 = new WP_Query( $query_args );
		$post_ids		 = $query->posts;
		$current_page	 = $query->get( 'paged' );
		$total			 = $query->max_num_pages;
		$notify			 = \WP_CLI\Utils\make_progress_bar( 'Cleaning Post Caches:', $query->found_posts );
		$done			 = 0;
		while ( $current_page <= $total ) {
			$query->set( 'paged', $current_page );
			$query->get_posts();
			foreach ( $query->posts as $id ) {
				clean_post_cache( $id );
				$notify->tick();
				$done++;
			}
			$current_page++;
		}
		WP_CLI::success( sprintf( '%d of %d posts flushed.', $done, $query->found_posts ) );
	}

}

WP_CLI::add_command( 'clearpostcache', 'Clearpostcache_Command' );
