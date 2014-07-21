

$(document).ready(function(){

	var source   = $("#presentation").html();
	var template = Handlebars.compile(source);

	$("#footer").hide();
	$("#timer").hide();

	var spinner = new Spinner();
	spinner.spin();
	document.getElementById('welcome').appendChild(spinner.el);

    /**
     *  Gets event id from url which looks like:
     *  /event/{id}
     */

    function getEventId(ret){
        var struct = window.location.pathname.split('/');

        // 2 because '/'.split('/') returns array len 2
        if(struct.length<=2)return ret.toString();

        return struct.pop();
    }

    var urlPath = '/event_status/'+getEventId(1);

	var checkPresentationStart = function() {
	    $.getJSON(urlPath, function(data){
	    	if (data["error"]){
	    		console.log(data["errorMessage"]);
	    	}
	    	var state = data["eventStatus"];

	    	if (state == "PRE"){
	    	    setTimeout(checkPresentationStart, 1000);
	    	}
	    	else if (state == "ACTIVE"){
	    		$("#welcome").hide();
				spinner.stop();

	    		//add presentations
	    		$("#voting").append (template (data));
	    		active ();
	    	}
	    	else if (state == "POST"){
	    		end();
	    	}
	    });
	}
	
	var active = function() {
		$.getJSON(urlPath, function(data){
			if (data["eventStatus"] == "POST"){
				footer("timer",data["seconds"]);
			}
			else {
				//check voting status
				var presentations = data["presentations"];
				for (var i in presentations){
					if (presentations[i]["votingEnabled"]){
						presentationId = presentations[i]["presentationId"]
						console.log("true");
						$( "#1" ).on( "click", function() {
							url = "/vote/";
							url += presentationId;
							console.log(url);
							$.post( url, { rate: 1})
								.done(function( data ) {
								alert( "Data Loaded: " + data );
							});				
						});
						$( "#2" ).on( "click", function() {
						});
						$( "#3" ).on( "click", function() {
						
						});
						$( "#4" ).on( "click", function() {
						
						});
						$( "#5" ).on( "click", function() {
						
						});						
					}

				}
				
				setTimeout(active, 5000);
			}

		});
	};

	var end = function() {
		console.log("end");
	}



	var changeTime = function (time1){
		var timerId = setInterval(function() {
			document.getElementById("timer").innerHTML = String(--time1);
			if (time1 == 0) {
				clearInterval(timerId);
				end();
			}
		    console.log(time1);
		}, 1000);
	}

	var footer = function (event,param){
		$("#footer").show();
		//first disable the rest of the screen
		//determine who called footer
		if (event == "timer"){
			var timeRemaining = param;
			$("#timer").show();
			changeTime(timeRemaining);
			
		}
		else { //determine error
			var error = param;
		}
	}

	checkPresentationStart ();
});
