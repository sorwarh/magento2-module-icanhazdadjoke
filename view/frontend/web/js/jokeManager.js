define([], function () {
    'use strict';

    return function (config) {
        const jokes = config.jokes;
        let currentJokeIndex = null;
        const jokeContainer = document.getElementById('joke-container');
        const randomJokeButton = document.getElementById('random-joke-button');

        function showRandomJoke() {
            // Generate a random index
            currentJokeIndex = Math.floor(Math.random() * jokes.length);

            // Get the joke and create HTML content
            const joke = jokes[currentJokeIndex];
            const jokeContent = `
                <div class="joke-content" data-joke-id='${joke.id}'>
                    <p class="joke-text">${joke.joke}</p>
                </div>
            `;

            jokeContainer.innerHTML = jokeContent;
        }
        showRandomJoke();
        randomJokeButton.addEventListener('click', showRandomJoke);
    };
});
