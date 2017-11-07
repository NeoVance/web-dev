/* global fetch */

function newGame(token, title) {
  fetch('/api/games', {
    method: 'POST',
    body: JSON.stringify(({
      title: title,
      token: token
    }))
  }).then((response) => {
    const idSub = response.headers.location.substring(
      response.headers.location.lastIndexOf('/')
    );
    
    const locationParts = response.headers.location.split('/');
    const id = locationParts[locationParts.length - 1];
    window.location.href = '/chessboard/' + id;
  });
}

export default function dashboardView(element) {
    if (!element) {
        return;
    }
    
    // List games.
    const token = sessionStorage.getItem('gametoken');
    
    fetch('/api/games?api_token='+token, {
        method: 'GET',
    }).then(function (response) {
        return response.json();
    }).then(function (json) {
        element.querySelector('#new-game-button')
          .addEventListener(
            'click',
            () => newGame(token, 'Generic Title ' + json.length)
          );
        
        json.forEach(function (game) {
            const gv = document.createElement('div');
            gv.classList.add('mdl-cell');
            gv.classList.add('mdl-cell--3-col');
            gv.innerHTML = `
                <div class="demo-card-event mdl-card mdl-shadow--2dp">
                  <div class="mdl-card__title mdl-card--expand">
                    <h4>
                      ${game.title}<br>
                      ${game.updated_at}<br>
                    </h4>
                  </div>
                  <div class="mdl-card__actions mdl-card--border">
                    <a class="mdl-button mdl-button--colored mdl-js-button mdl-js-ripple-effect"
                      href="/chessboard/${game.id}">
                      Play
                    </a>
                    <div class="mdl-layout-spacer"></div>
                    <i class="material-icons">event</i>
                  </div>
                </div>
            `;
            element.appendChild(gv);
        });
    });
}