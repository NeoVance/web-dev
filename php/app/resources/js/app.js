import emailsMatch from './validation/emailsMatch';
import dashboardView from './view/dashboard';

/* global fetch */

const authorityElement = document.getElementById('email');
const nonAuthorityElement = document.getElementById('email-verify');
const signInButton = document.getElementById('sign-in');
const dashboardContent = document.getElementById('dashboard-content');

dashboardView(dashboardContent);

if (signInButton)
{
    signInButton.onclick = function() {
        const data = {
            name: document.querySelector('input[name="name"]').value,
            password: document.querySelector('input[name="password"]').value,
        };
        
        fetch('/api/login', {
            method: 'POST',
            body: JSON.stringify(data),
            headers: new Headers({'Content-Type': 'application/json'})
        }).then(function (response) {
            return response.json();
        }).then(function (json) {
            sessionStorage.setItem('gametoken', json.token);
            window.location.href = '/dashboard';
        });
    }
}

if (authorityElement && nonAuthorityElement)
{
    const validator = emailsMatch();
    
    const authorityValidate = function (e) {
        if (nonAuthorityElement.value !== '') {
            nonAuthorityElement.onkeyup();
        }
        console.log('AUTHORITY VALID', validator.setAuthority(e.target.value));
        return validator.setAuthority(e.target.value);
    }
    
    authorityElement.onkeyup = authorityValidate;
    authorityElement.onchange = authorityValidate;
    
    nonAuthorityElement.onkeyup = function(e) {
        console.log('NON-AUTHORITY VALID', validator.validate(nonAuthorityElement.value))
        if (validator.validate(nonAuthorityElement.value)) {
            nonAuthorityElement.parentElement.classList.remove('is-invalid');
            return;
        }
        
        nonAuthorityElement.parentElement.classList.add('is-invalid');
    }
    
    document.querySelector('#chess-content button').onclick = function() {
        const formData = {};
        
        const inputs = [].slice.call(
            document.querySelectorAll('#chess-content .login input')
        ).forEach(function(item) {
            formData[item.getAttribute('name')] = item.value;
        });
        
        fetch('/register', {
            method: 'POST',
            body: JSON.stringify(formData),
            headers: new Headers({
                'Content-Type': 'application/json'
            }),
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
            
            Object.keys(json).forEach(function(key) {
                const element = document.querySelector(`input[name="${key}"]`);
                
                if (element) {
                    element.parentElement.classList.add('is-invalid');
                    const error = document.createElement('span');
                    error.classList.add('mdl-textfield__error');
                    error.innerHTML = json[key].join('<br />');
                    element.parentElement.appendChild(error);
                }
            });
            console.log(json);
        });
    }
}
