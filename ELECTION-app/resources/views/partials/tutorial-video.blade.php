<div class="tutorial-launcher">
    <button class="tutorial-button" type="button" data-tutorial-open aria-haspopup="dialog" aria-controls="tutorial-dialog">▶ Watch {{ $audience }} tutorial</button>
</div>
<dialog id="tutorial-dialog" class="tutorial-dialog" aria-labelledby="tutorial-title">
    <div class="tutorial-heading">
        <h2 id="tutorial-title">{{ ucfirst($audience) }} video guide</h2>
        <button class="tutorial-button" type="button" data-tutorial-close aria-label="Close tutorial">Close ×</button>
    </div>
    <p>Recorded demonstration with sample data and step-by-step text. No narration.</p>
    <video controls playsinline preload="none" data-tutorial-video data-src="{{ asset('videos/'.$audience.'-tutorial.mp4') }}">
        Your browser does not support video playback.
    </video>
    <p class="tutorial-note">The recorded results include a flagged duplicate-count issue; they are demonstration data, not live election results.</p>
    <p><a href="{{ asset('videos/'.$audience.'-tutorial.mp4') }}" target="_blank" rel="noopener">Open video directly</a></p>
</dialog>
<link rel="stylesheet" href="{{ asset('css/tutorial-video.css') }}?v={{ filemtime(public_path('css/tutorial-video.css')) }}">
<script src="{{ asset('js/tutorial-video.js') }}?v={{ filemtime(public_path('js/tutorial-video.js')) }}" defer></script>
