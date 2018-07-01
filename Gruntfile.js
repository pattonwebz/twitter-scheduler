/**
 * This is the plugins main Gruntfile.js for task definitions.
 *
 * @package         Twitter Scheduler
 * @since           0.1.0
 * @author          William Patton <will@pattonwebz.com>
 * @copyright       Copyright (c) 2018, William Patton
 * @license         http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

module.exports = function( grunt ) {

	'use strict';
	var banner = '/**\n * <%= pkg.homepage %>\n * Copyright (c) <%= grunt.template.today("yyyy") %>\n * This file is generated automatically. Do not edit.\n */\n';
	// Project configuration.
	grunt.initConfig(
		{

			pkg: grunt.file.readJSON( 'package.json' ),

			addtextdomain: {
				options: {
					textdomain: 'twitter-scheduler',
				},
				target: {
					files: {
						src: [ '*.php', '**/*.php', '!node_modules/**', '!php-tests/**', '!bin/**' ]
					}
				}
			},

			wp_readme_to_markdown: {
				your_target: {
					files: {
						'README.md': 'readme.txt'
					}
				},
			},

			makepot: {
				target: {
					options: {
						domainPath: '/languages',
						mainFile: 'twitter-scheduler.php',
						potFilename: 'twitter-scheduler.pot',
						potHeaders: {
							poedit: true,
							'x-poedit-keywordslist': true
						},
						type: 'wp-plugin',
						updateTimestamp: true
					}
				}
			},
		}
	);

	grunt.loadNpmTasks( 'grunt-wp-i18n' );
	grunt.loadNpmTasks( 'grunt-wp-readme-to-markdown' );
	grunt.registerTask( 'i18n', ['addtextdomain', 'makepot'] );
	grunt.registerTask( 'readme', ['wp_readme_to_markdown'] );

	grunt.util.linefeed = '\n';

};
