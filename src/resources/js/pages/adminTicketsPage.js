const statuses = ['new', 'in_progress', 'processed'];

const state = {
    currentPage: 1,
    lastPage: 1,
};
let ticketDetailsModalInstance = null;

function getElement(id) {
    return document.getElementById(id);
}

function getFilters() {
    const idFilter = getElement('idFilter');
    const customerNameFilter = getElement('customerNameFilter');
    const customerEmailFilter = getElement('customerEmailFilter');
    const subjectFilter = getElement('subjectFilter');
    const statusFilter = getElement('statusFilter');
    const createdAtFilter = getElement('createdAtFilter');
    const perPageFilter = getElement('perPageFilter');

    return {
        id: idFilter.value.trim(),
        customer_name: customerNameFilter.value.trim(),
        customer_email: customerEmailFilter.value.trim(),
        subject: subjectFilter.value.trim(),
        status: statusFilter.value,
        created_at: createdAtFilter.value,
        per_page: perPageFilter.value,
    };
}

function getStatusOptions(currentStatus) {
    return statuses
        .map((status) => {
            const selected = status === currentStatus ? 'selected' : '';
            return `<option value="${status}" ${selected}>${status}</option>`;
        })
        .join('');
}

function renderRows(items) {
    const ticketsTableBody = getElement('ticketsTableBody');

    if (!items.length) {
        ticketsTableBody.innerHTML = `
            <tr>
                <td colspan="5" class="text-center py-4 text-muted">No tickets found</td>
            </tr>
        `;
        return;
    }

    ticketsTableBody.innerHTML = items
        .map(
            (ticket) => `
                <tr>
                    <td>${ticket.id}</td>
                    <td>
                        <div>${ticket.customer?.name ?? '-'}</div>
                        <small class="text-muted">${ticket.customer?.email ?? ''}</small>
                    </td>
                    <td>${ticket.subject}</td>
                    <td><span class="badge text-bg-secondary">${ticket.status}</span></td>
                    <td>
                        <div class="d-flex gap-2">
                            <select class="form-select form-select-sm" id="status-${ticket.id}">
                                ${getStatusOptions(ticket.status)}
                            </select>
                            <button class="btn btn-sm btn-success js-update-status" data-ticket-id="${ticket.id}">
                                Save
                            </button>
                            <button class="btn btn-sm btn-outline-primary js-show-ticket" data-ticket-id="${ticket.id}">
                                View
                            </button>
                        </div>
                    </td>
                </tr>
            `
        )
        .join('');
}

function updatePagination(meta) {
    const paginationInfo = getElement('paginationInfo');
    const prevPageButton = getElement('prevPage');
    const nextPageButton = getElement('nextPage');

    state.currentPage = meta.current_page;
    state.lastPage = meta.last_page;

    paginationInfo.textContent = `Showing ${meta.from ?? 0}-${meta.to ?? 0} of ${meta.total}`;
    prevPageButton.disabled = state.currentPage <= 1;
    nextPageButton.disabled = state.currentPage >= state.lastPage;
}

async function showError(text) {
    await Swal.fire({
        icon: 'error',
        title: 'Error',
        text,
        confirmButtonText: 'OK',
    });
}

async function loadStatistics() {
    const statsDayElement = getElement('statsDay');
    const statsWeekElement = getElement('statsWeek');
    const statsMonthElement = getElement('statsMonth');

    if (!statsDayElement || !statsWeekElement || !statsMonthElement) {
        return;
    }

    try {
        const response = await axios.get(window.ticketAdminConfig.statisticsUrl, {
            headers: {
                Accept: 'application/json',
            },
        });

        const statistics = response.data?.data ?? {};
        statsDayElement.textContent = statistics.day ?? 0;
        statsWeekElement.textContent = statistics.week ?? 0;
        statsMonthElement.textContent = statistics.month ?? 0;
    } catch (error) {
        const errorMessage = error?.response?.data?.message ?? 'Failed to load statistics';
        await showError(errorMessage);
    }
}

async function loadTickets() {
    const ticketsTableBody = getElement('ticketsTableBody');

    ticketsTableBody.innerHTML = `
        <tr>
            <td colspan="5" class="text-center py-4 text-muted">Loading...</td>
        </tr>
    `;

    try {
        const params = {
            ...getFilters(),
            page: state.currentPage,
        };

        Object.keys(params).forEach((key) => {
            if (params[key] === '') {
                delete params[key];
            }
        });

        const response = await axios.get(window.ticketAdminConfig.indexUrl, {
            params,
            headers: {
                Accept: 'application/json',
            },
        });

        renderRows(response.data.data ?? []);
        updatePagination(response.data.meta);
    } catch (error) {
        const errorMessage = error?.response?.data?.message ?? 'Failed to load tickets';
        await showError(errorMessage);
    }
}

