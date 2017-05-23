
module.exports = function(grunt) {

	var pkg = grunt.file.readJSON('package.json');

	pkg.banner = '/*! \n ' +
	' * @package    <%= name %>\n' +
	' * @version    <%= version %>\n' +
	' * @date       <%= grunt.template.today("yyyy-mm-dd") %>\n' +
	' * @author     <%= author %>\n' +
	' * @copyright  Copyright (c) <%= grunt.template.today("yyyy") %> <%= copyright %>\n' +
	' */\n';

	pkg.banner = grunt.template.process(pkg.banner, {data: pkg});

	pkg.phpbanner = pkg.banner + "\n defined('_JEXEC') or die; \n";

	// pkg.minify = '.min';
	// var fileVars = pkg;

	// Project configuration.
	grunt.initConfig({

		//Read the package.json (optional)
		pkg: pkg,

		// Metadata.
		meta: {
			srcPath: '',
			jsPath: 'js/',
			deployPath: '../release/',
			stagePath: '../staging/'
		},

		banner: pkg.banner,

	    clean: {
		    deployDir: {
				src: ['<%= meta.deployPath %>source/**/*'],
				options: {
					force: true // force cleanup outside of cwd
				}
		    }
	    },

		copy: {
			plugin: {
				files: [
					{
						expand: true,
						dot: true,
						cwd: '<%= meta.srcPath %>plg_<%= pkg.name %>/',
						dest: '<%= meta.deployPath %>source/<%= pkg.name %>/',
						src: [
							'**/*.{js,json,css,less,svg,png,jpg,php,html,xml}'
						],
					},
				],
				options: {
					process: function(content,srcPath) {
						if(grunt.file.match('**/*.{js,json,css,less,html,php,xml}', srcPath)) {
							return grunt.template.process(content, {data: pkg});
						} else {
							return content;
						}
					},
					noProcess: ['**/*.{png,gif,jpg,ico,psd}'] // processing would corrupt image files.
				}
			},
			// staging: {
			// 	files: [
			// 		{
			// 			expand: true,
			// 			dot: true,
			// 			src: [ '**/*.*' ],
			// 			cwd: '<%= meta.deployPath %>source/<%= pkg.name %>',
			// 			dest: '<%= meta.stagePath %>templates/<%= pkg.name %>',
			// 		}
			// 	]
			// }
		},

		compress: {
			plugin: {
				options: {
					mode: 'zip',
					archive: '<%= meta.deployPath %><%= pkg.name %>-<%= pkg.version %>.zip'
				},
				files: [{
					cwd: '<%= meta.deployPath %>source/<%= pkg.name %>/',
					src: ['**/*'],
					// dest: '/',
					expand: true
				}]
			},
		},

		// watch: {
		// 	stage : {
		// 		// don't include all dirs as this would include node_modules too!
		// 		// or use !**/node_modules/** to exclude dir
		// 		files: ['<%= meta.srcPath %>template/**/*'],
		// 		tasks: ['clean:deployDir' , 'copy:template', 'copy:staging']
		// 	}
		// }
	});

	// These plugins provide necessary tasks.
	grunt.loadNpmTasks('grunt-contrib-copy');
	grunt.loadNpmTasks('grunt-contrib-concat');
	grunt.loadNpmTasks('grunt-contrib-watch');
	grunt.loadNpmTasks('grunt-contrib-compress');
	grunt.loadNpmTasks('grunt-contrib-clean');

	// Default task
	// grunt.registerTask('default', [ 'copy' ]);
	grunt.registerTask('release', [ 'clean:deployDir' , 'copy:plugin' , 'compress:plugin' ]);
	// grunt.registerTask('stage', [ 'clean:deployDir' , 'copy:template' , 'copy:staging' ]);
	grunt.registerTask('default', function() {
		console.log('Choose one of the registered tasks:');
		console.log('release - compile plugin and create a zip file');
		// console.log('stage / watch:stage - compile template and copy to stage');
	});

};
