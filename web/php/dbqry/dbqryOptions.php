<script type="text/javascript">
	function toggleFieldsSelector() {
		if (document.getElementById("FieldsSelectorCheckbox").checked) {
			document.getElementById("FieldsSelector").style.display = "";
		}
		else {
			document.getElementById("FieldsSelector").style.display = "none";
		}
	}
</script>

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
				$size = sizeof($ARR_OPTIONS_AGGREG_FIELDS);
				for($i = 0; $i < $size; $i++) {
					echo '<option value=\'',$ARR_OPTIONS_AGGREG_FIELDS[$i]->name,'\' title=\'',$ARR_OPTIONS_AGGREG_FIELDS[$i]->hint,'\'>',$ARR_OPTIONS_AGGREG_FIELDS[$i]->name,'</option>';
				}
				$size = sizeof($ARR_OPTIONS_COMMON_FIELDS);
				for($i = 0; $i < $size; $i++) {
					echo '<option value=\'',$ARR_OPTIONS_COMMON_FIELDS[$i]->name,'\' title=\'',$ARR_OPTIONS_COMMON_FIELDS[$i]->hint,'\'>',$ARR_OPTIONS_COMMON_FIELDS[$i]->name,'</option>';
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
			$size = sizeof($ARR_OPTIONS_ORDERBY_FIELDS);
				for($i = 0; $i < $size; $i++) {
					echo '<option value=\'',$ARR_OPTIONS_ORDERBY_FIELDS[$i]->name,'\' title=\'',$ARR_OPTIONS_ORDERBY_FIELDS[$i]->hint,'\'>',$ARR_OPTIONS_ORDERBY_FIELDS[$i]->name,'</option>';
				}
				$size = sizeof($ARR_OPTIONS_COMMON_FIELDS);
				for($i = 0; $i < $size; $i++) {
					echo '<option value=\'',$ARR_OPTIONS_COMMON_FIELDS[$i]->name,'\' title=\'',$ARR_OPTIONS_COMMON_FIELDS[$i]->hint,'\'>',$ARR_OPTIONS_COMMON_FIELDS[$i]->name,'</option>';
				}
			?>
		</select>
	</div>
	<div class="col-sm-6">
		<!--Checkbox butons-->
		<div class="btn-group" data-toggle="buttons">
			<label class="btn btn-default active">
				<input type="radio" checked name="OrderByDirRadio" value="" autocomplete="off"> --
			</label>

			<label class="btn btn-default">
				<input type="radio" name="OrderByDirRadio" value="#asc" autocomplete="off"> Ascending
			</label>

			<label class="btn btn-default">
				<input type="radio" name="OrderByDirRadio" value="#desc" autocomplete="off"> Descending
			</label>
		</div>
		<!--Checkbox butons-->
	</div>
</div>

<div class="row" style="vertical-align: middle;">
	<div class="col-sm-2">
		<b>Output:</b>
	</div>
	<div class="col-sm-10">
		<div class="btn-group" data-toggle="buttons">
			<label class="btn btn-default active">
				<input type="radio" checked name="OutputFormatRadio" value="pretty" autocomplete="off"> Pretty
			</label>

			<label class="btn btn-default">
				<input type="radio" name="OutputFormatRadio" value="csv" autocomplete="off"> CSV
			</label>
			
			<label class="btn btn-default">
				<input type="radio" name="OutputFormatRadio" value="prettycsv" autocomplete="off"> Pretty CSV
			</label>
		</div>
		
		<div class="btn-group" data-toggle="buttons">
			<label class="btn btn-default">
				<input type="radio" name="OutputVolumeConvRadio" value="--output-volume-conv=none" autocomplete="off"> Raw
			</label>

			<label class="btn btn-default active">
				<input type="radio" checked name="OutputVolumeConvRadio" value="--output-volume-conv=metric-prefix" autocomplete="off"> K,M,B
			</label>

			<label class="btn btn-default">
				<input type="radio" name="OutputVolumeConvRadio" value="--output-volume-conv=binary-prefix" autocomplete="off"> Ki,Mi,Bi
			</label>
		</div>
		
		<label class="btn btn-default">
			<input type="checkbox" id="Option_OutputNoEllipsize"> No Ellipsize
		</label>
		
		<label class="btn btn-default">
			<input type="checkbox" id="Option_OutputNoSummary"> No summary
		</label>
		
		<label class="btn btn-default" onclick="toggleFieldsSelector()">
			<input type="checkbox" id="FieldsSelectorCheckbox"> Custom output fields
		</label>
	</div>
</div>

<div class="row" style="vertical-align: middle;" id="FieldsSelector">
	<div class="col-sm-2">
		<b>Fields:</b>
	</div>
	<div class="col-sm-10">
		<select id="Option_FieldList" multiple="multiple" class="form-control">
				<?php
				$size = sizeof($ARR_OPTIONS_FIELDSEL_FIELDS);
				for($i = 0; $i < $size; $i++) {
					echo '<option value=\'',$ARR_OPTIONS_FIELDSEL_FIELDS[$i]->name,'\' title=\'',$ARR_OPTIONS_FIELDSEL_FIELDS[$i]->hint,'\'>',$ARR_OPTIONS_FIELDSEL_FIELDS[$i]->name,'</option>';
				}
				$size = sizeof($ARR_OPTIONS_COMMON_FIELDS);
				for($i = 0; $i < $size; $i++) {
					echo '<option value=\'',$ARR_OPTIONS_COMMON_FIELDS[$i]->name,'\' title=\'',$ARR_OPTIONS_COMMON_FIELDS[$i]->hint,'\'>',$ARR_OPTIONS_COMMON_FIELDS[$i]->name,'</option>';
				}
				?>
			</select>
	</div>
</div>
