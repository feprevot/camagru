<h2>Éditeur d’image</h2>

<div id="editor" style="display: flex; flex-wrap: wrap; gap: 2rem; align-items: flex-start;">
    <div>
        <div id="camera-wrapper" style="position: relative; width: 640px; height: 480px;">
            <video id="preview" autoplay playsinline style="width: 100%; height: 100%; background: #000;"></video>
            <img id="overlay-preview"
                 style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none; z-index: 1;" />
        </div>

        <canvas id="canvas" style="display: none;"></canvas>

        <div style="margin-top: 1rem;">
            <label for="overlay-select">Superposable :</label>
            <select id="overlay-select">
                <option value="">-- Choisir un overlay --</option>
                <option value="/images/cadre1.png">Cadre 1</option>
                <option value="/images/cat-laser.png">Chat laser</option>
            </select>

            <button id="capture-btn" disabled>Capturer</button>
            <form id="upload-form" enctype="multipart/form-data" method="POST" action="/upload">
                <label for="file">Ou téléversez une image :</label>
                <input type="file" name="file" accept="image/*" required>
                <input type="submit" value="Envoyer">
            </form>
        </div>
    </div>

    <aside>
        <h3>Vos images</h3>
        <div class="thumbnails" style="display: flex; flex-direction: column; gap: 0.5rem;">
            <?php foreach ($images as $img): ?>
                <div style="position: relative;">
                    <img src="/uploads/<?= htmlspecialchars($img['filename']) ?>" width="120">
                    <form method="POST" action="/delete" onsubmit="return confirm('Supprimer cette image ?');">
                        <input type="hidden" name="filename" value="<?= htmlspecialchars($img['filename']) ?>">
                        <button type="submit">🗑️</button>
                    </form>
                </div>
            <?php endforeach; ?>

        </div>
    </aside>
</div>

<script src="/js/editor.js"></script>
