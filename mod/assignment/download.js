YUI().use("node", function (Y) {

		var text = Y.one("#download_ass");
 
		var helloWorld = function (e) {
			out_form.setStyle('display', '');
		};
		
		text.on("click", helloWorld);
		
		var out_form = Y.one("#download_form");
});







