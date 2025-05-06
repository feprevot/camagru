<h2>Publish</h2>

<div id="editor" style="display: flex; flex-wrap: wrap; gap: 2rem; align-items: flex-start;">
    <div>
        <div id="camera-wrapper" style="position: relative; width: 640px; height: 480px;">
            <video id="preview" autoplay playsinline style="width: 100%; height: 100%; background: #000;"></video>
            <img id="overlay-preview"
                 style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none; z-index: 1;" />
        </div>

        <canvas id="canvas" style="display: none;"></canvas>

        <div style="margin-top: 1rem;">
            <label for="overlay-select">Overlay :</label>
            <select id="overlay-select">
                <option value="">-- Get an overlay --</option>
                <option value="none">None</option>
                <option value="/images/first.png">Fairy Dream</option>
                <option value="/images/Bunny.png">Bunny Ears</option>
            </select>

            <button id="capture-btn" disabled>Catch it</button>
            <form id="upload-form" enctype="multipart/form-data" method="POST" action="/upload">
                <label for="file">Upload :</label>
                <input type="file" name="file" accept="image/*" required>
                <input type="submit" value="Envoyer">
            </form>
        </div>
    </div>

    <aside>
        <h3>Your post</h3>
        <div class="thumbnails" style="display: flex; flex-direction: column; gap: 0.5rem;">
            <?php foreach ($images as $img): ?>
                <div style="position: relative;">
                    <img src="/uploads/<?= htmlspecialchars($img['filename']) ?>" width="120">
                    <form method="POST" action="/delete" onsubmit="return confirm('Delete?');">
                        <input type="hidden" name="filename" value="<?= htmlspecialchars($img['filename']) ?>">
                        <button type="submit">🗑️</button>

                    </form>
                    <a href="https://twitter.com/intent/tweet?text=Check%20out%20my%20photo%20on%20Camagru!&url=<?= urlencode('https://localhost:8443/uploads/' . $img['filename']) ?>" 
                            target="_blank" 
                            style="margin-left: 0.5rem;">🔗</a>
                </div>
            <?php endforeach; ?>

        </div>
    </aside>
</div>

<script src="/js/editor.js"></script>
