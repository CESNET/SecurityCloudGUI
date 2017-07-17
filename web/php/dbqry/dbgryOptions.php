<div class="row" style="vertical-align: middle;">
	<div class="col-lg-6"><div class="row">
		<div class="col-sm-4">
			<b>Limit to:</b>
		</div>
		<div class="col-sm-8">
			<select id="Option_LimitTo_<?php echo $tab; ?>" class="form-control">
				<?php
				$size = sizeof($ARR_OPTIONS_CODE_LIMITTO);
				for($i = 0; $i < $size; $i++) {
					echo '<option value=\'',$ARR_OPTIONS_CODE_LIMITTO[$i],'\'';
					if($i == 0) echo ' selected';
					echo '>',$ARR_OPTIONS_NAME_LIMITTO[$i],'</option>';
				}
				?>
			</select>
		</div>
	</div></div>
	<div class="col-lg-6"><div class="row">
		<div class="col-sm-4">
			<b>Aggregate:</b>
		</div>
		<div class="col-sm-8">
			<select id="Option_AggregateList_<?php echo $tab; ?>" multiple="multiple" class="form-control">
				<?php
				$size = sizeof($ARR_OPTIONS_NAME_FIELDS);
				for($i = 0; $i < $size; $i++) {
					echo '<option value=\'',$ARR_OPTIONS_NAME_FIELDS[$i],'\' title=\'',$ARR_OPTIONS_HINT_FIELDS[$i],'\'>',$ARR_OPTIONS_NAME_FIELDS[$i],'</option>';
				}
				?>
			</select>
		</div>
	</div></div>
</div>
<div class="row" style="vertical-align: middle;">
	<div class="col-sm-2">
		<b>Order by:</b>
	</div>
	<div class="col-sm-4">
		<select id="Option_OrderBy_<?php echo $tab; ?>" class="form-control">
			<option value="none" selected>nothing</option>
			<?php
			$size = sizeof($ARR_OPTIONS_NAME_FIELDS);
			for($i = 0; $i < $size; $i++) {
				echo '<option value=\'',$ARR_OPTIONS_NAME_FIELDS[$i],'\' title=\'',$ARR_OPTIONS_HINT_FIELDS[$i],'\'>',$ARR_OPTIONS_NAME_FIELDS[$i],'</option>';
			}
			?>
		</select>
	</div>
	<div class="col-sm-2">
		<b>Direction:</b>
	</div>
	<div class="col-sm-4">
		<select id="Option_OrderDirection_<?php echo $tab; ?>" class="form-control">
			<option value="" selected>Default</option>
			<option value="#asc">Ascending</option>
			<option value="#desc">Descending</option>
		</select>
	</div>
</div>
<div class="row" style="vertical-align: middle;">
	<div class="col-sm-2">
		<b>Output:</b>
	</div>
	<div class="col-sm-4">
		<select id="Option_OutputFormat_<?php echo $tab; ?>" class="form-control">
			<option value="pretty" selected>pretty</option>
			<option value="csv">csv</option>
			<option value="long">long</option>
		</select>
	</div>
	<div class="col-sm-2">
		&nbsp;
	</div>
	<div class="col-sm-4">
		<label><input type="checkbox" id="Option_OutputNoSummary_<?php echo $tab; ?>"> No summary</label>
	</div>
</div>
