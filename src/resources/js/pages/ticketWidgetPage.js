function getElement(id) {
    return document.getElementById(id);
}

async function showError(text) {
    await Swal.fire({
        icon: 'error',
        title: 'Error',
        text,
        confirmButtonText: 'OK',
    });
}

async function showSuccess(text) {
    await Swal.fire({
        icon: 'success',
        title: 'Success',
        text,
        confirmButtonText: 'OK',
    });
}

function extractValidationError(error) {
    const validationErrors = error?.response?.data?.errors;
    if (!validationErrors) {
        return null;
    }

    const firstError = Object.values(validationErrors)[0];
    if (Array.isArray(firstError)) {
        return firstError[0] ?? null;
    }

    return typeof firstError === 'string' ? firstError : null;
}

async function submitTicketForm(event) {
    event.preventDefault();

    const form = getElement('ticketForm');
    const submitButton = getElement('submitBtn');

    submitButton.disabled = true;
    submitButton.textContent = 'Sending...';

    try {
        const formData = new FormData(form);

        const response = await axios.post(window.ticketWidgetConfig.storeUrl, formData, {
            headers: {
                Accept: 'application/json',
            },
        });

        const successMessage = response.data?.message ?? 'Ticket created successfully';
        await showSuccess(successMessage);
        form.reset();
    } catch (error) {
        const validationMessage = extractValidationError(error);
        const defaultMessage = error?.response?.data?.message ?? 'Failed to create ticket';
        const errorMessage = validationMessage ?? defaultMessage;
        await showError(errorMessage);
    } finally {
        submitButton.disabled = false;
        submitButton.textContent = 'Send ticket';
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const form = getElement('ticketForm');
    if (!form || !window.ticketWidgetConfig) {
        return;
    }

    form.addEventListener('submit', submitTicketForm);
});
