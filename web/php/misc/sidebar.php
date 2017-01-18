<div id="sidebar-wrapper">
	<ul class="sidebar-nav" id="SidebarIsBig">
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
			<a href="#" onclick="gotoPage('Dbqry');">Database Query</a>
		</li>
		<li>
			<a href="#" onclick="gotoPage('Profiles');">Profiles</a>
		</li>
		<!--li>
			<a href="#" onclick="gotoPage('Usrctrl');">User Control</a>
		</li-->
	</ul>
	<ul class="sidebar-nav" id="SidebarIsSmall">
		<li class="sidebar-brand">
			<a href="#">
				<span class="glyphicon glyphicon-home" aria-hidden="true"></span>
			</a>
		</li>
		<li>
			<a href="#" onclick="gotoPage('Graphs');" rel="tooltip" data-original-title="Graph"><span class="glyphicon glyphicon glyphicon-th-large" title="Graphs" aria-hidden="true"></span></a>
		</li>
		<li>
			<a href="#" onclick="gotoPage('Stats');" rel="tooltip" data-original-title="Statistics"><span class="glyphicon glyphicon-list-alt" aria-hidden="true"></span></a>
		</li>
		<li>
			<a href="#" onclick="gotoPage('Dbqry');" rel="tooltip" data-original-title="Database Query"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></a>
		</li>
		<li>
			<a href="#" onclick="gotoPage('Profiles');" rel="tooltip" data-original-title="Profiles"><span class="glyphicon glyphicon-cog" aria-hidden="true"></span></a>
		</li>
		<!--li>
			<a href="#" onclick="gotoPage('Usrctrl');" rel="tooltip" data-original-title="User Control"><span class="glyphicon glyphicon-user" aria-hidden="true"></span></a>
		</li-->
	</ul>
	
	<div style="position: fixed; bottom: 0px; padding: 5px; width: 200px;">
		<div class="well well-sm">
			Selected profile: <span class="label label-default"><?php echo $NAME; ?></span><br>
			Timeslot: <span id="SidebarSelectedTime"></span>
		</div>
	</div>
</div>

