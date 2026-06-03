@extends('layouts.bootstrap')

@section('title', 'Tickets')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Tickets</h1>
        <a href="{{ route('welcome') }}" class="btn btn-outline-secondary btn-sm">Back to home</a>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-md-4">
            <div class="card p-3 h-100">
                <div class="text-muted small">Created in last day</div>
                <div class="display-6" id="statsDay">-</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card p-3 h-100">
                <div class="text-muted small">Created in last week</div>
                <div class="display-6" id="statsWeek">-</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card p-3 h-100">
                <div class="text-muted small">Created in last month</div>
                <div class="display-6" id="statsMonth">-</div>
            </div>
        </div>
    </div>

    <div class="card p-3 mb-3">
        <div class="row g-2">
            <div class="col-md-2">
                <label for="idFilter" class="form-label">ID</label>
                <input id="idFilter" type="number" min="1" class="form-control" placeholder="e.g. 5">
            </div>
            <div class="col-md-3">
                <label for="customerNameFilter" class="form-label">Customer name</label>
                <input id="customerNameFilter" type="text" class="form-control" placeholder="Name">
            </div>
            <div class="col-md-3">
                <label for="customerEmailFilter" class="form-label">Customer email</label>
                <input id="customerEmailFilter" type="text" class="form-control" placeholder="Email">
            </div>
            <div class="col-md-4">
                <label for="subjectFilter" class="form-label">Subject</label>
                <input id="subjectFilter" type="text" class="form-control" placeholder="Ticket subject">
            </div>
            <div class="col-md-3">
                <label for="statusFilter" class="form-label">Status</label>
                <select id="statusFilter" class="form-select">
                    <option value="">All</option>
                    <option value="new">new</option>
                    <option value="in_progress">in_progress</option>
                    <option value="processed">processed</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="createdAtFilter" class="form-label">Created date</label>
                <input id="createdAtFilter" type="date" class="form-control">
            </div>
            <div class="col-md-2">
                <label for="perPageFilter" class="form-label">Per page</label>
                <select id="perPageFilter" class="form-select">
                    <option value="10" selected>10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
            </div>
            <div class="col-md-4 d-flex align-items-end gap-2">
                <button id="refreshTickets" class="btn btn-primary">Apply filters</button>
                <button id="resetFilters" class="btn btn-outline-secondary">Reset</button>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Customer</th>
                        <th>Subject</th>
                        <th>Status</th>
                        <th style="min-width: 220px;">Actions</th>
                    </tr>
                </thead>
                <tbody id="ticketsTableBody">
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">Loading...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="modal fade" id="ticketDetailsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Ticket details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-3">ID</dt>
                        <dd class="col-sm-9" id="ticketDetailId">-</dd>

                        <dt class="col-sm-3">Customer</dt>
                        <dd class="col-sm-9" id="ticketDetailCustomer">-</dd>

                        <dt class="col-sm-3">Email</dt>
                        <dd class="col-sm-9" id="ticketDetailEmail">-</dd>

                        <dt class="col-sm-3">Subject</dt>
                        <dd class="col-sm-9" id="ticketDetailSubject">-</dd>

                        <dt class="col-sm-3">Message</dt>
                        <dd class="col-sm-9" id="ticketDetailMessage">-</dd>

                        <dt class="col-sm-3">Status</dt>
                        <dd class="col-sm-9" id="ticketDetailStatus">-</dd>

                        <dt class="col-sm-3">Created at</dt>
                        <dd class="col-sm-9" id="ticketDetailCreatedAt">-</dd>

                        <dt class="col-sm-3">Manager reply at</dt>
                        <dd class="col-sm-9" id="ticketDetailManagerReplyAt">-</dd>

                        <dt class="col-sm-3">Attachments</dt>
                        <dd class="col-sm-9">
                            <ul class="mb-0 ps-3" id="ticketDetailAttachments">
                                <li class="text-muted">No attachments</li>
                            </ul>
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mt-3">
        <small id="paginationInfo" class="text-muted"></small>
        <div class="d-flex gap-2">
            <button id="prevPage" class="btn btn-outline-secondary btn-sm">Prev</button>
            <button id="nextPage" class="btn btn-outline-secondary btn-sm">Next</button>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        window.ticketAdminConfig = {
            statisticsUrl: @json(route('admin.api.tickets.statistics')),
            indexUrl: @json(route('admin.api.tickets.index')),
            showBaseUrl: @json(url('/admin/api/tickets')),
            updateBaseUrl: @json(url('/admin/api/tickets')),
        };
    </script>
@endpush
