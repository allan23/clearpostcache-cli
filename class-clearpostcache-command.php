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

		$defaults	 = array(
			'posts_per_page' => -1,
			'post_status'	 => 'any'
		);
		$query_args	 = array_merge( $defaults, $assoc_args );
		$query		 = new WP_Query( $query_args );
		$cleared	 = 0;
		foreach ( $query->posts as $post ) {
			clean_post_cache( $post->ID );
			$cleared++;
		}
		WP_CLI::success( sprintf( '%d posts flushed.', $cleared ) );
	}

}

WP_CLI::add_command( 'clearpostcache', 'Clearpostcache_Command' );
