<script type="text/javascript">
    window.game_id = <?php echo $game_id; ?>;
    
    var bodyElement = document.querySelector('body');
    var styleElement = document.createElement('style');
    
    var req = new XMLHttpRequest();
    
    req.addEventListener('load', function() {
        styleElement.innerText = req.responseText;
        bodyElement.appendChild(styleElement);
    });
    
    req.open('GET', 'https://chess-game-zachkadish.c9users.io/chessgame.css');
    req.send();
</script>
<div id="chess-board"></div>
<script type="text/javascript" src="https://chess-game-zachkadish.c9users.io/chessgame.js"></script>
