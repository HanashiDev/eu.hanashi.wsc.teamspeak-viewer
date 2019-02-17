define(['DateUtil','Ajax','Language'], function(DateUtil, Ajax, Language) {
    "use strict";

    function TeamSpeakViewer(options) {
        this._showData = false;
        this._showPassword = false;
        this._serverPassword = null;

        this._init(options);
    }
    TeamSpeakViewer.prototype = {
        _init: function(options) {
            this._showData = options.showData;
            this._showPassword = options.showPassword;
            this._serverPassword = options.serverPassword;

            var collapseArr = document.getElementsByClassName('channelCollapse');
            for (var i = 0; i < collapseArr.length; i++) {
                var collapseDiv = collapseArr[i];
                collapseDiv.onclick = this._collapseClick.bind(this);
            }
            var channelWrapperArr = document.getElementsByClassName('channelWrapper');
            for (var i = 0; i < channelWrapperArr.length; i++) {
                var channelWrapperDiv = channelWrapperArr[i];
                channelWrapperDiv.onclick = this._channelWrapperClick.bind(this);
            }
        },

        _collapseClick: function(e) {
            var target = e.target;
            var mode = 'none';

            if (target.classList.contains('fa-caret-down')) {
                target.classList.remove('fa-caret-down');
                target.classList.add('fa-caret-right');
            } else if (target.classList.contains('fa-caret-right')) {
                target.classList.remove('fa-caret-right');
                target.classList.add('fa-caret-down');
                mode = 'block';
            }

            var childs = target.parentNode.parentNode.parentNode.children;
            for (var i = 0; i < childs.length; i++) {
                if (childs[i].tagName == 'UL') {
                    childs[i].style.display = mode;
                }
            }
        },

        _channelWrapperClick: function(e) {
            if (e.target.classList.contains('icon')) return;
            var target = e.target;
            if (target.tagName == 'IMG') {
                target = target.parentNode;
            }
            var line = target.parentNode.parentNode;
            var type = line.getAttribute('data-type');
            var id = line.getAttribute('data-id');

            Ajax.api(this, {
                actionName: 'showData',
                parameters: {
                    data: {
                        type: type,
                        id: id
                    }
                }
            });
        },

        _showClientInfos: function(data) {
            var infoBox = document.getElementById('TeamSpeakServerInfo');
            infoBox.innerHTML = '';

            // Name
            var sectionTitle = document.createElement('h2');
            sectionTitle.classList.add('sectionTitle');
            sectionTitle.innerText = data.client_nickname;
            infoBox.appendChild(sectionTitle);

            // Version
            infoBox.appendChild(this._createElement(Language.get('wcf.js.teamSpeakViewer.versionTitle'), Language.get('wcf.js.teamSpeakViewer.versionContent', {version: data.client_version, platform: data.client_platform})));

            // Online seit
            infoBox.appendChild(this._createElement(Language.get('wcf.js.teamSpeakViewer.onlineSinceTitle'), DateUtil.getTimeElement(new Date(data.connection_connected_time))));

            // Beschreibung
            if (data.client_description != null) {
                infoBox.appendChild(this._createElement(Language.get('wcf.js.teamSpeakViewer.descriptionTitle'), data.client_description));
            }

            // Servergruppen
            infoBox.appendChild(this._createElement(Language.get('wcf.js.teamSpeakViewer.servergroupsTitle'), this._createGroupElement(data.client_servergroups)));

            // Channelgruppen
            infoBox.appendChild(this._createElement(Language.get('wcf.js.teamSpeakViewer.channelgroupTitle'), this._createGroupElement(data.client_channel_group_id)));

            // avatar
            if (data.avatar) {
                var avatarImg = document.createElement('img');
                avatarImg.setAttribute('src', WCF_PATH + 'images/teamspeak_viewer/avatar/avatar_' + data.client_base64HashClientUID + '.png');
                infoBox.appendChild(this._createElement(Language.get('wcf.js.teamSpeakViewer.avatarTitle'), avatarImg));
            }
        },

        _showChannelInfos: function(data) {
            var infoBox = document.getElementById('TeamSpeakServerInfo');
            infoBox.innerHTML = '';

            // Name
            var sectionTitle = document.createElement('h2');
            sectionTitle.classList.add('sectionTitle');
            sectionTitle.innerText = data.channel_name;
            infoBox.appendChild(sectionTitle);

            // Channel Topic
            if (data.channel_topic != null) {
                infoBox.appendChild(this._createElement(Language.get('wcf.js.teamSpeakViewer.channelTopicTitle'), data.channel_topic));
            }

            // Audio Codec
            var codec = Language.get('wcf.js.teamSpeakViewer.codecUnknown');
            if (data.channel_codec >= 0 && data.channel_codec <= 5) {
                codec = Language.get('wcf.js.teamSpeakViewer.codec' + data.channel_codec);
            }
            infoBox.appendChild(this._createElement(Language.get('wcf.js.teamSpeakViewer.codecTitle'), codec));

            // Eigenschaften
            var property = '';
            if (data.channel_flag_permanent == 1) {
                property = Language.get('wcf.js.teamSpeakViewer.flag_permanent');
            } else if (data.channel_flag_semi_permanent == 1) {
                property = Language.get('wcf.js.teamSpeakViewer.flag_semi_permanent');
            } else {
                property = Language.get('wcf.js.teamSpeakViewer.flag_temporary');
            }
            if (data.channel_flag_default == 1) {
                property = property + ', ' + Language.get('wcf.js.teamSpeakViewer.flag_default');
            } else if (data.channel_flag_password == 1) {
                property = property + ', ' + Language.get('wcf.js.teamSpeakViewer.flag_password');
            }
            infoBox.appendChild(this._createElement(Language.get('wcf.js.teamSpeakViewer.settingsTitle'), property));

            // Aktuelle Clients
            var maxclients = data.channel_maxclients;
            if (data.channel_maxclients == -1) {
                maxclients = Language.get('wcf.js.teamSpeakViewer.unlimited');
            }
            infoBox.appendChild(this._createElement(Language.get('wcf.js.teamSpeakViewer.actualClientsTitle'), data.total_clients + ' / ' + maxclients));

            // Moderiert
            if (data.channel_needed_talk_power > 0) {
                infoBox.appendChild(this._createElement(Language.get('wcf.js.teamSpeakViewer.moderatedTitle'), Language.get('wcf.js.teamSpeakViewer.yes')));
            }

            // Beschreibung
            if (data.channel_description != null) {
                var descriptionDev = document.createElement('div');
                descriptionDev.innerHTML = data.channel_description;
                infoBox.appendChild(this._createElement(Language.get('wcf.js.teamSpeakViewer.descriptionTitle'), descriptionDev));
            }
        },

        _showServerInfos: function(data) {
            var infoBox = document.getElementById('TeamSpeakServerInfo');
            infoBox.innerHTML = '';

            // Name
            var sectionTitle = document.createElement('h2');
            sectionTitle.classList.add('sectionTitle');
            sectionTitle.innerText = data.virtualserver_name;
            infoBox.appendChild(sectionTitle);

            // Hostbanner
            if (data.virtualserver_hostbanner_gfx_url != undefined) {
                if (data.virtualserver_hostbanner_url != undefined) {
                    var hostbannerLink = document.createElement('a');
                    hostbannerLink.setAttribute('href', data.virtualserver_hostbanner_url);
                    var hostbannerImg = document.createElement('img');
                    hostbannerImg.setAttribute('src', data.virtualserver_hostbanner_gfx_url);
                    hostbannerImg.setAttribute('id', 'HostBanner');
                    hostbannerLink.appendChild(hostbannerImg);
                    infoBox.appendChild(this._createElement(Language.get('wcf.js.teamSpeakViewer.hostbannerTitle'), hostbannerLink));
                } else {
                    var hostbannerImg = document.createElement('img');
                    hostbannerImg.setAttribute('src', data.virtualserver_hostbanner_gfx_url);
                    hostbannerImg.setAttribute('id', 'HostBanner');
                    infoBox.appendChild(this._createElement(Language.get('wcf.js.teamSpeakViewer.hostbannerTitle'), hostbannerImg));
                }
            }

            if (this._showData) {
                // Adresse
                var address = data.hostname;
                if (data.port != 9987) {
                    address = address + ':' + data.port;
                }
                infoBox.appendChild(this._createElement(Language.get('wcf.js.teamSpeakViewer.addressTitle'), address));

                // Passwort
                if (data.virtualserver_flag_password && this._showPassword && this._serverPassword != '') {
                    infoBox.appendChild(this._createElement(Language.get('wcf.js.teamSpeakViewer.passwordTitle'), this._serverPassword));
                }
            }

            // Version
            infoBox.appendChild(this._createElement(Language.get('wcf.js.teamSpeakViewer.versionTitle'), data.virtualserver_version + ' on ' + data.virtualserver_platform));

            // Online seit
            infoBox.appendChild(this._createElement(Language.get('wcf.js.teamSpeakViewer.onlineSinceTitle'), DateUtil.getTimeElement(new Date((TIME_NOW - data.virtualserver_uptime) * 1000))));

            // Aktuelle Clients
            // TODO reserved
            infoBox.appendChild(this._createElement(Language.get('wcf.js.teamSpeakViewer.actualClientsTitle'), data.virtualserver_clientsonline + ' / ' + data.virtualserver_maxclients));

            // Aktuelle Channel
            infoBox.appendChild(this._createElement(Language.get('wcf.js.teamSpeakViewer.actualChannelsTitle'), data.virtualserver_channelsonline));
        },

        _createGroupElement: function(groupData) {
            var groupList = document.createElement('ul');
            for (var i = 0; i < groupData.length; i++) {
                var group = groupData[i];
                var groupEntry = document.createElement('li');
                groupEntry.classList.add('groupWrapper');

                // Icon
                var groupIconDiv = document.createElement('div');
                groupIconDiv.classList.add('channelSubscription');
                if (group.iconid != null) {
                    var groupIcon = document.createElement('img');
                    groupIcon.setAttribute('src', WCF_PATH + 'images/teamspeak_viewer/' + group.iconid);
                    groupIconDiv.appendChild(groupIcon);
                }
                groupEntry.appendChild(groupIconDiv);

                // Gruppenname
                var groupNameDiv = document.createElement('div');
                groupNameDiv.classList.add('channelName');
                groupNameDiv.innerText = group.name;
                groupEntry.appendChild(groupNameDiv);

                groupList.appendChild(groupEntry);
            }
            return groupList;
        },

        _createElement: function(name, content) {
            var wrapper = document.createElement('dl');
            var desc = document.createElement('dt');
            desc.innerText = name;
            wrapper.appendChild(desc);
            var contentDD = document.createElement('dd');
            if (typeof content === 'string') {
                contentDD.innerText = content;
            } else {
                contentDD.appendChild(content);
            }
            wrapper.appendChild(contentDD);
            return wrapper;
        },

        _ajaxSetup: function() {
            return {
				data: {
					className: 'wcf\\data\\teamspeak\\viewer\\TeamspeakViewerAction'
				},
				silent: false
			};
        },

        _ajaxSuccess: function(data) {
            if (data.returnValues.type == 'client') {
                this._showClientInfos(data.returnValues.data);
            } else if (data.returnValues.type == 'channel') {
                this._showChannelInfos(data.returnValues.data);
            } else if (data.returnValues.type == 'server') {
                this._showServerInfos(data.returnValues.data);
            }
        },

        _ajaxFailure: function() {
        }
    }

    return TeamSpeakViewer;
});