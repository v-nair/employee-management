<div class="row">
	<div class="col-12">
		<h3>Expense Overview</h3>
		<table class="table table-striped">
			<thead>
				<tr>
					<th>User</th>
					<th>Expense Type</th>
					<th>Amount</th>
					<th>Status</th>
				</tr>
			</thead>
			<tbody>
				@foreach ($expenseOverview as $expense)
					<tr>
						<td>{{ $expense->user->first_name }} {{ $expense->user->last_name }}</td>
						<td>{{ $expense->expenseType->name }}</td>
						<td>{{ $expense->amount }}</td>
						<td>{{ $expense->status }}</td>
					</tr>
				@endforeach
			</tbody>
		</table>
	</div>
</div>