module.exports = function(grunt) {

  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),

    compass: {
      dist: {
        options: {
          sassDir: 'src/styles',
          cssDir: 'assets/css',
          trace: true,
          force: true,
          outputStyle: 'compressed',
          environment: 'production'
        }
      }
    },

    jshint: {
      all: ['src/app/**/*.js', 'Gruntfile.js']
    },

    concat: {
      basic: {
        src: ['src/util/**/*.js', 'src/app/**/*.js', 'src/app/issuepress.js'],
        dest: 'build/main.js'
      }
    },

    uglify: {
      target: {
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
        files: 'src/styles/**/*.scss',
        tasks: ['compass']
      },
      scripts: {
        files: ['app/**/*.js', 'Gruntfile.js' ],
        tasks: ['jshint', 'concat'], //, 'uglify'],
        options: {
          interrupt: true,
        }
      },
    },

  });

  grunt.loadNpmTasks('grunt-contrib-jshint');
  grunt.loadNpmTasks('grunt-contrib-compass');
  grunt.loadNpmTasks('grunt-contrib-concat');
  grunt.loadNpmTasks('grunt-contrib-uglify');
  grunt.loadNpmTasks('grunt-contrib-watch');
  grunt.registerTask('default',['watch']);
  grunt.registerTask('lint',['jshint']);
  grunt.registerTask('build',['concat']);

};
