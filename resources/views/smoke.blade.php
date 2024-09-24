<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smoke Removal with Hand Gesture</title>
    <script src="https://cdn.jsdelivr.net/npm/handtrackjs/dist/handtrack.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        #frame {
            width: 400px;
            height: 300px;
            background-image: url('2.jpg'); /* Replace with your frame image */
            background-size: cover;
            position: relative;
            margin: 0 auto;
        }

        #smoke-cover {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7); /* Smoke effect */
            transition: opacity 0.5s;
            opacity: 1; /* Fully covered with smoke */
        }

        #video {
            display: block;
            margin: 20px auto;
            width: 400px;
            height: 300px;
            border: 2px solid #000;
        }
    </style>
</head>
<body>

<h2 style="text-align: center;">Move your hand left or right to remove the smoke!</h2>

<!-- Frame containing smoke overlay -->
<div id="frame">
    <div id="smoke-cover"></div>
</div>

<!-- Webcam video feed -->
<video id="video" autoplay></video>

<script>
    const video = document.getElementById('video');
    const smokeCover = document.getElementById('smoke-cover');
    let lastHandX = null; // Track last hand position
    let currentOpacity = 1.0; // Starting opacity

    // Load the Handtrack.js model
    handTrack.load().then(model => {
        // Start video from webcam
        handTrack.startVideo(video).then(status => {
            if (status) {
                // Detect hand positions every 200ms
                setInterval(() => {
                    model.detect(video).then(predictions => {
                        if (predictions.length > 0) {
                            let handX = predictions[0].bbox[0]; // X position of the hand
                            let handMovementDirection = detectHandRotation(handX);

                            // Call Laravel API when hand moves left or right
                            if (handMovementDirection === 'left' || handMovementDirection === 'right') {
                                removeSmoke(handMovementDirection);
                            }
                        }
                    });
                }, 200);
            } else {
                alert("Please enable video");
            }
        });
    });

    // Function to detect hand movement (left or right)
    function detectHandRotation(currentX) {
        const movementThreshold = 20; // Adjust for sensitivity

        if (lastHandX === null) {
            lastHandX = currentX;
            return null;
        }

        let direction = null;
        if (currentX < lastHandX - movementThreshold) {
            direction = 'left';
        } else if (currentX > lastHandX + movementThreshold) {
            direction = 'right';
        }

        lastHandX = currentX; // Update last hand position
        return direction;
    }

    // Function to send an AJAX request to Laravel to remove the smoke
    function removeSmoke(direction) {
        fetch('/remove-smoke', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ direction: direction })
        }).then(response => response.json()).then(data => {
            if (data.success && currentOpacity > 0) {
                // Gradually reduce the opacity of the smoke cover
                currentOpacity -= 0.1;
                smokeCover.style.opacity = currentOpacity;
            }
        });
    }
</script>

</body>
</html>
