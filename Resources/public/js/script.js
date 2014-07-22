function brain(options_){

    var options = options_;
    var rate = $(this).serialize();
    var source   = $("#presentation").html();
    var template = Handlebars.compile(source);
    var urlPath = options['URLS']['EVENT_STATUS']+getEventId(1);
    var globalState = null;
    var timeout = options['STATES']['PRE']['TIMEOUT'];
    var presentations = [];
    var timer = new timer();
    var canIVote = true;


    $("#footer").hide();
    $("#timer").hide();

    var spinner = new Spinner();
    spinner.spin();

    document.getElementById('welcome').appendChild(spinner.el);

    $('body').on('change', '.forma', function(e){
        if(!canIVote)return;
        var presentation_id = $(this).attr('action').split('/').pop();
        var presentation = null;

        for(var i in presentations){
            if(presentations[i]['presentationId']==presentation_id){
                presentation=presentations[i];
                break;
            }
        }
        var rate = $(this).serialize();
        if(presentation['votingEnabled']==true){
            $.post($(this).attr('action'), rate, function(data){
                console.log(data);
            });
        }
        e.preventDefault();
    });


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

    var run = function() {
        $.getJSON(urlPath, function(data){

            if(data["error"]){
                console.log(data["errorMessage"]);
            }
            console.log(data);
            var state = data["eventStatus"];
            timeout = parseInt(options['STATES'][state]['TIMEOUT'])*1000;
            switch(state){
                case 'PRE':
                   break;
                case 'POST':
                    var seconds = parseInt(data['seconds']);
                    if(!timer.isRunning && seconds>0){
                        timer.init(parseInt(data['seconds']), function(s){alert('sek '+ s);}, endVoting);
                        timer.runTimer();

                    }
                case 'ACTIVE':
                    $("#welcome").hide();
                    spinner.stop();
                    //add presentations
                    $("#voting").html(template(data));
                    presentations = data['presentations'];
                    break;

            }
            globalState = state;
            if(timeout>0) setTimeout(run, timeout);
        });
    }
    
    function endVoting(){
        canIVote = false;
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

    function timer(){
        this.isRunning = false;
        this.seconds = 1;
        this.callbackEverySecond;
        this.callbackEnd;

        this.init = function(seconds_, callbackEverySecond_, callbackEnd_){
            this.seconds = seconds_;
            this.callbackEverySecond = callbackEverySecond_;
            this.callbackEnd = callbackEnd_;
        }

        this.runTimer = function(){
            var that = this;
            this.isRunning = true;
            if(this.seconds==0){
                this.callbackEnd();
                this.isRunning = false;
                return;
            }
            this.callbackEverySecond(this.seconds--);
            setTimeout(function(){that.runTimer()}, 1000);
        }
    }

    run();


}

