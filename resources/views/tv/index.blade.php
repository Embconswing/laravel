<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Appointments Display</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<style>
:root {
    --india-saffron:#ff9933;
    --india-green:#138808;
    --india-blue:#1d4ed8;
    --bg-main:#f8fafc;
    --panel-border:#e5e7eb;
    --bottom-panel-height:30vh;
}

body {
    margin:0;
    background:var(--bg-main);
    font-family:Arial,Helvetica,sans-serif;
    overflow:hidden;
}

.container {
    height:100vh;
    display:flex;
    flex-direction:column;
    padding:1vh 1.5vw;
}

/* ===== HEADER ===== */
.header {
    display:grid;
    grid-template-columns:auto 1fr auto;
    align-items:center;
    background:#eef2ff;
    padding:1vh 1.5vw;
    border-radius:6px;
    border-bottom:4px solid var(--india-saffron);
}

.clock { font-size:5.2vh; font-weight:900; color:var(--india-blue); }
.date  { font-size:2.4vh; }

.header h1 { margin:0; font-size:4.2vh; font-weight:900; }
.header h2 { margin:0; font-size:2.8vh; }
.header img { width:80px; }

/* ===== MAIN ===== */
.main {
    flex:1;
    margin-top:1vh;
    display:flex;
    gap:1vw;
}

/* ===== LEFT COLUMN ===== */
.left-column {
    flex:7;
    display:flex;
    flex-direction:column;
    gap:1vh;
}

/* ===== ACTIVE TABLE ===== */
.active-section {
    height:42vh;
    background:#fff;
    border-left:6px solid var(--india-green);
    border-radius:6px;
    padding:0.4vh 0.8vw;
    overflow:hidden;
}
.active-section tbody td:nth-child(2) {
    font-size: 5vh;
    font-weight: 900;
    line-height: 2.0;   /* 🔥 tighter lines */
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
     text-transform: uppercase;
    color: #111827;
}

.active-section table {
    width:100%;
    height:100%;
    border-collapse:collapse;
    table-layout:fixed;
}

.active-section colgroup col:nth-child(1) { width:35%; }
.active-section colgroup col:nth-child(2) { width:52%; }
.active-section colgroup col:nth-child(3) { width:10%; }
.active-section colgroup col:nth-child(4) { width:3%; }

thead th {
    background:#dbeafe;
    font-size:2.4vh;
    font-weight:900;
    padding:0.35vh 0.6vw;
}
.active-section tbody td:nth-child(1) {
    font-size: 3.8vh;        /* increased */
    font-weight: 900;
}




.active-section tbody tr {
    height: calc((42vh - 7vh) / 4);
}





tbody td {
    font-size:3.4vh;
    font-weight:900;
    padding:0.15vh 0.5vw;
    white-space:nowrap;
    overflow:hidden;
    vertical-align:middle;
}

.big-window {
    font-size:5vh;
    color:var(--india-blue);
    text-align:center;
    vertical-align:middle;
}

.status-dot {
    width:2vh;
    height:2vh;
    background:var(--india-green);
    border-radius:50%;
    display:inline-block;
}

/* ===== BOTTOM LEFT ===== */
.bottom-left-section {
    height:var(--bottom-panel-height);
    display:flex;
    gap:1vw;
}

.today-panel {
    flex:1.2;
    background:#fff;
    border-top:4px solid var(--india-saffron);
    border-radius:6px;
    padding:1vh;
    display:flex;
    flex-direction:column;
}

.today-panel h3 {
    font-size:3vh;
    margin:0 0 0.5vh;
}

#today-list {
    flex:1;
    overflow:hidden;
}

#today-list li {
    font-size:3.2vh;
    padding:0.8vh 0;
    border-bottom:1px solid var(--panel-border);
    height:5.2vh;
    box-sizing:border-box;
}

/* VIDEO */
.video-panel {
    flex:1;
    background:black;
    border-radius:6px;
    overflow:hidden;
}

