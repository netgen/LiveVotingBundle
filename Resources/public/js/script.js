

$(document).ready(function(){

	var source   = $("#presentation").html();
	var template = Handlebars.compile(source);

	$("#footer").hide();
	$("#timer").hide();

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

    var addPresentations = function (newPresentations) {
		var html = template({presentation: newPresentations});
		document.body.innerHTML += html;
		}	

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
				addPresentations(data["presentations"]);
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
				//check votingStatus, iterate through JSON
				
				var presentations = data["presentations"];
				for (var i in presentations){
					console.log(presentations[i]);
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



/*
1) footer u slucaju greske
2) footer za timer
u oba slucaja glasanje onemoguceno
*/