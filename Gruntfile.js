module.exports = function(grunt) {

  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),

    compass: {
      dist: {
        options: {
          sassDir: 'assets/scss',
          cssDir: 'assets/css',
          trace: true,
          force: true,
          environment: 'production'
        }
      }
    },

    jshint: {
      all: ['src/app/**/*.js']
    },

    concat: {
      basic: {
        src: ['src/util/**/*.js', 'src/app/**/*.js', 'src/app/issuepress.js'],
        dest: 'build/main.js'
      }
    },

    uglify: {
      my_target: {
        options: {
          mangle: false
        },
        files: {
          'build/main.js': ['build/main.js']
        }
      }
    },

    watch: {
      css: {
        files: 'assets/scss/**/*.scss',
        tasks: ['compass']
      },
      scripts: {
        files: ['public/assets/js/app/**/*.js', ],
        tasks: ['jshint', 'concat', 'uglify'],
        options: {
          interrupt: true,
        }
      },
    }
  });

  grunt.loadNpmTasks('grunt-contrib-jshint');
  grunt.loadNpmTasks('grunt-contrib-compass');
  grunt.loadNpmTasks('grunt-contrib-concat');
  grunt.loadNpmTasks('grunt-contrib-uglify');
  grunt.loadNpmTasks('grunt-contrib-watch');
  grunt.registerTask('default',['watch']);
  grunt.registerTask('lint',['jshint']);

};
