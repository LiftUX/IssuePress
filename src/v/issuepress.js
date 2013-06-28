var app = app || {};

// The Application
// ---------------
(function($){
  app.AppView = Backbone.View.extend({

    el: $('ul#repo-list'),

    events: {
    },

    template: _.template("<li class=\"repo\"><a href=\"/support/<%= name %>\"><%= name %></a></li>"),

    initialize: function(){
      
      _.bindAll(this, 'render', 'appendItem');

      this.collection = app.repoNames;

      this.render();
    },

    render: function() {
      var self = this;
      _(this.collection.models).each(function(item){
        self.appendItem(item);
      }, this);

    },

    appendItem: function(item){
      $(this.el).append("<li><a href=\"/support/#!/"+item.get('name')+"\">"+item.get('name')+"</a></li>");
    }

  });
})(jQuery);
