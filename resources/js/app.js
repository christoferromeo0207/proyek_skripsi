import './bootstrap';
import 'flowbite';

document.addEventListener('DOMContentLoaded', function () {
    console.log('DOM fully loaded and parsed');

    // Open modals
    document.querySelectorAll('[data-modal]').forEach(function(card) {
        card.addEventListener('click', function() {
            var modalId = this.getAttribute('data-modal');
            var modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.remove('hidden');
                // Disable scrolling
                document.body.style.overflow = 'hidden';
            }
        });
    });

    // Close modals and handle redirect
    document.querySelectorAll('.close-modal').forEach(function(button) {
        button.addEventListener('click', function() {
            var categoryId = this.getAttribute('data-category-id');
            closeModal(categoryId); 

            // Log the redirect URL
            var redirectUrl = "{{ route('categories.index') }}";
            console.log("Redirecting to: ", redirectUrl); 
            
            // Redirect to categories page
            window.location.href = redirectUrl;
        });
    });


    // Initialize all modals using Bootstrap
    var myModalEl = document.querySelectorAll('.modal');
    myModalEl.forEach(function(modalEl) {
        var modal = new bootstrap.Modal(modalEl);
    });

    $('#editModal').on('shown.bs.modal', function () {
        $('html, body').animate({
            scrollTop: $(this).offset().top
        }, 500);
    });
});

// Close modal function
function closeModal(categoryId) {
    const modal = document.getElementById(`modal-${categoryId}`);
    if (modal) {
        modal.classList.add('hidden');
        // Enable scrolling
        document.body.style.overflow = 'auto';
    }
}

button.addEventListener('click', function() {
    var categoryId = this.getAttribute('data-category-id');
    closeModal(categoryId); // Close the modal
    var redirectUrl = "{{ route('categories') }}"; // Log this to check
    console.log("Redirecting to: ", redirectUrl);
    window.location.href = redirectUrl; // Redirect to categories page
});


document.getElementById('search-form').addEventListener('submit', function(event) {
    event.preventDefault(); // Mencegah refresh halaman
    const searchTerm = document.getElementById('search-dropdown').value;
    // Logika untuk menampilkan data berdasarkan searchTerm
    console.log("Searching for:", searchTerm); // Ganti dengan logika pencarian Anda
});


const offcanvasElement = document.getElementById('createNotulenOffcanvas');

// Function to add the backdrop
function showBackdrop() {
    const backdrop = document.createElement('div');
    backdrop.classList.add('offcanvas-backdrop');
    document.body.appendChild(backdrop);
}

// Function to remove the backdrop
function hideBackdrop() {
    const backdrop = document.querySelector('.offcanvas-backdrop');
    if (backdrop) {
        backdrop.remove();
    }
}

// Add event listener for when the offcanvas is shown
offcanvasElement.addEventListener('show.bs.offcanvas', function () {
    showBackdrop();
});

// Add event listener for when the offcanvas is hidden
offcanvasElement.addEventListener('hidden.bs.offcanvas', function () {
    hideBackdrop();
});





