@extends('layouts.bootstrap')

@section('title', 'Support Portal')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-10">
            @auth
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <h2 class="h5">Admin session active</h2>
                        <p class="text-muted">
                            You are logged in as <strong>{{ auth()->user()->email }}</strong>.
                        </p>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.tickets.page') }}" class="btn btn-primary">Tickets</a>
                        </div>
                    </div>
                </div>
            @else
                <div class="card shadow-sm mb-3">
                    <div class="card-body p-4">
                        <h2 class="h5 mb-3">Admin access</h2>
                        <a href="{{ route('admin.login') }}" class="btn btn-dark">Login as admin</a>
                    </div>
                </div>

                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                            <h2 class="h5 mb-0">Create a ticket</h2>
                            <button id="toggleWidgetBtn" class="btn btn-primary">Open ticket widget</button>
                        </div>

                        <p class="text-muted mb-3">
                            The form is loaded as a separate page through iframe to keep it embeddable.
                        </p>

                        <div id="widgetContainer" class="d-none">
                            <iframe
                                src="{{ route('tickets.widget') }}"
                                title="Ticket Widget"
                                class="w-100 border rounded"
                                style="min-height: 640px;"
                            ></iframe>
                        </div>
                    </div>
                </div>
            @endauth
        </div>
    </div>
@endsection
