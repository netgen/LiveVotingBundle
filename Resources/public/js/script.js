function brain(options_){

    var options = options_;
    var source   = $("#presentation").html();
    var template = Handlebars.compile(source);
    var urlPath = options['URLS']['EVENT_STATUS']+getEventId(1);
    var globalState = null;
    var timeout = options['STATES']['PRE']['TIMEOUT'];
    var timer = new timer();
    var canIVote = true;
    var presentations = new presentationsArray();

    Handlebars.registerHelper ('ifCond', function(v1, v2, options) {
        if (v1 == v2) {
            return options.fn(this);
        }
        return options.inverse(this);
    });

    $("#footer").hide();

    var spinner = new Spinner();
    spinner.spin();

    document.getElementById('welcome').appendChild(spinner.el);

    $('body').on('change', '.forma', function(e){
        if(!canIVote)return;
        var presentation_id = $(this).attr('action').split('/').pop();
        var presentation = presentations.getById(presentation_id);

        var rate = $(this).serialize();
        if(presentation.getData()['votingEnabled']==true){
            $.post($(this).attr('action'), rate, function(data){
                presentation.setCheckMark(true);
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
                        timer.init(parseInt(data['seconds']), changeFooter, endVoting);
                        timer.runTimer();
                    }else{
                        timeout = -1;
                        endVoting();
                    }
                case 'ACTIVE':
                    $("#welcome").hide();
                    spinner.stop();
                    //add presentations
                    handleNewPresentations(data['presentations']);
                    break;
                default:
                    timeout = -1;

            }
            globalState = state;
            if(timeout>0) setTimeout(run, timeout);
        }); 
    }

    function handleNewPresentations(data){
        var pres; // single presentations
        for(var i in data){
            pres = data[i];
            presentations.add(pres);
        }
    }

    function endVoting(){
        //Thank u for voting
        canIVote = false;
        $(".forma input").prop("disabled", true);
        $("#footer").html("Voting is now closed.");
        presentations.setEnabledAll(false);
    }

    function changeFooter(seconds_) {
        $("#footer").show();
        $("#timer").html(seconds_);
    }

    /*
    Class timer
     */
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

    /*
    Class presentation that holds one presentation and pointer to html element
     */
    function presentationClass(data_){

        var data = data_;
        this.element = $(template(data));
        $("#voting").append(this.element);
        this.element.find('.check').hide();

        this.setData = function(data_){
            data = data_;
        }

        this.getData = function(){
            return data;
        }
        this.setVote = function(vote_number){
            this.element.find('input[type=radio]').each(function(){
                if(this.value == vote_number){
                    this.setAttribute('checked', 'checked');
                }
            });
        }


        this.setEnabled = function(enabled_status){
            this.element.find('input[type=radio]').each(function(){

                if(!enabled_status || canIVote==false){
                    this.setAttribute('disabled', 'disabled');
                }else{
                    this.removeAttribute('disabled');
                }
            });
        }

        this.setCheckMark = function(check_mark){
            this.element.find('.check').each(function(){
                if(check_mark){
                    $(this).show();
                }else{
                    $(this).hide();
                }
            })
        }

        this.handle = function(){
            var vote = data['presenterRate'];
            this.setVote(vote);
            this.setEnabled(data['votingEnabled']);
            this.setCheckMark(vote>0);
        }
    }

    function presentationsArray(){
        var arr = {};

        /*
        Adds new presentation if it's not there and change it's view if needs.
         */
        this.add = function(presentation){
            var id = presentation['presentationId'].toString();
            if(arr[id] === undefined){
                arr[id] = new presentationClass(presentation);
            }
            arr[id].setData(presentation);
            arr[id].handle();
        }

        this.get = function(presentation){

        }

        this.getById = function(id){
            return arr[id];
        }

        this.setEnabledAll = function(state){
            for(var i in arr){
                arr[i].setEnabled(state);
            }
        }
    }

    run();

}

