@extends('layouts.base')

@section('title', 'Dashboard')

@push('styles')
	<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@endpush

@section('content')
<div class="container-fluid">
	<div class="row">
		<div class="col-12">
			<h2 class="mb-4">Dashboard</h2>
		</div>
	</div>

	{{-- Dashboard Sections Based on Permissions --}}
	
	{{-- Employee Summary (For Admin and HR) --}}
	@if (auth()->user()->hasRole('Admin') || auth()->user()->hasRole('HR'))
	<div class="row mb-4">
		@include('partials.dashboard.employeeSummary', ['employeeSummary' => $employeeSummary])
	</div>
	@endif

	{{-- Leave Management Section (For Admin, HR, and Manager) --}}
	@if (auth()->user()->hasRole('Admin') || auth()->user()->hasRole('HR') || auth()->user()->hasRole('Manager'))
	<div class="row mb-4">
		@include('partials.dashboard.leaveManagement', ['leaveRequests' => $leaveRequests])
	</div>
	@endif

	{{-- Expense Overview (For Admin and Finance) --}}
	@if (auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Manager') || auth()->user()->hasRole('Finance'))
	<div class="row mb-4">
		@include('partials.dashboard.expenseOverview', ['expenseOverview' => $expenseOverview])
	</div>
	@endif

	{{-- Timesheet Summary (For Admin, Manager, and HR) --}}
	@if (auth()->user()->hasRole('Admin') || auth()->user()->hasRole('HR') || auth()->user()->hasRole('Manager'))
	<div class="row mb-4">
		@include('partials.dashboard.timesheetSummary', ['timesheets' => $timesheets])
	</div>
	@endif

	{{-- Custom Section for Other Roles --}}
	{{-- Add additional custom sections here based on the role and permissions --}}
</div>
@endsection

@push('scripts')
	<script src="{{ asset('js/dashboard.js') }}"></script>
@endpush