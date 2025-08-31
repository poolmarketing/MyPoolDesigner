/**
 * MyPoolDesigner Gallery JavaScript (Repository Version)
 * Version: 1.2.0 - WordPress.org Compliant
 */

(function($) {
    'use strict';

    // Initialize when document is ready
    $(document).ready(function() {
        createLightboxModal();
        initMPDGallery();
    });

    function createLightboxModal() {
        // Create simple lightbox modal (replaces Bootstrap modal)
        if ($('#mpdLightbox').length === 0) {
            $('body').append(`
                <div id="mpdLightbox" class="mpd-lightbox" style="display: none;">
                    <div class="mpd-lightbox-content">
                        <span class="mpd-lightbox-close">&times;</span>
                        <div id="mpdLightboxContent"></div>
                        <div id="mpdNavigationControls" style="display: none;">
                            <button id="mpdPrevImage">Previous</button>
                            <span id="mpdImageCounter"></span>
                            <button id="mpdNextImage">Next</button>
                        </div>
                    </div>
                </div>
            `);

            // Close lightbox handlers
            $(document).on('click', '.mpd-lightbox-close', function() {
                hideLightbox();
            });

            $(document).on('click', '#mpdLightbox', function(e) {
                if (e.target === this) {
                    hideLightbox();
                }
            });
        }
    }

    function showLightbox() {
        $('#mpdLightbox').show();
        $('body').addClass('mpd-lightbox-open');
    }

    function hideLightbox() {
        $('#mpdLightbox').hide();
        $('body').removeClass('mpd-lightbox-open');
        // Pause and remove video to prevent memory leaks and double loads
        $('#mpdLightboxContent video').each(function() {
            this.pause();
            this.src = ''; // Clear the source
            this.load(); // Reset the video element
        });
        // Clear the content to fully remove video element
        $('#mpdLightboxContent').empty();
    }

    function initMPDGallery() {
        // Remove any existing handlers first to prevent duplicates
        $(document).off('click', 'a[data-lightbox="mpd-gallery"]');
        $(document).off('click', 'a.mpd-multi-item');
        $(document).off('click', '.mpd-video-item');
        
        // Single image lightbox (using data-lightbox attribute)
        $(document).on('click', 'a[data-lightbox="mpd-gallery"]', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const imageUrl = $(this).attr('href');
            const title = $(this).find('img').attr('alt') || 'Design';
            showSingleImage(imageUrl, title);
        });

        // Multi-image lightbox (using data-lightbox with gallery prefix)
        $(document).on('click', 'a.mpd-multi-item', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const imagesString = $(this).data('images');
            const title = $(this).find('img').attr('alt') || 'Design Collection';
            const images = imagesString.split(',');
            showImageGallery(images, title, 0);
        });

        // Video lightbox
        $(document).on('click', '.mpd-video-item', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const videoUrl = $(this).data('video-url');
            const title = $(this).data('title');
            showVideo(videoUrl, title);
        });

        // Navigation controls
        $(document).on('click', '#mpdPrevImage', function() {
            const currentIndex = parseInt($('#mpdLightbox').data('current-index') || 0);
            const images = $('#mpdLightbox').data('images') || [];
            if (currentIndex > 0) {
                showImageGallery(images, '', currentIndex - 1);
            }
        });

        $(document).on('click', '#mpdNextImage', function() {
            const currentIndex = parseInt($('#mpdLightbox').data('current-index') || 0);
            const images = $('#mpdLightbox').data('images') || [];
            if (currentIndex < images.length - 1) {
                showImageGallery(images, '', currentIndex + 1);
            }
        });

        // Keyboard navigation
        $(document).on('keydown', function(e) {
            if ($('#mpdLightbox').is(':visible')) {
                if (e.key === 'ArrowLeft') {
                    $('#mpdPrevImage').click();
                } else if (e.key === 'ArrowRight') {
                    $('#mpdNextImage').click();
                } else if (e.key === 'Escape') {
                    hideLightbox();
                }
            }
        });

        // Pagination handlers
        $(document).on('click', '.mpd-prev-page', function(e) {
            e.preventDefault();
            const $gallery = $(this).closest('.mpd-gallery-wrapper');
            const currentPage = parseInt($gallery.data('current-page')) || 1;
            if (currentPage > 1) {
                mpdLoadPage($gallery, currentPage - 1);
            }
        });

        $(document).on('click', '.mpd-next-page', function(e) {
            e.preventDefault();
            const $gallery = $(this).closest('.mpd-gallery-wrapper');
            const currentPage = parseInt($gallery.data('current-page')) || 1;
            const totalPages = parseInt($gallery.data('total-pages')) || 1;
            if (currentPage < totalPages) {
                mpdLoadPage($gallery, currentPage + 1);
            }
        });
    }

    function mpdLoadPage($gallery, page) {
        // Reload current page with new page parameter
        const currentUrl = new URL(window.location);
        currentUrl.searchParams.set('mpd_page', page);
        window.location.href = currentUrl.toString();
    }

    function showSingleImage(imageUrl, title) {
        $('#mpdLightboxContent').html(`<img src="${imageUrl}" alt="${title}">`);
        $('#mpdNavigationControls').hide();
        showLightbox();
    }

    function showImageGallery(images, title, startIndex = 0) {
        const currentIndex = Math.max(0, Math.min(startIndex, images.length - 1));
        const currentImage = images[currentIndex];
        
        $('#mpdLightboxContent').html(`<img src="${currentImage}" alt="${title}">`);
        
        // Update navigation
        $('#mpdImageCounter').text(`${currentIndex + 1} of ${images.length}`);
        $('#mpdPrevImage').prop('disabled', currentIndex === 0);
        $('#mpdNextImage').prop('disabled', currentIndex === images.length - 1);
        $('#mpdNavigationControls').show();
        
        // Store data for navigation
        $('#mpdLightbox').data('images', images);
        $('#mpdLightbox').data('current-index', currentIndex);
        
        showLightbox();
    }

    function showVideo(videoUrl, title) {
        // Clear any existing content completely
        $('#mpdLightboxContent').empty();
        
        // Simple video HTML insertion - no autoplay, no extra complexity
        const videoHtml = `
            <video id="mpdVideo" controls style="max-width: 100%; max-height: 80vh;">
                <source src="${videoUrl}" type="video/mp4">
                Your browser does not support the video tag.
            </video>
            <div class="mpd-video-title">${title}</div>
        `;
        
        // Insert video HTML
        $('#mpdLightboxContent').html(videoHtml);
        
        // Hide navigation controls for video
        $('#mpdNavigationControls').hide();
        
        // Show lightbox
        showLightbox();
        
        // Optional: Try to play after a delay (user can always click play if blocked)
        setTimeout(function() {
            const video = document.getElementById('mpdVideo');
            if (video) {
                video.play().catch(function() {
                    // Silent fail - user can click play manually
                });
            }
        }, 300);
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