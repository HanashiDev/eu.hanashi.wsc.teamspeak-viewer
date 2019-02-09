define(['DateUtil','Ajax'], function(DateUtil, Ajax) {
    "use strict";

    function TeamSpeakViewer() {
        this._init();
    }
    TeamSpeakViewer.prototype = {
        _init: function() {
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
            infoBox.appendChild(this._createElement('Version:', data.client_version + ' auf ' + data.client_platform));

            // Online seit
            infoBox.appendChild(this._createElement('Online seit:', DateUtil.getTimeElement(new Date(data.connection_connected_time))));

            // Beschreibung
            if (data.client_description != null) {
                infoBox.appendChild(this._createElement('Beschreibung:', data.client_description));
            }

            // Servergruppen
            infoBox.appendChild(this._createElement('Server Gruppen:', this._createGroupElement(data.client_servergroups)));

            // Channelgruppen
            infoBox.appendChild(this._createElement('Channel Gruppe:', this._createGroupElement(data.client_channel_group_id)));

            // avatar
            if (data.avatar) {
                var avatarImg = document.createElement('img');
                avatarImg.setAttribute('src', WCF_PATH + 'images/teamspeak_viewer/avatar/avatar_' + data.client_base64HashClientUID + '.png');
                infoBox.appendChild(this._createElement('Avatar:', avatarImg));
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
                infoBox.appendChild(this._createElement('Channel Topic:', data.channel_topic));
            }

            // Audio Codec
            // TODO: Zahl auftrennen
            infoBox.appendChild(this._createElement('Audio Codec:', data.channel_codec));

            // Eigenschaften
            var property = '';
            if (data.channel_flag_permanent == 1) {
                property = 'Permanent';
            } else if (data.channel_flag_semi_permanent == 1) {
                property = 'Semi-Permanent';
            } else {
                property = 'Temporär';
            }
            if (data.channel_flag_default == 1) {
                property = property + ', Standard';
            } else if (data.channel_flag_password == 1) {
                property = property + ', Passwort';
            }
            infoBox.appendChild(this._createElement('Eigenschaften:', property));

            // Aktuelle Clients
            // TODO

            // Moderiert
            if (data.channel_needed_talk_power > 0) {
                infoBox.appendChild(this._createElement('Moderiert:', 'Ja'));
            }

            // Beschreibung
            if (data.channel_description != null) {
                infoBox.appendChild(this._createElement('Channel Topic:', data.channel_description));
            }
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
            // this._showMessages(data.returnValues);
            if (data.returnValues.type == 'client') {
                this._showClientInfos(data.returnValues.data);
            } else if (data.returnValues.type == 'channel') {
                this._showChannelInfos(data.returnValues.data);
            } else {
                console.log(data.returnValues);
            }
        },

        _ajaxFailure: function() {
            console.log('fehler');
        }
    }

    return TeamSpeakViewer;
});