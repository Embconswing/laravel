<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Button Sound</title>
</head>
<body>

    <button id="playSoundBtn">Click Me</button>

    <!-- Audio element -->
    <audio id="clickSound">
        <source src="{{ asset('sounds/callnext.mp3') }}" type="audio/mpeg">
        Your browser does not support the audio element.
    </audio>

    <script>
        document.getElementById('playSoundBtn').addEventListener('click', function () {
            const sound = document.getElementById('clickSound');
            sound.currentTime = 0; // rewind to start
            sound.play();
        });
    </script>

</body>
</html>