.video-panel video {
    width:100%;
    height:100%;
    object-fit:contain;
}

/* ===== RIGHT COLUMN ===== */
.right-column {
    flex:3;
    background:#fff;
    border-top:4px solid var(--india-green);
    border-radius:6px;
    padding:1vh;
    display:flex;
    flex-direction:column;
}

.waiting-section h3 {
    color:#dc2626;
    font-size:3.4vh;
    margin:0 0 0.5vh;
}

#waiting-list li {
    display:flex;
    justify-content:space-between;
    font-size:3.2vh;
    padding:0.8vh 0;
    border-bottom:1px solid var(--panel-border);
}

.wait-dot {
    width:2.2vh;
    height:2.2vh;
    border-radius:50%;
    background:#facc15;
}
.wait-dot.red { background:#dc2626; }

.announcement-section {
    height:calc(var(--bottom-panel-height)/2);
    background:#fff7ed;
    border-top:4px solid var(--india-saffron);
    border-radius:6px;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:3.2vh;
    font-weight:900;
    text-align:center;
}

/* FOOTER */
.footer {
    font-size:2.2vh;
    text-align:right;
}
@keyframes blink {
    0%   { opacity: 1; }
    50%  { opacity: 0; }
    100% { opacity: 1; }
}

.wait-dot.blink {
    animation: blink 1s infinite;
    background: #facc15; /* yellow */
}
</style>
</head>

<body>
<div class="container">

<div class="header">
    <div>
        <div class="clock" id="clock"></div>
        <div class="date" id="date"></div>
    </div>
    <div style="text-align:center">
        <h1>{{ $registname }}</h1>
        <h2>Now Serving</h2>
    </div>
    <img src="/images/Emblem_of_India.svg">
</div>

<div class="main">
<div class="left-column">

<div class="active-section">
<table>
    <colgroup><col><col><col><col></colgroup>
    <thead>
        <tr>
            <th>Application No</th>
            <th>Name</th>
            <th>Counter</th>
            <th></th>
        </tr>
    </thead>
    <tbody id="active-body"></tbody>
</table>
</div>

<div class="bottom-left-section">
    <div class="today-panel">
        <h3>
            Today
            <span id="today-count" style="font-weight:700; color:#1d4ed8;"></span>
        </h3>
        <ul id="today-list"></ul>
    </div>
    <div class="video-panel">
        <video id="promoVideo" autoplay muted playsinline></video>
    </div>
</div>
</div>

<div class="right-column">
    <div class="waiting-section">
        <h3>Please wait to be called</h3>
        <ul id="waiting-list"></ul>
    </div>
    <div class="announcement-section" id="announcement-section">
        Please keep your documents ready
    </div>
</div>
</div>

<div class="footer">
    Updated: <span id="last-updated"></span>
</div>

<script>
const API_URL = '/api/tv/appointments';

const activeBody  = document.getElementById('active-body');
const todayList   = document.getElementById('today-list');
const waitingList = document.getElementById('waiting-list');
const lastUpdated = document.getElementById('last-updated');
const todayCountEl = document.getElementById('today-count');

/* CLOCK */
function updateClock(){
    const now=new Date();
    clock.textContent=now.toLocaleTimeString('en-GB',{hour:'2-digit',minute:'2-digit'});
    date.textContent=now.toLocaleDateString('en-GB',{weekday:'long',day:'2-digit',month:'long',year:'numeric'});
}
updateClock();
setInterval(updateClock,1000);

/* SMOOTH AUTO SCROLL */
function smoothAutoScroll(el, speed=60){
    let dir=1;
    setInterval(()=>{
        if(el.scrollHeight<=el.clientHeight) return;
        el.scrollTop+=dir;
        if(el.scrollTop+el.clientHeight>=el.scrollHeight) dir=-1;
        if(el.scrollTop<=0) dir=1;
    }, speed);
}

/* CORE LOGIC */
function renderAppointments(res){
    const data = res.data;
    const todayAppointments = res.today_appointments || [];

    todayCountEl.textContent = `(${res.today_appointments_count})`;

    activeBody.innerHTML='';
    todayList.innerHTML='';
    waitingList.innerHTML='';

    todayAppointments.forEach(ref=>{
        todayList.innerHTML += `<li>${ref}</li>`;
    });

    const now=new Date();
    const waitingItems=[];
    let activeCount = 0;

    data.forEach(a=>{
        if(a.window_no){
            activeBody.innerHTML+=`
            <tr>
                <td>${a.appointment_no}</td>
                <td>${a.name||''}</td>
                <td class="big-window">${a.window_no}</td>
                <td><span class="status-dot"></span></td>
            </tr>`;
            activeCount++;
            return;
        }

if (a.status !== 'waiting') return;

/* Parse times */
const start = new Date(`${a.appointment_date} ${a.start_time}`);
const end   = new Date(`${a.appointment_date} ${a.end_time}`);

/* Time windows */
const showFrom = new Date(start.getTime() - 5 * 60000);   // 5 min before
const hideAfter = new Date(end.getTime() + 30 * 60000);   // 30 min after

/* Outside display window → don't show */
if (now < showFrom || now > hideAfter) return;

/* Yellow blinking logic */
const shouldBlink = now >= showFrom && now < start;

/* Red dot logic (15 mins after end) */
const isRed = now > end && ((now - end) / 60000) > 15;

waitingItems.push({
    ref: a.appointment_no,
    blink: shouldBlink,
    red: isRed
});

    });

    for(let i=activeCount;i<4;i++){
        activeBody.innerHTML+=`
        <tr>
            <td>&nbsp;</td>
            <td></td>
            <td></td>
            <td></td>
        </tr>`;
    }

  waitingItems.slice(-6).forEach(w => {
    waitingList.innerHTML += `
    <li>
        <span>${w.ref}</span>
        <span class="wait-dot
            ${w.red ? 'red' : ''}
            ${w.blink ? 'blink' : ''}">
        </span>
    </li>`;
});


    lastUpdated.textContent=new Date().toLocaleTimeString('en-GB');
}

function fetchAppointments(){
    fetch(API_URL)
    .then(r=>r.json())
    .then(r=>r.success && renderAppointments(r))
    .catch(console.error);
}

fetchAppointments();
setInterval(fetchAppointments,10000);

smoothAutoScroll(todayList,40);

/* VIDEO */
const videos=["/videos/video1.mp4","/videos/video2.mp4"];
let i=0;
const video=document.getElementById("promoVideo");
video.src=videos[0];
video.onended=()=>{
    i=(i+1)%videos.length;
    video.src=videos[i];
    video.play();
};
/* ANNOUNCEMENTS */
const announcementEl = document.getElementById('announcement-section');
let announcements = [];
let current = 0;

fetch('/api/announcements', { cache: 'no-store' })
  .then(r => r.json())
  .then(d => {
      console.log('Announcements API response:', d);

      // ✅ Normalize API response
      if (Array.isArray(d)) {
          announcements = d.map(a => a.message);
      } else if (Array.isArray(d.messages)) {
          announcements = d.messages;
      } else if (Array.isArray(d.data)) {
          announcements = d.data.map(a => a.message);
      }

      if (!announcements.length) {
          announcementEl.textContent = 'No announcements available';
          return;
      }

      // ✅ Show first announcement immediately
      announcementEl.textContent = announcements[0];

      // ✅ Rotate announcements
      setInterval(() => {
          current = (current + 1) % announcements.length;
          announcementEl.textContent = announcements[current];
      }, 8000);
  })
  .catch(err => {
      console.error('Announcement fetch failed:', err);
      announcementEl.textContent = 'Unable to load announcements';
  });

</script>
</body>
</html>
