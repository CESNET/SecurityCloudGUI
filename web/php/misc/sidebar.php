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
	
	<div style="position: relative; bottom: 0px" class="alert alert-info text-center" role="alert">
		Selected profile: <?php echo $NAME; ?><br>
		Selected time: <span id="SidebarSelectedTime"></span>
	</div>
</div>
