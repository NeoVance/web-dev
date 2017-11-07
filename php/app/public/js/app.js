/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, {
/******/ 				configurable: false,
/******/ 				enumerable: true,
/******/ 				get: getter
/******/ 			});
/******/ 		}
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 0);
/******/ })
/************************************************************************/
/******/ ([
/* 0 */
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(1);
module.exports = __webpack_require__(4);


/***/ }),
/* 1 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__validation_emailsMatch__ = __webpack_require__(2);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__view_dashboard__ = __webpack_require__(3);



/* global fetch */

var authorityElement = document.getElementById('email');
var nonAuthorityElement = document.getElementById('email-verify');
var signInButton = document.getElementById('sign-in');
var dashboardContent = document.getElementById('dashboard-content');

Object(__WEBPACK_IMPORTED_MODULE_1__view_dashboard__["a" /* default */])(dashboardContent);

if (signInButton) {
    signInButton.onclick = function () {
        var data = {
            name: document.querySelector('input[name="name"]').value,
            password: document.querySelector('input[name="password"]').value
        };

        fetch('/api/login', {
            method: 'POST',
            body: JSON.stringify(data),
            headers: new Headers({ 'Content-Type': 'application/json' })
        }).then(function (response) {
            return response.json();
        }).then(function (json) {
            sessionStorage.setItem('gametoken', json.token);
            window.location.href = '/dashboard';
        });
    };
}

if (authorityElement && nonAuthorityElement) {
    var validator = Object(__WEBPACK_IMPORTED_MODULE_0__validation_emailsMatch__["a" /* default */])();

    var authorityValidate = function authorityValidate(e) {
        if (nonAuthorityElement.value !== '') {
            nonAuthorityElement.onkeyup();
        }
        console.log('AUTHORITY VALID', validator.setAuthority(e.target.value));
        return validator.setAuthority(e.target.value);
    };

    authorityElement.onkeyup = authorityValidate;
    authorityElement.onchange = authorityValidate;

    nonAuthorityElement.onkeyup = function (e) {
        console.log('NON-AUTHORITY VALID', validator.validate(nonAuthorityElement.value));
        if (validator.validate(nonAuthorityElement.value)) {
            nonAuthorityElement.parentElement.classList.remove('is-invalid');
            return;
        }

        nonAuthorityElement.parentElement.classList.add('is-invalid');
    };

    document.querySelector('#chess-content button').onclick = function () {
        var formData = {};

        var inputs = [].slice.call(document.querySelectorAll('#chess-content .login input')).forEach(function (item) {
            formData[item.getAttribute('name')] = item.value;
        });

        fetch('/register', {
            method: 'POST',
            body: JSON.stringify(formData),
            headers: new Headers({
                'Content-Type': 'application/json'
            })
        }).then(function (response) {
            if (response.redirected) {
                window.location.href = response.url;
                return;
            }

            return response.json();
        }).then(function (json) {
            /**
             * {
             *      name: [],
             *      email: [],
             *      password: [],
             * }
             */

            Object.keys(json).forEach(function (key) {
                var element = document.querySelector('input[name="' + key + '"]');

                if (element) {
                    element.parentElement.classList.add('is-invalid');
                    var error = document.createElement('span');
                    error.classList.add('mdl-textfield__error');
                    error.innerHTML = json[key].join('<br />');
                    element.parentElement.appendChild(error);
                }
            });
            console.log(json);
        });
    };
}

/***/ }),
/* 2 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (immutable) */ __webpack_exports__["a"] = emailsMatch;
function emailsMatch() {
    var authority = '';
    var nonauthority = '';

    var compare = function compare() {
        if (nonauthority !== '') {
            return authority === nonauthority;
        }
        return false;
    };

    var email = function email(value) {
        var r = /(.*)@(.*)/.exec(value);
        if (r === null) {
            return false;
        }

        return true;
    };

    return {
        setAuthority: function setAuthority(value) {
            authority = value;
        },

        validate: function validate(value) {
            nonauthority = value;
            if (email(nonauthority)) {
                return compare();
            }
            return false;
        }
    };
}

/***/ }),
/* 3 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (immutable) */ __webpack_exports__["a"] = dashboardView;
/* global fetch */

function newGame(token, title) {
  fetch('/api/games', {
    method: 'POST',
    body: JSON.stringify({
      title: title,
      token: token
    })
  }).then(function (response) {
    var idSub = response.headers.location.substring(response.headers.location.lastIndexOf('/'));

    var locationParts = response.headers.location.split('/');
    var id = locationParts[locationParts.length - 1];
    window.location.href = '/chessboard/' + id;
  });
}

function dashboardView(element) {
  if (!element) {
    return;
  }

  // List games.
  var token = sessionStorage.getItem('gametoken');

  fetch('/api/games?api_token=' + token, {
    method: 'GET'
  }).then(function (response) {
    return response.json();
  }).then(function (json) {
    element.querySelector('#new-game-button').addEventListener('click', function () {
      return newGame(token, 'Generic Title ' + json.length);
    });

    json.forEach(function (game) {
      var gv = document.createElement('div');
      gv.classList.add('mdl-cell');
      gv.classList.add('mdl-cell--3-col');
      gv.innerHTML = '\n                <div class="demo-card-event mdl-card mdl-shadow--2dp">\n                  <div class="mdl-card__title mdl-card--expand">\n                    <h4>\n                      ' + game.title + '<br>\n                      ' + game.updated_at + '<br>\n                    </h4>\n                  </div>\n                  <div class="mdl-card__actions mdl-card--border">\n                    <a class="mdl-button mdl-button--colored mdl-js-button mdl-js-ripple-effect"\n                      href="/chessboard/' + game.id + '">\n                      Play\n                    </a>\n                    <div class="mdl-layout-spacer"></div>\n                    <i class="material-icons">event</i>\n                  </div>\n                </div>\n            ';
      element.appendChild(gv);
    });
  });
}

/***/ }),
/* 4 */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ })
/******/ ]);