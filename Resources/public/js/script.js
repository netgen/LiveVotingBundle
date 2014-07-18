$(document).ready(function(){
	$(".welcomeScreen").show();
	$(".activeScreen").hide();
	$(".endScreen").hide();
	$("#footer").hide();

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
	    		$(".welcomeScreen").hide();
	    		$(".activeScreen").show();
	    		active ();
	    	}
	    	else if (state == "POST"){
	    		end();
	    	}
	    });
	}
	checkPresentationStart ();


	var active = function() {
		$.getJSON(urlPath, function(data){
			if (data["eventStatus"] == "POST"){
				footer("timer",data["seconds"]*1000);
			}
			else {
				//check votingStatus, iterate through JSON
				$.each(data, function(mainKey,mainValue){
					if ($.isArray(mainValue)){
						//iterate through array
						$.each(mainValue, function(k,v){
							if(v["votingEnabled"]){
								//enable voting for v["presentationId"] (changing screen state)
								console.log("vote");
							}
							else {
								//disable voting for v["presentationId"] (changing screen state)
							}
						});
					}
				});
				setTimeout(active, 5000);
			}

		});
	};

	var end = function() {
		$(".welcomeScreen").hide();
		$(".activeScreen").hide();
		$(".endScreen").show();
	}

	var sendVote = function(presId, myRate){
		$.post("/dest.php", {id:presId, rate: myRate  }, function(status){
			if(status["error"]){
				console.log(status["error"]);
			}
		});
	}
	var res;
	var changeTime = function (time1){
		var timerId = setInterval(function() {
			time1 = time1 - 1000;
			res = time1 / 1000;
			res =String(res);
			document.getElementById("timer").innerHTML = res;
			if (time1 == 0) {
				clearInterval(timerId);
				end();
			}
		    console.log(time1);
		}, 1000);
	}

	var footer = function (event,param){

		//first disable the rest of the screen
		//determine who called footer
		if (event == "timer"){
			var timeRemaining = param;
			console.log(event);
			changeTime(timeRemaining);
			$("#footer").show();

		}
		else { //odredi error
			var error = param;
		}
	}
});



/*
1) footer u slucaju greske
2) footer za timer
u oba slucaja glasanje onemoguceno
*/