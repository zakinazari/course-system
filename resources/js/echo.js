import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
    wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
    enabledTransports: ['ws', 'wss'],
});

// فقط اگر کاربر لاگین باشد و meta وجود داشته باشد
const userMeta = document.querySelector('meta[name="user-id"]');

if (userMeta && window.Echo) {

    const user_id = userMeta.content;

    window.Echo
        .private(`App.Models.User.${user_id}`)
        .notification((notification) => {

            console.log('New notification:', notification);

            if (window.Livewire) {
                Livewire.dispatch('notificationReceived');
            }

            playNotificationSound();
        });

}

function playNotificationSound() {

    const AudioContextClass = window.AudioContext || window.webkitAudioContext;

    if (!AudioContextClass) {
        return;
    }

    const audioContext = new AudioContextClass();

    const now = audioContext.currentTime;


    // Master volume
    const masterGain = audioContext.createGain();

    masterGain.gain.setValueAtTime(0, now);
    masterGain.gain.linearRampToValueAtTime(
        0.25,
        now + 0.05
    );

    masterGain.gain.exponentialRampToValueAtTime(
        0.001,
        now + 1.2
    );


    masterGain.connect(audioContext.destination);



    // Slight echo effect
    const delay = audioContext.createDelay();

    delay.delayTime.value = 0.18;


    const delayGain = audioContext.createGain();

    delayGain.gain.value = 0.25;


    delay.connect(delayGain);
    delayGain.connect(masterGain);



    const createTone = (frequency, start, duration) => {

        const oscillator = audioContext.createOscillator();

        const gain = audioContext.createGain();


        oscillator.type = 'sine';
        oscillator.frequency.value = frequency;


        gain.gain.setValueAtTime(0, now + start);

        gain.gain.linearRampToValueAtTime(
            0.7,
            now + start + 0.03
        );

        gain.gain.exponentialRampToValueAtTime(
            0.001,
            now + start + duration
        );


        oscillator.connect(gain);

        gain.connect(masterGain);
        gain.connect(delay);


        oscillator.start(now + start);
        oscillator.stop(now + start + duration);

    };


    // Telegram / modern notification style
    createTone(880, 0, 0.45);
    createTone(1175, 0.12, 0.5);
    createTone(1568, 0.24, 0.65);


    setTimeout(() => {

        audioContext.close();

    }, 1500);

}