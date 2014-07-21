$(document).ready(function(){
	$(".welcomeScreen").show();
	$(".activeScreen").hide();
	$(".endScreen").hide();


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
                console.log('usao u post');
	    		end();
	    	}
	    });
	}
	checkPresentationStart ();

	var active = function() {
		$.getJSON(urlPath, function(data){
			if (data["eventStatus"] == "POST"){
				setTimeout(end,(1)*1000);
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

	var sendVote = function(presId, myRate){
		$.post("/dest.php", {id:presId, rate: myRate  }, function(status){
			if(status["error"]){
				console.log(status["error"]);
			}
		});
	}

	var end = function() {
		$(".welcomeScreen").hide();
		$(".activeScreen").hide();
		$(".endScreen").show();
		console.log("end");
	}

});