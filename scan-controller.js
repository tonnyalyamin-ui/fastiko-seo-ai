let total = 1000;
let processed = 0;

function updateBar() {
    let percent = Math.min(100, (processed / total) * 100);
    document.getElementById('fastiko-bar').style.width = percent + '%';
}

function startScan() {
    jQuery.post(ajaxurl, {
        action: 'fastiko_scan_start',
        _ajax_nonce: fastikoScan.nonce
    }, function() {
        document.getElementById('fastiko-status').innerText = 'Running...';
        runStep();
    });
}

function pauseScan() {
    jQuery.post(ajaxurl, {
        action: 'fastiko_scan_pause',
        _ajax_nonce: fastikoScan.nonce
    }, function() {
        document.getElementById('fastiko-status').innerText = 'Paused';
    });
}

function runStep() {
    jQuery.post(ajaxurl, {
        action: 'fastiko_scan_step',
        _ajax_nonce: fastikoScan.nonce
    }, function(res) {

        if (!res.success) return;

        processed += res.data.processed;

        updateBar();

        if (!res.data.done) {
            runStep();
        } else {
            document.getElementById('fastiko-status').innerText = 'Completed';
        }
    });
}