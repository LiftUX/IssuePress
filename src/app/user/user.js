angular.module('user', [
  'AppState'
])

.factory('IPUser', ['IPAppState', function(IPAppState){

  user = {};
  user.data = IPAppState.IP_user;
  user.login_link = IPAppState.IP_login;
  user.logout_link = IPAppState.IP_logout;

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
}]);

