<div 
    x-data="{ show: false }" 
    x-show="show"
    x-on:loading.window="show = true"
    x-on:loading-complete.window="show = false"
    style="display: none"
    class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
>
    <div class="bg-white p-4 rounded-lg shadow-lg flex items-center space-x-3">
        <div class="animate-spin rounded-full h-8 w-8 border-4 border-blue-500 border-t-transparent"></div>
        <span class="text-gray-700 font-medium">Memproses...</span>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Show loading on form submissions
    document.addEventListener('submit', function() {
        window.dispatchEvent(new CustomEvent('loading'));
    });

    // Show loading on AJAX requests
    let originalXHR = window.XMLHttpRequest;
    function newXHR() {
        let xhr = new originalXHR();
        xhr.addEventListener('loadstart', function() {
            window.dispatchEvent(new CustomEvent('loading'));
        });
        xhr.addEventListener('loadend', function() {
            window.dispatchEvent(new CustomEvent('loading-complete'));
        });
        return xhr;
    }
    window.XMLHttpRequest = newXHR;

    // Show loading on fetch requests
    let originalFetch = window.fetch;
    window.fetch = function() {
        window.dispatchEvent(new CustomEvent('loading'));
        return originalFetch.apply(this, arguments)
            .finally(function() {
                window.dispatchEvent(new CustomEvent('loading-complete'));
            });
    };
});
</script> 