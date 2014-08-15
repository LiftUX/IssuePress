module.exports = function(grunt) {

  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),

    compass: {
      admin: {
        options: {
          sassDir: 'admin/assets/scss',
          cssDir: 'admin/assets/css',
          trace: true,
          force: true,
          outputStyle: 'expanded',
          environment: 'production'
        },
      },
      public: {
        options: {
          sassDir: 'public/assets/scss',
          cssDir: 'public/assets/css',
          trace: true,
          force: true,
          outputStyle: 'expanded',
          environment: 'production'
        }
      }
    },

    jshint: {
      all: ['admin/assets/js/src/**/*.js', 'public/assets/js/src/**/*.js', 'Gruntfile.js']
    },

    concat: {
      admin: {
        src: ['admin/assets/js/src/**/*.js'],
        dest: 'admin/assets/js/admin.js'
      },
      public: {
        src: ['public/assets/js/src/**/*.js'],
        dest: 'public/assets/js/public.js'
      },
    },

    uglify: {
      admin: {
        options: {
          mangle: false
        },
        files: {
          'admin/assets/js/admin.js': ['admin/assets/js/admin.js']
        }
      },
      public: {
        options: {
          mangle: false
        },
        files: {
          'public/assets/js/public.js': ['public/assets/js/public.js']
        }
      }
    },

    watch: {
      admin_scss: {
        files: 'admin/assets/scss/**/*.scss',
        tasks: ['compass:admin']
      },
      public_scss: {
        files: 'public/assets/scss/**/*.scss',
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
        files: ['admin/assets/js/src/**/*.js'],
        tasks: ['jshint', 'concat:admin'], //, 'uglify'],
        options: {
          interrupt: true,
        }
      },
      public_scripts: {
        files: ['public/assets/js/src/**/*.js'],
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
