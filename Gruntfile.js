module.exports = function(grunt) {

    grunt.initConfig({
        concat: {
            css: {
                src: 'app/assets/css/*.css',
                dest: 'app/assets/css/master.css'
            },

            js: {
                src: 'app/assets/js/*.js',
                dest: 'app/assets/js/main.js'
            }
        },
        uglify: {
            js: {
                src: 'app/assets/js/main.js',
                dest: 'app/public/main.min.js'
            }
        },
        cssmin: {
            css: {
                src: 'app/assets/css/master.css',
                dest: 'app/public/master.min.css'
            }
        },
        watch: {
            files: ['app/assets/*', 'app/public/*.min.*'],
            tasks: ['concat', 'uglify', 'cssmin']
        }
    });

    grunt.loadNpmTasks('grunt-contrib-concat');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-cssmin');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.registerTask('default', ['concat', 'uglify', 'cssmin']);
};