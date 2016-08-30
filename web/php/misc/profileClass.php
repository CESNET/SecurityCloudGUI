<?php
	/**
	 *  Profile channel manages important info of a single <channel> of each profile <channelList>
	 *  It has a name (like "smtp", "ch1", ...), filter ("ipVersion = 4", "dstport > 1024", ...)
	 *  And also has a full set of sources from <sourceList>. Sources are stored as strings in array ("smtp", "ch1").
	 *  Since shadow profiles don't have to use all of channels from parent profile (not sure if ipfixcol actually supports it)
	 *  this can come in handy when you are making queries on the shadow profiles.
	 */
	class ProfileChannel {
		private $name		= 'default';
		private $filter		= "";
		private $sources	= null;
		
		/**
		 *  Sets a name of the channel
		 */
		public function setName($name) {
			$this->name = $name;
		}
		
		/**
		 *  Stores the filter channels uses
		 */
		public function setFilter($filter) {
			$this->filter = $filter;
		}
		
		public function setSources($src) {
			$this->sources = $src;
		}
		
		/**
		 *  Retrieves the name of the channel
		 *  string is returned
		 */
		public function getName() {
			return $this->name;
		}
		
		/**
		 *  Retrieves the filter of the channel
		 *  string is returned
		 */
		public function getFilter() {
			return $this->filter;
		}
		
		public function getSources() {
			return $this->sources;
		}
	};

	/**
	*	A tree structure into which is parsed the xml file with the profiles configuration
	*	Each profile has its name which represents full path to the profile in the profile
	*	structure (ie.: /live or /live/emails or /live/http/secure)
	*	Each profile (except for live) has a pointer to it's parent, pointers to it's children
	*	(maintained in an array). It also stores information whether it's a shadow profile or not
	* 	also has an array of channels (see ProfileChannel above for more info).
	*/
	class Profile {
		private $name		= 'default';
		private $children	= array();
		private $childPtr	= -1;
		private $channels	= array();
		private $chnlPtr	= -1;
		private $tatulda	= null;
		private $shadow		= false;
		
		/**
		 *  Sets the name of the profile
		 */
		public function setName($name) {
			$this->name = $name;
		}
		
		/**
		 *  Sets the pointer to parent profile
		 */
		public function setParent(&$ref) {
			$this->tatulda = $ref;
		}
		
		/**
		 *  Sets the shadow type flag on and off
		 */
		public function setShadow($bool) {
			$this->shadow = $bool;
		}
		
		/**
		 *  Adds a child alongside with the pointer to its parent (this profile)
		 *  returns a pointer to the child (required by createProfileTreeFromXml)
		 */
		public function addChild() {
			$child = new Profile();
			$child->setParent($this);
			array_push($this->children, $child);
			$++this->childPtr;
			
			return $this->children[$this->childPtr];
		}
		
		/**
		 *  Adds a channel to the profile. Channel must have everything defined: name, filter and array of sources
		 */
		public function addChannel($name, $filter, $sources) {
			$chnl = new ProfileChannel();
			$chnl->setName($name);
			$chnl->setFilter($filter);
			$chnl->setSources($sources);
			
			array_push($this->channels, $chnl);
			$++this->chnlPtr;
		}
		
		/**
		 *  Retrieves array of children
		 *  returns array of Profile objects
		 */
		public function getChildren() {
			return $this->children;
		}
		
		/**
		 *  Retrieves array of channels
		 *  returns array of ProfileChannel objects
		 */
		public function getChannels() {
			return $this->channels;
		}
		
		/**
		 *  Retrives a name of the profile. It's actually a full path to the profile
		 *  If you want only a name of the profile without the path, use the following call:
		 *  preg_replace("/^\/[a-zA-Z0-9_\/]+\//", "", $object->getName()); (this is what printProfile does)
		 *  This will always leave "live" profile as "/live"
		 */
		public function getName() {
			return $this->name;
		}
		
		/**
		 *  Retrieves information about shadow status of a profile
		 *  returns a boolean
		 */
		public function getShadow() {
			return $this->shadow;
		}
		
		/**
		 *  Gets the name (full path to) of parent profile
		 *  Silently does nothing if you want the name of parent of /live
		 */
		public function getParentName() {
			if ($this->tatulda == null) return;
			
			return $this->tatulda->getName();
		}
		
		/**
		 *	Returns a pointer to the Profile object of
		 *	of a node's parent.
		 */
		public function getParent() {
			return $this->tatulda;
		}
	}
?>
