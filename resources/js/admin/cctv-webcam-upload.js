// Global storage untuk webcam streams dan recorders
window._webcamStreams = window._webcamStreams || {};
window._recorders = window._recorders || {};
window._segmentCounters = window._segmentCounters || {};

/**
 * Generate UUID v4
 */
function generateUUID() {
    return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
        const r = Math.random() * 16 | 0;
        const v = c === 'x' ? r : (r & 0x3 | 0x8);
        return v.toString(16);
    });
}

/**
 * Start recording dengan auto-upload setiap 10 detik
 */
function startRecording(stream, roomId) {
    const mimeType = MediaRecorder.isTypeSupported('video/webm;codecs=vp9')
        ? 'video/webm;codecs=vp9'
        : MediaRecorder.isTypeSupported('video/webm') ? 'video/webm' : 'video/mp4';
    
    const recorder = new MediaRecorder(stream, { 
        mimeType: mimeType,
        videoBitsPerSecond: 2500000 // 2.5 Mbps
    });
    
    const sessionId = generateUUID();
    let currentSegmentIndex = 0;
    const startedAt = new Date().toISOString();
    
    console.log(`[Webcam] Recording session started: ${sessionId}`);
    
    // Handler untuk setiap segment yang selesai (setiap 10 detik)
    recorder.ondataavailable = function(e) {
        if (e.data && e.data.size > 0) {
            console.log(`[Webcam] Segment ${currentSegmentIndex} ready, size: ${(e.data.size / 1024).toFixed(2)} KB`);
            uploadSegment(roomId, sessionId, [e.data], currentSegmentIndex, startedAt);
            currentSegmentIndex++;
        }
    };
    
    // Ketika stop
    recorder.onstop = function() {
        console.log(`[Webcam] Recording stopped for room ${roomId}, session ${sessionId}`);
        
        // Hide REC badge
        const recBadge = document.getElementById('rec-badge-' + roomId);
        if (recBadge) recBadge.style.display = 'none';
    };
    
    // Start recording dengan timeslice 10 detik
    recorder.start(10000);
    
    window._recorders[roomId] = {
        recorder: recorder,
        sessionId: sessionId,
        segmentIndex: currentSegmentIndex
    };
    
    // Show REC badge
    const recBadge = document.getElementById('rec-badge-' + roomId);
    if (recBadge) recBadge.style.display = 'flex';
    
    console.log(`[Webcam] Recording started for room ${roomId}`);
}

/**
 * Upload segment ke server
 */
function uploadSegment(roomId, sessionId, chunks, segmentIndex, startedAt) {
    // Validate chunks
    if (!chunks || chunks.length === 0) {
        console.error(`[Upload] No chunks to upload for segment ${segmentIndex}`);
        return;
    }
    
    // Check if chunks are valid Blobs
    const validChunks = chunks.filter(chunk => chunk instanceof Blob && chunk.size > 0);
    if (validChunks.length === 0) {
        console.error(`[Upload] No valid chunks for segment ${segmentIndex}`);
        return;
    }
    
    const blob = new Blob(validChunks, { type: 'video/webm' });
    
    if (blob.size === 0) {
        console.error(`[Upload] Blob size is 0 for segment ${segmentIndex}`);
        return;
    }
    
    const formData = new FormData();
    
    formData.append('room_id', roomId);
    formData.append('recording_session_id', sessionId);
    formData.append('segment', blob, `segment_${segmentIndex}.webm`);
    formData.append('segment_index', segmentIndex);
    formData.append('started_at', startedAt);
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (csrfToken) {
        formData.append('_token', csrfToken.content);
        console.log(`[Upload] CSRF token found`);
    } else {
        console.error(`[Upload] CSRF token NOT found!`);
    }
    
    console.log(`[Upload] Uploading segment ${segmentIndex} for session ${sessionId}, size: ${(blob.size / 1024).toFixed(2)} KB`);
    
    fetch('/admin/cctv/upload-segment', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': csrfToken ? csrfToken.content : '',
            'Accept': 'application/json'
        }
    })
    .then(r => {
        console.log(`[Upload] Response status: ${r.status}`);
        if (!r.ok) {
            return r.text().then(text => {
                console.error(`[Upload] Server error response:`, text);
                throw new Error(`HTTP ${r.status}: ${text}`);
            });
        }
        return r.json();
    })
    .then(data => {
        if (data.success) {
            console.log(`[Upload] Segment ${segmentIndex} uploaded successfully`);
        } else {
            console.error(`[Upload] Failed to upload segment ${segmentIndex}:`, data.message || data);
        }
    })
    .catch(err => {
        console.error(`[Upload] Error uploading segment ${segmentIndex}:`, err);
    });
}

