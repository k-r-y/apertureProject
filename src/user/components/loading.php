<!-- Initial Page Loader -->
<div id="initial-loader" class="luxury-loader-overlay active" style="position: fixed; z-index: 9999; background: #0f0f0f;">
    <div class="luxury-loader">
        <div class="spinner"></div>
        <div class="loader-text">Loading Aperture...</div>
    </div>
</div>

<script>
    // Auto-hide loader when page is fully loaded
    window.addEventListener('load', function() {
        const loader = document.getElementById('initial-loader');
        if(loader) {
            loader.style.opacity = '0';
            setTimeout(() => {
                loader.remove();
            }, 500);
        }
    });
</script>
