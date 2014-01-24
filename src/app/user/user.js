angular.module('user', [
  'AppState',
  'ui.gravatar',
])

.factory('IPUser', ['IPAppState', 'gravatar', function(IPAppState, gravatar){

  user = {};
  user.data = IPAppState.user;
  user.login_link = IPAppState.login;
  user.logout_link = IPAppState.logout;

  if(IPAppState.user)
    user.data.gravatar_id = gravatar.getEmailHash(IPAppState.user.email);

  logout = function() {
    console.log(user.data);
    console.log("logged out");
    user.data = null;
    console.log(user.data);
  };

  return { 
    user: user.data,
    login_link: user.login_link,
    logout_link: user.logout_link,
    logout: function(c_user){
      return logout(c_user); 
    }
  };
}]);

