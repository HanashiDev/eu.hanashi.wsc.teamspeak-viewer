WCF.User.Panel.Teamspeak = WCF.User.Panel.Abstract.extend({
	/**
	 * @see	WCF.User.Panel.Abstract.init()
	 */
	init: function(options) {
		this._super($('#teamspeakUsers'), 'teamspeakUsers', options);
		
		WCF.System.Event.addListener('eu.hanashi.wsc.menu-test.userPanel', 'reset', (function() {
			this.resetItems();
			this._loadData = true;
		}).bind(this));
		
		require(['EventHandler'], (function(EventHandler) {
			EventHandler.add('com.woltlab.wcf.UserMenuMobile', 'more', (function(data) {
				if (data.identifier === 'eu.hanashi.wsc.teamspeak-viewer') {
					this.toggle();
				}
			}).bind(this));
		}).bind(this));
	},

	/**
	 * @see	WCF.User.Panel.Abstract._initDropdown()
	 */
	_initDropdown: function() {
		var $dropdown = this._super();
		$('<li>' + this._options.usersOnlineCount + ' ' + this._options.usersOnline + '</li>').appendTo($dropdown.getLinkList());
		
		return $dropdown;
	},
	
	/**
	 * @see	WCF.User.Panel.Abstract._load()
	 */
	_load: function() {
		this._proxy.setOption('data', {
			actionName: 'getClientlist',
			className: 'wcf\\data\\teamspeak\\viewer\\TeamspeakViewerAction'
		});
		this._proxy.sendRequest();
	}
});
