angular.module('user', [
  'ui.gravatar'
])

.factory('IPUser', function(){

  user = {};
  
  user.data = window.IP_user;
  
  user.login_link = window.IP_login;

  user.logout_link = window.IP_logout;

  logout = function() {
    console.log(user.data);
    console.log("logged out");
    user.data = null;
    console.log(user.data);
  }


  return { 
    user: user.data,
    login_link: user.login_link,
    logout_link: user.logout_link,
    logout: function(c_user){
      return logout(c_user); 
    }
  }
})

