function hideRegistration() {
  var regForm = document.getElementById('form-registration');
  regForm.style.display = 'none';
}

function showRegistration() {
  var regForm = document.getElementById('form-registration');
  regForm.style.display = 'inline';

  var signInForm = document.getElementById('form-signin');
  signInForm.style.display = 'none';
}

function showSignIn() {
  var regForm = document.getElementById('form-registration');
  regForm.style.display = 'none';

  var signInForm = document.getElementById('form-signin');
  signInForm.style.display = 'inline';
}

function validateSignOn() {
	var username = document.getElementById('inputUsername').value;
  	var password = document.getElementById('inputPassword').value;

	var signOnReturnFunc = function(request) {
    	return function() {
    		if (request.readyState == 4) {
       		 var errContainer = document.getElementById('signOnErrMessages');

        	 switch (request.responseText) {
         	 case 'emptyParams':
            	 errContainer.innerText = 'Please fill out all requirements and re-submit.';
            	 break;
          	case 'authenFailed':
           	 	clearSignOnInfo();
           	 	errContainer.innerText = 'Username or password is incorrect. Please try again.';
           	 	break;
          	case 'loginSuccessful':
            	location.replace('beers.php');
           	 	break;
          	default:
           	 	errContainer.innerText = 'Server error. Retry or call an administrator.';
            break;
        }
      }
    }
  };

  var userParams = {
    validateSignOn: true,
    username: username,
    password: password
  };

  callLoginPhp(signOnReturnFunc, userParams);

  return false;
}

function clearSignOnInfo() {
  var username = document.getElementById('inputUsername').value = '';
  var password = document.getElementById('inputPassword').value = '';
}

function registerUser() {

  var username = document.getElementById('regUsername').value;
  var password = document.getElementById('regPassword').value;
  var passwordRepeated = document.getElementById('regRepeatPassword').value;
  var birthday = document.getElementById('birthday').value;

  var regUserFunc = function(request) {
    return function() {
      if (request.readyState == 4) {
        var errContainer = document.getElementById('regErrMessages');

        switch (request.responseText) {
          case 'emptyParams':
            errContainer.innerText = 'Please fill out all the values and re-submit.';
            break;
          case 'passwordsNotMatching':
            clearRegPasswords();
            errContainer.innerText = 'Please re-enter your ' +
              'passwords. They do not match.';
            break;
          case 'usernameDuplicate':
            clearUsername();
            errContainer.innerText = 'Username already exists. ' +
              'Please enter another username.';
            break;
          default:
            alert('Registration Successful. Please Login.');
            location.reload();
            break;
        }
      }
    }
  };

  // Create Php parameters
  var userRegParams = {
    registerUser: true,
    username: username,
    password: password,
    passwordRepeated: passwordRepeated,
    birthday: birthday
  };

  callLoginPhp(regUserFunc, userRegParams);

  return false;
}

function clearRegPasswords() {
  document.getElementById('regPassword').value = '';
  document.getElementById('regRepeatPassword').value = '';
}

function clearUsername() {
  document.getElementById('regUsername').value = '';
}

function callLoginPhp(returnFunc, postParams) {
  if (typeof(postParams) === 'undefined') {
    postParams = '';
  }

  var request = new XMLHttpRequest();
  var url = 'login.php';
  var postParamsStr = '';

  if (postParams.length !== 0) {
    var i = 0;
    for (var property in postParams) {
      if (postParams.hasOwnProperty(property)) {
        if (i === 0) {postParamsStr += property + '=' + postParams[property];
        }
        else {
          postParamsStr += '&' + property + '=' + postParams[property];
        }
        i++;
      }
    }
  }

  if (!request) {
    return false;
  }

  request.onreadystatechange = returnFunc(request);
  request.open('POST', url, true);
  request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
  request.send(postParamsStr);
  return request;
}

function logout() {

  var signOffReturnFunc = function(request) {
    return function() {
      if (request.readyState == 4) {
        location.replace('login.html');
      }
    }
  };

  var userParams = {
    logoff: true
  };

  callLoginPhp(signOffReturnFunc, userParams);

  return false;
}

function goToLogin() {
  location.replace('login.html');
}