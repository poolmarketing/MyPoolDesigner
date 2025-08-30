/**
 * MyPoolDesigner Gallery JavaScript
 * Version: 1.2.0
 */

(function($) {
    'use strict';

    // Initialize when document is ready
    $(document).ready(function() {
        initMPDGallery();
    });

    function initMPDGallery() {
        // Single image lightbox
        $('.mpd-single-image-item').on('click', function() {
            const imageUrl = $(this).data('image-url');
            const title = $(this).data('title');
            showSingleImage(imageUrl, title);
        });

        // Multi-image lightbox
        $('.mpd-multi-image-item').on('click', function() {
            const imagesString = $(this).data('images');
            const title = $(this).data('title');
            const images = imagesString.split(',');
            showImageGallery(images, title, 0);
        });

        // Video lightbox
        $('.mpd-video-item').on('click', function() {
            const videoUrl = $(this).data('video-url');
            const title = $(this).data('title');
            showVideo(videoUrl, title);
        });

        // Navigation controls
        $('#mpdPrevImage').on('click', function() {
            const currentIndex = parseInt($('#mpdLightbox').data('current-index') || 0);
            const images = $('#mpdLightbox').data('images') || [];
            if (currentIndex > 0) {
                showImageGallery(images, $('#mpdLightboxLabel').text(), currentIndex - 1);
            }
        });

        $('#mpdNextImage').on('click', function() {
            const currentIndex = parseInt($('#mpdLightbox').data('current-index') || 0);
            const images = $('#mpdLightbox').data('images') || [];
            if (currentIndex < images.length - 1) {
                showImageGallery(images, $('#mpdLightboxLabel').text(), currentIndex + 1);
            }
        });

        // Keyboard navigation
        $(document).on('keydown', function(e) {
            if ($('#mpdLightbox').hasClass('show')) {
                if (e.key === 'ArrowLeft') {
                    $('#mpdPrevImage').click();
                } else if (e.key === 'ArrowRight') {
                    $('#mpdNextImage').click();
                } else if (e.key === 'Escape') {
                    $('#mpdLightbox').modal('hide');
                }
            }
        });
    }

    function showSingleImage(imageUrl, title) {
        $('#mpdLightboxLabel').text(title);
        $('#mpdLightboxContent').html(`<img src="${imageUrl}" alt="${title}" class="img-fluid">`);
        $('#mpdNavigationControls').hide();
        $('#mpdLightbox').modal('show');
    }

    function showImageGallery(images, title, startIndex = 0) {
        const currentIndex = Math.max(0, Math.min(startIndex, images.length - 1));
        const currentImage = images[currentIndex];
        
        $('#mpdLightboxLabel').text(title);
        $('#mpdLightboxContent').html(`<img src="${currentImage}" alt="${title}" class="img-fluid">`);
        
        // Update navigation
        $('#mpdImageCounter').text(`${currentIndex + 1} of ${images.length}`);
        $('#mpdPrevImage').prop('disabled', currentIndex === 0);
        $('#mpdNextImage').prop('disabled', currentIndex === images.length - 1);
        $('#mpdNavigationControls').show();
        
        // Store data for navigation
        $('#mpdLightbox').data('images', images);
        $('#mpdLightbox').data('current-index', currentIndex);
        
        $('#mpdLightbox').modal('show');
    }

    function showVideo(videoUrl, title) {
        $('#mpdLightboxLabel').text(title);
        
        // Check if it's a direct video file or a presentation
        if (videoUrl.includes('.mp4') || videoUrl.includes('.webm') || videoUrl.includes('.mov')) {
            $('#mpdLightboxContent').html(`
                <video controls class="img-fluid">
                    <source src="${videoUrl}" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
            `);
        } else {
            // For presentations or other video formats, use iframe
            $('#mpdLightboxContent').html(`
                <iframe src="${videoUrl}" width="100%" height="400" frameborder="0" allowfullscreen></iframe>
            `);
        }
        
        $('#mpdNavigationControls').hide();
        $('#mpdLightbox').modal('show');
    }

    // Utility function for loading states
    function showLoading(message = 'Loading...') {
        return `<div class="mpd-loading">${message}</div>`;
    }

    // Export functions for external use if needed
    window.MPDGallery = {
        showSingleImage: showSingleImage,
        showImageGallery: showImageGallery,
        showVideo: showVideo,
        showLoading: showLoading
    };

})(jQuery);