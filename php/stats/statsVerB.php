<div class="panel panel-primary">
	<div class="panel-heading">
		Statistics timeslot Time A - Time B
	</div>
	<div class="panel-body">

<div class="col-md-4">
	<div class="panel panel-default">
		<div class="panel-heading">
			Flows
		</div>
		<div class="panel-body table-responsive">
			<table class="table table-striped table-condensed">
				<thead>
					<tr>
						<th>Source</th>
						<th>Any</th>
						<th>TCP</th>
						<th>UDP</th>
						<th>ICMP</th>
						<th>Other</th>
					</tr>
				</thead>
				<tbody>
					<?php
						for($i = 0; $i < sizeof($ARR_SOURCES); $i++) {
							echo "<tr>\n";
							echo "\t<td>$ARR_SOURCES[$i]</td>\n";
							for($p = 0; $p < 5; $p++) {
								echo "\t<td>0 /s</td>\n";
							}
							echo "</tr>";
						}
					?>
					<tr>
						<th>Total</th>
						<td>0 j/s</td>
						<td>0 j/s</td>
						<td>0 j/s</td>
						<td>0 j/s</td>
						<td>0 j/s</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</div>
<div class="col-md-4">
	<div class="panel panel-default">
		<div class="panel-heading">
			Traffic
		</div>
		<div class="panel-body table-responsive">
			<table class="table table-striped table-condensed table-hover">
				<thead>
					<tr>
						<th>Source</th>
						<th>Any</th>
						<th>TCP</th>
						<th>UDP</th>
						<th>ICMP</th>
						<th>Other</th>
					</tr>
				</thead>
				<tbody>
					<?php
						for($i = 0; $i < sizeof($ARR_SOURCES); $i++) {
							echo "<tr>\n";
							echo "\t<td>$ARR_SOURCES[$i]</td>\n";
							for($p = 0; $p < 5; $p++) {
								echo "\t<td>0 /s</td>\n";
							}
							echo "</tr>";
						}
					?>
				</tbody>
				<thead>
					<tr>
						<th>Total</th>
						<th>0 j/s</th>
						<th>0 j/s</th>
						<th>0 j/s</th>
						<th>0 j/s</th>
						<th>0 j/s</th>
					</tr>
				</thead>
			</table>
		</div>
	</div>
</div>
<div class="col-md-4">
	<div class="panel panel-default">
		<div class="panel-heading">
			Packets
		</div>
		<div class="panel-body table-responsive">
			<table class="table table-striped table-condensed table-bordered">
				<thead>
					<tr>
						<th>Source</th>
						<th>Any</th>
						<th>TCP</th>
						<th>UDP</th>
						<th>ICMP</th>
						<th>Other</th>
					</tr>
				</thead>
				<tbody>
					<?php
						for($i = 0; $i < sizeof($ARR_SOURCES); $i++) {
							echo "<tr>\n";
							echo "\t<td>$ARR_SOURCES[$i]</td>\n";
							for($p = 0; $p < 5; $p++) {
								echo "\t<td>0 /s</td>\n";
							}
							echo "</tr>";
						}
					?>
				</tbody>
			</table>
		</div>
		<div class="panel-footer table-condensed">
			<table class="table table-bordered">
				<tr>
					<th>Total</th>
					<td>0 j/s</td>
					<td>0 j/s</td>
					<td>0 j/s</td>
					<td>0 j/s</td>
					<td>0 j/s</td>
				</tr>
			</table>
		</div>
	</div>
</div>

</div></div>