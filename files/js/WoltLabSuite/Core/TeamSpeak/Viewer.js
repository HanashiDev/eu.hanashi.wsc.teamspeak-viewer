define(['Ajax'], function(Ajax) {
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
            console.log(data);
        },

        _ajaxFailure: function() {
            console.log('fehler');
        }
    }

    return TeamSpeakViewer;
});