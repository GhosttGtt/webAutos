class MaterialModal {
    constructor(elementId) {
        this.modalElement = document.getElementById(elementId);
        this.overlay = document.createElement('div');
        this.overlay.classList.add('material-modal-overlay');
        document.body.appendChild(this.overlay);

        this.closeButton = this.modalElement.querySelector('.material-modal-close-button');
        if (this.closeButton) {
            this.closeButton.addEventListener('click', this.close.bind(this));
        }

        this.overlay.addEventListener('click', (e) => {
            if (e.target === this.overlay) {
                this.close();
            }
        });

        // Move the modal content into the overlay
        this.overlay.appendChild(this.modalElement);
        this.modalElement.style.display = 'block'; // Ensure it's visible within the overlay
    }

    open() {
        this.overlay.classList.add('show');
        document.body.style.overflow = 'hidden'; // Prevent scrolling of background
    }

    close() {
        this.overlay.classList.remove('show');
        document.body.style.overflow = ''; // Restore scrolling
    }
}

// Function to update Material Design like text fields
function updateMaterialTextFields() {
    document.querySelectorAll('.material-form-group input, .material-form-group textarea').forEach(input => {
        if (input.value) {
            input.classList.add('has-value');
        } else {
            input.classList.remove('has-value');
        }
        input.addEventListener('focus', () => input.classList.add('is-focused'));
        input.addEventListener('blur', () => {
            input.classList.remove('is-focused');
            if (input.value) {
                input.classList.add('has-value');
            } else {
                input.classList.remove('has-value');
            }
        });
    });
}

// Initial call to set up existing fields
document.addEventListener('DOMContentLoaded', updateMaterialTextFields);