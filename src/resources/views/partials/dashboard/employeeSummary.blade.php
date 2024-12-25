<div class="row">
	<div class="col-12">
		<h3>Employee Summary</h3>
		<table class="table table-striped">
			<thead>
				<tr>
					<th>Name</th>
					<th>Department</th>
					<th>Email</th>
					<th>Hire Date</th>
				</tr>
			</thead>
			<tbody>
				@foreach ($employeeSummary as $employee)
					<tr>
						<td>{{ $employee->first_name }} {{ $employee->last_name }}</td>
						<td>{{ $employee->department }}</td>
						<td>{{ $employee->email }}</td>
						<td>{{ $employee->hire_date }}</td>
					</tr>
				@endforeach
			</tbody>
		</table>
	</div>
</div>