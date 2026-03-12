<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Now Serving</title>

<style>
body {
    background:black;
    color:white;
    text-align:center;
    font-family:Arial, Helvetica, sans-serif;
}

h1 {
    font-size:64px;
    margin-top:30px;
}

button {
    margin-top:40px;
    font-size:20px;
    padding:10px 20px;
}

#debug {
    position:fixed;
    top:0;
    left:0;
    width:100%;
    background:#111;
    color:#0f0;
    font-size:16px;
    padding:6px;
    z-index:9999;
}

#callList {
    margin-top:30px;
}

.call-row {
    font-size:52px;
    font-weight:bold;
    margin:12px 0;
}
</style>
</head>

<body>

<div id="debug">DEBUG: page loaded</div>

<h1>Now Serving</h1>

<div id="callList"></div>

<audio id="callNextSound" preload="auto">
    <source src="/sounds/callnext.mp3" type="audio/mpeg">
</audio>

<button onclick="enableAudio()">Start Queue System</button>

<!-- LOAD PUSHER + ECHO FROM CDN -->
<script src="https://js.pusher.com/7.2/pusher.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.15.0/dist/echo.iife.js"></script>

<script>
/* ===================== CONFIG ===================== */
const MAX_ROWS = 4;
const DISPLAY_TIME = 15000; // 15 seconds

/* ===================== DEBUG ====================== */
function log(msg) {
    document.getElementById('debug').innerText = 'DEBUG: ' + msg;
}

/* ===================== AUDIO ====================== */
let audioUnlocked = false;

function enableAudio() {
    const audio = document.getElementById('callNextSound');
    audio.play().then(() => {
        audio.pause();
        audio.currentTime = 0;
        audioUnlocked = true;
        log('Audio unlocked ✅');
    }).catch(() => {
        log('Audio unlock FAILED ❌');
    });
}

/* ===================== QUEUE UI =================== */
function addCall(reference, windowNo) {
    const list = document.getElementById('callList');

    const row = document.createElement('div');
    row.className = 'call-row';
    row.textContent = `${reference} → Counter ${windowNo}`;

    // Add newest on top
    list.prepend(row);

    // Keep only MAX_ROWS
    while (list.children.length > MAX_ROWS) {
        list.removeChild(list.lastChild);
    }

    // Auto-remove after DISPLAY_TIME
    setTimeout(() => {
        if (row.parentNode) {
            row.remove();
        }
    }, DISPLAY_TIME);
}

/* ===================== ECHO ======================= */
window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: "{{ env('PUSHER_APP_KEY') }}",
    cluster: "{{ env('PUSHER_APP_CLUSTER') }}",
    forceTLS: true,
});

log('Echo loaded ✅');

/* Connection status */
Echo.connector.pusher.connection.bind('connected', () => {
    log('Pusher connected ✅');
});

/* Listen for appointment calls */
Echo.channel('queue')
    .listen('.AppointmentCalled', (e) => {
        log('EVENT RECEIVED 🔔');

        if (!audioUnlocked) {
            log('Event received but audio locked ⚠️');
            return;
        }

        addCall(e.reference, e.window);

        const audio = document.getElementById('callNextSound');
        audio.currentTime = 0;
        audio.play().then(() => {
            log('Sound played 🔊');
        }).catch(() => {
            log('Sound FAILED ❌');
        });
    });

log('Waiting for calls…');
</script>

</body>
</html>
