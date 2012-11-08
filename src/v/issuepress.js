var app = app || {};

// The Application
// ---------------

app.AppView = Backbone.View.extend({

  el: 'ul#repo-list',

  events: {
    "click .repo-name": "log",
  },

  initialize: function(){
    
    _.each(app.repoNames, function(i, q) {
      $(this.el).html(app.repoNames.at(q).get("name"));
    });


  },

  log: function() {
    console.log(this.model);
  },


});
