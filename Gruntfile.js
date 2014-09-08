module.exports = function(grunt) {

  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),

    compass: {
      admin: {
        options: {
          sassDir: 'admin/scss',
          cssDir: 'admin/css',
          trace: true,
          force: true,
          outputStyle: 'expanded',
          environment: 'production'
        },
      },
      public: {
        options: {
          sassDir: 'public/scss',
          cssDir: 'public/css',
          trace: true,
          force: true,
          outputStyle: 'expanded',
          environment: 'production'
        }
      }
    },

    jshint: {
      all: ['admin/js/src/**/*.js', 'public/js/src/**/*.js', 'Gruntfile.js']
    },

    concat: {
      admin: {
        src: ['admin/js/src/**/*.js'],
        dest: 'admin/js/issuepress-admin.js'
      },
      public: {
        src: ['public/js/src/**/*.js'],
        dest: 'public/js/issuepress-public.js'
      },
    },

    uglify: {
      admin: {
        options: {
          mangle: false
        },
        files: {
          'admin/js/issuepress-admin.js': ['admin/js/issuepress-admin.js']
        }
      },
      public: {
        options: {
          mangle: false
        },
        files: {
          'public/js/issuepress-public.js': ['public/js/issuepress-public.js']
        }
      }
    },

    watch: {
      admin_scss: {
        files: 'admin/scss/**/*.scss',
        tasks: ['compass:admin']
      },
      public_scss: {
        files: 'public/scss/**/*.scss',
        tasks: ['compass:public']
      },
      gruntfile: {
        files: ['Gruntfile.js'],
        tasks: ['jshint'],
        options: {
          interrupt: true,
        }
      },
      admin_scripts: {
        files: ['admin/js/src/**/*.js'],
        tasks: ['jshint', 'concat:admin'], //, 'uglify'],
        options: {
          interrupt: true,
        }
      },
      public_scripts: {
        files: ['public/js/src/**/*.js'],
        tasks: ['jshint', 'concat:public'], //, 'uglify'],
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
  grunt.registerTask('build',['concat']);

};
