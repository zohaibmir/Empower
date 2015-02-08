
module.exports = function(grunt) {
    
    // Load Grunt tasks declared in the package.json file
    //require('matchdep').filterDev('grunt-*').forEach(grunt.loadNpmTasks);
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),

        sass: {
            options: {
                includePaths: ['bower_components/foundation/scss']
            },
            dist: {
                options: {
                    outputStyle: 'compressed'
                },
                files: {
                    'stylesheets/app.css': 'scss/app.scss'
                }        
            }
        },
        
        uglify: {
            options: {
                mangle: false
            },
            my_target: {
                files: {
                    'js/app.min.js': ['js/app.js']
                }
            }
        },
        express: {
            all: {
                options: {
                    port: 9000,
                    hostname: "127.0.0.1",
                    bases: [__dirname]
                    //livereload: true
                }
            }
        },

        watch: {
            grunt: {
                files: ['Gruntfile.js']
            },

            sass: {
                files: 'scss/**/*.scss',
                tasks: ['sass']
            },            
           
            /* watch and see if our javascript files change, or new packages are installed */
            js: {
                files: ['js/app.js', 'bower_components/**/*.js'],
                tasks: ['uglify']
            },
            /* watch our files for change, reload */
            livereload: {
                files: ['*.html', 'stylesheets/*.css', 'images/*', '{app.min.js, plugins.min.js}'],
                options: {
                    livereload: true
                }
            }
        },
        open: {
            all: {
                // Gets the port from the connect configuration
                path: 'http://localhost:<%= express.all.options.port%>'
            }
        }
    });

    grunt.loadNpmTasks('grunt-sass');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-express');
    grunt.loadNpmTasks('grunt-open');
    
    // Creates the `server` task
    grunt.registerTask('server', [
        'express',
        'open'    
        ]);
    grunt.registerTask('build', ['sass']);
    grunt.registerTask('default', ['server','build','watch']);
}