async function updateStatus(buttonElement) {
    const ticketId = buttonElement.dataset.ticketId;
    const statusSelect = getElement(`status-${ticketId}`);
    const selectedStatus = statusSelect.value;

    buttonElement.disabled = true;
    const originalButtonText = buttonElement.textContent;
    buttonElement.textContent = 'Saving...';

    try {
        await axios.patch(
            `${window.ticketAdminConfig.updateBaseUrl}/${ticketId}`,
            {status: selectedStatus},
            {
                headers: {
                    Accept: 'application/json',
                },
            }
        );

        await Swal.fire({
            icon: 'success',
            title: 'Updated',
            text: `Ticket #${ticketId} updated`,
            timer: 1400,
            showConfirmButton: false,
        });

        await loadTickets();
    } catch (error) {
        const errorMessage = error?.response?.data?.message ?? `Failed to update ticket #${ticketId}`;
        await showError(errorMessage);
    } finally {
        buttonElement.disabled = false;
        buttonElement.textContent = originalButtonText;
    }
}

function formatDate(value) {
    if (!value) {
        return '-';
    }

    const date = new Date(value);
    if (Number.isNaN(date.getTime())) {
        return value;
    }

    return date.toLocaleString();
}

function renderTicketDetails(ticket) {
    getElement('ticketDetailId').textContent = ticket.id ?? '-';
    getElement('ticketDetailCustomer').textContent = ticket.customer?.name ?? '-';
    getElement('ticketDetailEmail').textContent = ticket.customer?.email ?? '-';
    getElement('ticketDetailSubject').textContent = ticket.subject ?? '-';
    getElement('ticketDetailMessage').textContent = ticket.message ?? '-';
    getElement('ticketDetailStatus').textContent = ticket.status ?? '-';
    getElement('ticketDetailCreatedAt').textContent = formatDate(ticket.created_at);
    getElement('ticketDetailManagerReplyAt').textContent = formatDate(ticket.manager_reply_at);

    const attachmentsContainer = getElement('ticketDetailAttachments');
    const attachments = ticket.attachments ?? [];
    if (!attachments.length) {
        attachmentsContainer.innerHTML = '<li class="text-muted">No attachments</li>';
        return;
    }

    attachmentsContainer.innerHTML = attachments
        .map(
            (attachment) => `
                <li>
                    <a href="${attachment.url}" target="_blank" rel="noopener noreferrer">${attachment.file_name}</a>
                </li>
            `
        )
        .join('');
}

async function showTicketDetails(ticketId) {
    try {
        const response = await axios.get(`${window.ticketAdminConfig.showBaseUrl}/${ticketId}`, {
            headers: {
                Accept: 'application/json',
            },
        });

        renderTicketDetails(response.data.data);
        ticketDetailsModalInstance.show();
    } catch (error) {
        const errorMessage = error?.response?.data?.message ?? `Failed to load ticket #${ticketId}`;
        await showError(errorMessage);
    }
}

function resetFilters() {
    getElement('idFilter').value = '';
    getElement('customerNameFilter').value = '';
    getElement('customerEmailFilter').value = '';
    getElement('subjectFilter').value = '';
    getElement('statusFilter').value = '';
    getElement('createdAtFilter').value = '';
    getElement('perPageFilter').value = '10';
    state.currentPage = 1;
}

function bindEvents() {
    const refreshButton = getElement('refreshTickets');
    const resetButton = getElement('resetFilters');
    const prevPageButton = getElement('prevPage');
    const nextPageButton = getElement('nextPage');
    const ticketsTableBody = getElement('ticketsTableBody');

    refreshButton.addEventListener('click', async () => {
        state.currentPage = 1;
        await loadTickets();
    });

    resetButton.addEventListener('click', async () => {
        resetFilters();
        await loadTickets();
    });

    prevPageButton.addEventListener('click', async () => {
        if (state.currentPage > 1) {
            state.currentPage -= 1;
            await loadTickets();
        }
    });

    nextPageButton.addEventListener('click', async () => {
        if (state.currentPage < state.lastPage) {
            state.currentPage += 1;
            await loadTickets();
        }
    });

    ticketsTableBody.addEventListener('click', async (event) => {
        const target = event.target;
        if (!(target instanceof HTMLElement)) {
            return;
        }

        if (target.classList.contains('js-update-status')) {
            await updateStatus(target);
            return;
        }

        if (target.classList.contains('js-show-ticket')) {
            const ticketId = target.dataset.ticketId;
            await showTicketDetails(ticketId);
        }
    });
}

document.addEventListener('DOMContentLoaded', async () => {
    if (!window.ticketAdminConfig || !getElement('ticketsTableBody')) {
        return;
    }

    const modalElement = getElement('ticketDetailsModal');
    ticketDetailsModalInstance = new bootstrap.Modal(modalElement);

    await loadStatistics();
    bindEvents();
    await loadTickets();
});
