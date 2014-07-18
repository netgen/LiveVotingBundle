$(document).ready(function(){
	$("#footer").hide();
	$("#timer").hide();
	$("#presentationId0").hide();

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

    var addPresentation = function (newId) {
		var div = document.getElementById('presentationId0'),
	    clone = div.cloneNode(true); // true means clone all childNodes and all event handlers
		clone.id = "presentationId"+newId;
		document.body.appendChild(clone);
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

	    		$("#presentationId0").show();
				$.each(data, function(mainKey,mainValue){
					if ($.isArray(mainValue)){
						//iterate through array
						$.each(mainValue, function(k,v){
							addPresentation(v["presentationId"]);
						});
					}
				});
				$("#presentationId0").hide();
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
				//temp
				end();
				//temp
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
		console.log("end");
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