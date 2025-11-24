<h2><?php echo $title; ?></h2>

<?php echo validation_errors(); ?>

<?php echo form_open('visitors/create'); ?>
    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label for="name" class="form-label">Visitor Name</label>
                <input type="text" class="form-control" name="name" id="name" required>
            </div>

            <div class="mb-3">
                <label for="reason" class="form-label">Reason for Visit</label>
                <textarea class="form-control" name="reason" id="reason" rows="3" required></textarea>
            </div>

            <div class="mb-3">
                <label for="location_id" class="form-label">Entry Location</label>
                <select class="form-control" name="location_id" id="location_id" required>
                    <option value="">Select Location</option>
                    <?php $first = true; foreach ($locations as $location): ?>
                        <?php
                            // If form was submitted and has a value, respect it; otherwise select the first location by default
                            $posted = set_value('location_id');
                            $selected = ($posted !== '') ? ($posted == $location->id) : $first;
                        ?>
                        <option value="<?php echo $location->id; ?>" <?php echo $selected ? 'selected' : ''; ?>><?php echo htmlspecialchars($location->location_name); ?></option>
                        <?php $first = false; endforeach; ?>
                </select>
            </div>
        </div>

        <div class="col-12">
            <!-- Visitor Photo field (styled like form input) -->
            <div class="mb-3">
                <label for="faceUpload" class="form-label">Visitor Photo <small class="text-muted">(JPG/JPEG, max 2 MB)</small></label>
                <div class="d-flex align-items-start" style="max-width: 500px;">
                    <div class="input-group">
                        <input type="text" id="faceFilename" class="form-control" placeholder="No file chosen" readonly>
                        <label class="input-group-text btn btn-outline-secondary mb-0" for="faceUpload"><i class="fas fa-upload"></i></label>
                        <input type="file" id="faceUpload" accept="image/jpeg,image/jpg" style="display:none;">
                    </div>
                    <button type="button" class="btn btn-outline-primary ms-2" id="openFaceCameraBtn"><i class="fas fa-camera"></i></button>
                </div>
                <input type="hidden" name="face_photo" id="face_photo">
                <div class="mt-2"><img id="facePreview" class="img-thumbnail" style="max-height:180px; display:none;" alt="Face preview"></div>
                <div id="faceInfo" class="small text-muted mt-1"></div>
                <div id="faceError" class="text-danger small mt-1"></div>
            </div>

            <!-- ID Photo field -->
            <div class="mb-3">
                <label for="idUpload" class="form-label">ID Photo <small class="text-muted">(JPG/JPEG, max 2 MB)</small></label>
                <div class="d-flex align-items-start" style="max-width: 500px;">
                    <div class="input-group">
                        <input type="text" id="idFilename" class="form-control" placeholder="No file chosen" readonly>
                        <label class="input-group-text btn btn-outline-secondary mb-0" for="idUpload"><i class="fas fa-upload"></i></label>
                        <input type="file" id="idUpload" accept="image/jpeg,image/jpg" style="display:none;">
                    </div>
                    <button type="button" class="btn btn-outline-primary ms-2" id="openIdCameraBtn"><i class="fas fa-camera"></i></button>
                </div>
                <input type="hidden" name="id_photo" id="id_photo">
                <div class="mt-2"><img id="idPreview" class="img-thumbnail" style="max-height:180px; display:none;" alt="ID preview"></div>
                <div id="idInfo" class="small text-muted mt-1"></div>
                <div id="idError" class="text-danger small mt-1"></div>
            </div>
        </div>

        <div class="mb-3 mt-3">
            <button type="submit" class="btn btn-success">Save Entry</button>
            <a href="<?php echo site_url('visitors'); ?>" class="btn btn-secondary">Cancel</a>
        </div>
    </form>

    <!-- Camera Modals -->
    <div class="modal fade" id="faceCameraModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Capture Visitor Photo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <video id="faceModalVideo" autoplay playsinline class="img-fluid" style="max-height:60vh;"></video>
                    <canvas id="faceModalCanvas" class="d-none"></canvas>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="captureFaceModalBtn">Capture</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="idCameraModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Capture ID Photo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <video id="idModalVideo" autoplay playsinline class="img-fluid" style="max-height:60vh;"></video>
                    <canvas id="idModalCanvas" class="d-none"></canvas>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="captureIdModalBtn">Capture</button>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function(){
        const MAX_BYTES = 2 * 1024 * 1024; // 2MB

        function supportsCamera() {
            return !!(navigator.mediaDevices && navigator.mediaDevices.getUserMedia);
        }

        // Common helpers
        function handleFileInput(file, hiddenInput, previewImg, infoEl, errorEl, filenameEl) {
            errorEl.textContent = '';
            if (!file) return;
            if (file.size > MAX_BYTES) {
                errorEl.textContent = 'File too large (max 2 MB).';
                return;
            }
            if (!/image\/jpe?g/.test(file.type)) {
                errorEl.textContent = 'Invalid file type. Only JPG/JPEG allowed.';
                return;
            }
            const reader = new FileReader();
            reader.onload = function(e) {
                hiddenInput.value = e.target.result;
                previewImg.src = e.target.result;
                previewImg.style.display = 'block';
                infoEl.textContent = `${file.name} — ${(file.size/1024|0)} KB`;
                if (filenameEl) filenameEl.value = file.name;
            };
            reader.readAsDataURL(file);
        }

        // Face elements
        const faceUpload = document.getElementById('faceUpload');
        const faceHidden = document.getElementById('face_photo');
        const facePreview = document.getElementById('facePreview');
        const faceInfo = document.getElementById('faceInfo');
        const faceError = document.getElementById('faceError');
        const faceFilename = document.getElementById('faceFilename');

        // ID elements
        const idUpload = document.getElementById('idUpload');
        const idHidden = document.getElementById('id_photo');
        const idPreview = document.getElementById('idPreview');
        const idInfo = document.getElementById('idInfo');
        const idError = document.getElementById('idError');
        const idFilename = document.getElementById('idFilename');

        // File change handlers
        faceUpload.addEventListener('change', function(){ handleFileInput(this.files[0], faceHidden, facePreview, faceInfo, faceError, faceFilename); });
        idUpload.addEventListener('change', function(){ handleFileInput(this.files[0], idHidden, idPreview, idInfo, idError, idFilename); });

        // Camera handling for modal
        let faceStream = null;
        let idStream = null;

        const faceModalEl = document.getElementById('faceCameraModal');
        const faceModal = new bootstrap.Modal(faceModalEl);
        const faceVideo = document.getElementById('faceModalVideo');
        const faceCanvas = document.getElementById('faceModalCanvas');
        const captureFaceModalBtn = document.getElementById('captureFaceModalBtn');

        const idModalEl = document.getElementById('idCameraModal');
        const idModal = new bootstrap.Modal(idModalEl);
        const idVideo = document.getElementById('idModalVideo');
        const idCanvas = document.getElementById('idModalCanvas');
        const captureIdModalBtn = document.getElementById('captureIdModalBtn');

        async function startStreamFor(videoEl) {
            try {
                const stream = await navigator.mediaDevices.getUserMedia({ video: true });
                videoEl.srcObject = stream;
                await videoEl.play();
                return stream;
            } catch (err) {
                console.error(err);
                return null;
            }
        }

        faceModalEl.addEventListener('shown.bs.modal', async function(){
            if (!supportsCamera()) { faceError.textContent = 'Camera not available.'; return; }
            faceStream = await startStreamFor(faceVideo);
            if (!faceStream) faceError.textContent = 'Could not access camera.';
        });
        faceModalEl.addEventListener('hidden.bs.modal', function(){
            if (faceStream) { faceStream.getTracks().forEach(t=>t.stop()); faceStream = null; }
            faceVideo.pause(); faceVideo.srcObject = null;
        });

        idModalEl.addEventListener('shown.bs.modal', async function(){
            if (!supportsCamera()) { idError.textContent = 'Camera not available.'; return; }
            idStream = await startStreamFor(idVideo);
            if (!idStream) idError.textContent = 'Could not access camera.';
        });
        idModalEl.addEventListener('hidden.bs.modal', function(){
            if (idStream) { idStream.getTracks().forEach(t=>t.stop()); idStream = null; }
            idVideo.pause(); idVideo.srcObject = null;
        });

        document.getElementById('openFaceCameraBtn').addEventListener('click', function(){ faceError.textContent = ''; faceModal.show(); });
        document.getElementById('openIdCameraBtn').addEventListener('click', function(){ idError.textContent = ''; idModal.show(); });

        // Capture from modal into hidden input and preview
        captureFaceModalBtn.addEventListener('click', function(){
            try {
                faceCanvas.width = faceVideo.videoWidth || 640;
                faceCanvas.height = faceVideo.videoHeight || 480;
                faceCanvas.getContext('2d').drawImage(faceVideo, 0, 0);
                const dataUrl = faceCanvas.toDataURL('image/jpeg');
                faceHidden.value = dataUrl;
                facePreview.src = dataUrl;
                facePreview.style.display = 'block';
                faceInfo.textContent = 'Captured from camera';
                faceFilename.value = 'camera.jpg';
                faceModal.hide();
            } catch (err) { console.error(err); faceError.textContent = 'Capture failed.'; }
        });

        captureIdModalBtn.addEventListener('click', function(){
            try {
                idCanvas.width = idVideo.videoWidth || 640;
                idCanvas.height = idVideo.videoHeight || 480;
                idCanvas.getContext('2d').drawImage(idVideo, 0, 0);
                const dataUrl = idCanvas.toDataURL('image/jpeg');
                idHidden.value = dataUrl;
                idPreview.src = dataUrl;
                idPreview.style.display = 'block';
                idInfo.textContent = 'Captured from camera';
                idFilename.value = 'camera.jpg';
                idModal.hide();
            } catch (err) { console.error(err); idError.textContent = 'Capture failed.'; }
        });

        // Simple initial messages
        if (!supportsCamera()) {
            faceInfo.textContent = 'Camera not available in this browser.';
            idInfo.textContent = 'Camera not available in this browser.';
            document.getElementById('openFaceCameraBtn').disabled = true;
            document.getElementById('openIdCameraBtn').disabled = true;
        }
    });
    </script>