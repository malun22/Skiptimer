Skiptimer plugin for TrackMania Forever xaseco
==============================================

This plugin provides the /skiptimer chat command, allowing players with the
appropriate privilege level to creat a timer to skip the current map.

Installation: copy chat.skiptimer.php to xaseco's plugins folder.
Enable the plugin in xaseco's plugins.xml file by putting <plugin>chat.skiptimer.php</plugin>

You can change the degree of permissions needed for the chat command by editing
the plugin with a texteditor. For that edit the value in line 46 for $st_perm.
[3]: Only Masteradmin, [2]: Masteradmin and Admin, [1]: Masteradmin, Admin and Operator, [0]: No permissions required

How to use:
/skiptimer <Amount> <sec/min/h> - to set a skiptimer
/skiptimer cancel - to cancel the current skiptimer

Example usage:
/skiptimer 10 min - Sets the skiptimer to 10 minutes
/skiptimer 30 sec - Sets the skiptimer to 30 seconds
/skiptimer 1 h - Sets the skiptimer to 1 hour
