document.addEventListener('DOMContentLoaded', () => {
    const audioPlayer = document.getElementById('audio-player');
    const trackTitle = document.getElementById('track-title');
    const playerMessage = document.getElementById('player-message');
    const trackItems = document.querySelectorAll('.track-item');

    if (!audioPlayer || !trackTitle || !playerMessage) {
        return;
    }

    trackItems.forEach((track) => {
        track.addEventListener('click', async () => {
            const audioUrl = track.dataset.audio;
            const title = track.dataset.title;
            const artist = track.dataset.artist;

            if (!audioUrl) {
                playerMessage.textContent =
                    'La source audio de ce morceau est indisponible.';
                return;
            }

            trackItems.forEach((item) => {
                item.classList.remove('is-playing');
            });

            track.classList.add('is-playing');
            trackTitle.textContent = `${title} — ${artist}`;
            playerMessage.textContent = 'Chargement du morceau…';

            audioPlayer.src = audioUrl;
            audioPlayer.load();

            try {
                await audioPlayer.play();
                playerMessage.textContent = 'Lecture en cours.';
            } catch (error) {
                playerMessage.textContent =
                    'Lecture impossible. Vérifie ta connexion ou essaie un autre morceau.';
            }
        });
    });

    audioPlayer.addEventListener('ended', () => {
        const currentTrack = document.querySelector('.track-item.is-playing');
        const nextTrack = currentTrack?.nextElementSibling;

        if (nextTrack && nextTrack.classList.contains('track-item')) {
            nextTrack.click();
        } else {
            playerMessage.textContent = 'Fin de la playlist.';
            trackItems.forEach((item) => {
                item.classList.remove('is-playing');
            });
        }
    });

    audioPlayer.addEventListener('error', () => {
        if (audioPlayer.src) {
            playerMessage.textContent =
                'Impossible de charger ce fichier audio.';
        }
    });
});