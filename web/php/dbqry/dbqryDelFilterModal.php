<div id="DbqryDelFilterModal" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Delete filter</h4>
			</div>
			
			<div class="modal-body">
				Do you really want to delete this filter?
				
				<input type="hidden" id="DbqryDelFilterModalNameValue">
				<input type="hidden" id="DbqryDelFilterModalFilterValue">
			</div>
			
			<div class="modal-footer">
				<a href="#" class="btn btn-danger" data-dismiss="modal" onclick="Filter.deleteFilter();">Delete</a>
				<a href="#" class="btn btn-default" data-dismiss="modal">Close</a>
			</div>
		</div>
	</div>
</div>