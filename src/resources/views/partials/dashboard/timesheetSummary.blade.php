<div class="row">
	<div class="col-12">
		<h3>Timesheet Summary</h3>
		<table class="table table-striped">
			<thead>
				<tr>
					<th>User</th>
					<th>Entry Date</th>
					<th>Clock In</th>
					<th>Clock Out</th>
					<th>Status</th>
				</tr>
			</thead>
			<tbody>
				@foreach ($timesheets as $timesheet)
					<tr>
						<td>{{ $timesheet->user->first_name }} {{ $timesheet->user->last_name }}</td>
						<td>{{ $timesheet->entry_date }}</td>
						<td>{{ $timesheet->clock_in_time }}</td>
						<td>{{ $timesheet->clock_out_time }}</td>
						<td>{{ $timesheet->status }}</td>
					</tr>
				@endforeach
			</tbody>
		</table>
	</div>
</div>