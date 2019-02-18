@ECHO OFF
del files.tar
7z a -ttar -mx=9 files.tar .\files\*
del templates.tar
7z a -ttar -mx=9 templates.tar .\templates\*
del eu.hanashi.wsc.teamspeak-viewer.tar
7z a -ttar -mx=9 eu.hanashi.wsc.teamspeak-viewer.tar .\* -x!acptemplates -x!files -x!templates -x!eu.hanashi.wsc.teamspeak-viewer.tar -x!.git -x!.gitignore -x!make.bat -x!make.sh