/**
 * Start webcam di card
 */
window.startCardWebcam = function(roomId) {
    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
        alert('Browser tidak mendukung akses kamera.');
        return;
    }
    
    // Kalau sudah ada stream, jangan buat baru
    if (window._webcamStreams[roomId]) {
        console.log(`[Webcam] Stream already exists for room ${roomId}`);
        return;
    }
    
    navigator.mediaDevices.getUserMedia({ 
        video: { 
            width: { ideal: 1280 },
            height: { ideal: 720 }
        }, 
        audio: true 
    })
    .then(function(stream) {
        window._webcamStreams[roomId] = stream;
        
        // Attach stream ke video element
        const card = document.getElementById('webcam-card-' + roomId);
        if (card) { 
            card.srcObject = stream; 
            card.style.display = 'block'; 
        }
        
        // Hide placeholder
        const ph = document.getElementById('webcam-card-placeholder-' + roomId);
        if (ph) { ph.style.display = 'none'; }
        
        // Show stop button
        const stopBtn = document.getElementById('stop-btn-' + roomId);
        if (stopBtn) { stopBtn.style.display = 'flex'; }
        
        // Start recording dengan auto-upload
        startRecording(stream, roomId);
        
        console.log(`[Webcam] Started successfully for room ${roomId}`);
    })
    .catch(function(err) {
        console.error('[Webcam] Error:', err);
        alert('Kamera tidak bisa diakses.\nError: ' + err.message);
    });
};

/**
 * Stop webcam di card
 */
window.stopCardWebcam = function(roomId) {
    console.log(`[Webcam] Stopping for room ${roomId}`);
    
    // Stop recorder first
    if (window._recorders[roomId]) {
        const recorderData = window._recorders[roomId];
        if (recorderData.recorder && recorderData.recorder.state !== 'inactive') {
            recorderData.recorder.stop();
        }
        delete window._recorders[roomId];
    }
    
    // Get video element and stop its stream
    const card = document.getElementById('webcam-card-' + roomId);
    if (card && card.srcObject) {
        const stream = card.srcObject;
        stream.getTracks().forEach(track => {
            track.stop();
            console.log(`[Webcam] Stopped track: ${track.kind}`);
        });
        card.srcObject = null;
    }
    
    // Stop stream from global storage
    if (window._webcamStreams[roomId]) {
        const stream = window._webcamStreams[roomId];
        stream.getTracks().forEach(track => {
            track.stop();
            console.log(`[Webcam] Stopped global track: ${track.kind}`);
        });
        delete window._webcamStreams[roomId];
    }
    
    // Reset UI
    if (card) { 
        card.style.display = 'none'; 
    }
    
    const ph = document.getElementById('webcam-card-placeholder-' + roomId);
    if (ph) { ph.style.display = 'flex'; }
    
    const recBadge = document.getElementById('rec-badge-' + roomId);
    if (recBadge) { recBadge.style.display = 'none'; }
    
    const stopBtn = document.getElementById('stop-btn-' + roomId);
    if (stopBtn) { stopBtn.style.display = 'none'; }
    
    console.log(`[Webcam] Stopped successfully for room ${roomId}`);
};

/**
 * Stop all webcams (cleanup saat page unload)
 */
window.addEventListener('beforeunload', function() {
    Object.keys(window._webcamStreams).forEach(roomId => {
        window.stopCardWebcam(roomId);
    });
});
