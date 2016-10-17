<div id="sidebar-wrapper">
	<ul class="sidebar-nav">
		<li class="sidebar-brand">
			<a href="#">
			SecureCloud
			</a>
		</li>
		<li>
			<a href="#" onclick="gotoPage('Graphs');">Graph</a>
		</li>
		<li>
			<a href="#" onclick="gotoPage('Stats');">Statistics</a>
		</li>
		<li>
			<a href="#" onclick="gotoPage('Dbqry');">Database Querry</a>
		</li>
		<li>
			<a href="#" onclick="gotoPage('Profiles');">Profiles</a>
		</li>
		<li>
			<a href="#" onclick="gotoPage('Usrctrl');">User Control</a>
		</li>
	</ul>
	
	<div style="position: fixed; bottom: 0px; padding: 5px; width: 200px;">
		<div class="well well-sm">
			Selected profile: <span class="label label-default"><?php echo $NAME; ?></span><br>
			Timeslot: <span id="SidebarSelectedTime"></span>
		</div>
	</div>
</div>
