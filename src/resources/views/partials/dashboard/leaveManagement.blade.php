<div class="row">
	<div class="col-12">
		<h3>Leave Management</h3>
		<table class="table table-striped">
			<thead>
				<tr>
					<th>User</th>
					<th>Leave Type</th>
					<th>Start Date</th>
					<th>End Date</th>
					<th>Status</th>
				</tr>
			</thead>
			<tbody>
				@foreach ($leaveRequests as $leave)
					<tr>
						<td>{{ $leave->user->first_name }} {{ $leave->user->last_name }}</td>
						<td>{{ $leave->leaveType->name }}</td>
						<td>{{ $leave->start_date }}</td>
						<td>{{ $leave->end_date }}</td>
						<td>{{ $leave->status }}</td>
					</tr>
				@endforeach
			</tbody>
		</table>
	</div>
</div>