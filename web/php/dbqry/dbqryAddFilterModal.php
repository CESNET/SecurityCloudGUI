<div id="DbqryAddFilterModal" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Add filter</h4>
			</div>
			
			<div class="modal-body">
				<div class="form-group">
					<label for="fname">Filter name</label>
					<input class="form-control" type="text"
					placeholder="Use short filter description" name="fname" required />
				</div>
			</div>
			
			<div class="modal-footer">
				<a href="#" class="btn btn-primary" data-dismiss="modal" onclick="dbqrySaveFilter();">Save</a>
				<a href="#" class="btn btn-default" data-dismiss="modal">Close</a>
			</div>
		</div>
	</div>
</div>