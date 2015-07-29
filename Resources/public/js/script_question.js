function brain(options_){

    var options = options_;
    var source_yes_no = $("#question").html();
    var source_1_5 = $("#vote").html();
    var template_yes_no = Handlebars.compile(source_yes_no);
    var template_1_5 = Handlebars.compile(source_1_5);
    var urlPath = options['URLS']['EVENT_STATUS'] + getEventId(1);
    var globalState = null;
    var canIAnswer = true; //voting enabled on questions?
    var questions = new questionsArray();
    var shadow = $('<div id="shadow"></div>');
    var loader = $('#circleG');
    var footer = new footerClass($('#footer'));
    var container = $("#answerScreen");

    shadow.hide();
    Handlebars.registerHelper ('ifCond', function(v1, v2, options) {
        if (v1 == v2) {
            return options.fn(this);
        }
        return options.inverse(this);
    });


    container.append(shadow);

    showSpinner();
    container.on('change', '.question', function(e){e.preventDefault();});


    container.on('click', '.question', function(e){
        e.preventDefault();
        if(!canIAnswer) return;
        var action = $(this).closest("form").attr('action');
        var question_id = action.split('/').pop();
        var question = questions.getById(question_id);
        var old_answer = question.getAnswer();
        var answer = $(this).attr('value');
        var rate = 'rate='+answer;
        question.setAnswer(answer);
        $.ajax({
            type: 'post',
            'url': action,
            'data': rate,
            success: function(data){
                question.highlightMe();
                showFooter();
                footer.displayMessage(data['errorMessage']);
                hideSpinner();
                setTimeout(hideFooter, 2000);
            },
            error: function(e){
                question.setAnswer(old_answer);
                //fly out erro on footer
                hideSpinner();
            }
        });
    });


    /**
     *  Gets event id from url which looks like:
     *  /question/{id}
     */
    function getEventId(ret){
        var struct = window.location.pathname.split('/');
        // 2 because '/'.split('/') returns array len 2
        if(struct.length<=2)return ret.toString();
        return struct.pop();
    }

    var run = function() {
        $.getJSON(urlPath, function(data){

            var state = data["questionStatus"];

            switch(state){
                case false:
                    endAnswering();
                    footer.displayMessage(data['errorMessage']);
                    break;
                case true:
                    startAnswering();
                    footer.displayMessage(data['errorMessage']);
                    hideSpinner();
                    handleNewQuestions(data['questions']);
                    break;
            }
            globalState = state;
            setTimeout(run, 5000);
        }); 
    }

    function handleNewQuestions(data){
        var ques;
        for(var i in data){
            ques = data[i];
            questions.add(ques);
        }
    }

    function endAnswering(message){
        //Thank u for answering
        canIAnswer = false;

        questions.setEnabledAll(false);
        footer.staticMessage(message);
    }

    function startAnswering(message){
        //Thank u for answering
        canIAnswer = true;

        questions.setEnabledAll(true);
        footer.staticMessage(message);
    }

    function hideFooter(){
        $('#footer').hide();
    }

    function showFooter(){
        $('#footer').show();
    }

    /*
    Class question that holds one question and pointer to html element
     */
    function questionClass(){

        var data = null;
        var that = this;
        this.element = null;

        this.init = function(newData){
            this.setData(newData);
            if(data.question_type == 1) 
                this.element = $(template_yes_no(data));
            else 
                this.element = $(template_1_5(data));

            //if(canIAnswer == false){
                this.element.find('.highLight').hide();
                this.element.find('.flash').hide();
                $("#answerScreen").append(this.element);
                this.element.find('.check').hide();
            //}
        }

        /*
        Returns true if user can now answer on it.
         */
        this.setData = function(newData){
            var status = false;
            if(data == null){
                status = newData['votingEnabled'];
            }else if(newData['votingEnabled'] == true && data['votingEnabled'] == false){
                status = true;
            }
            delete data;
            data = newData;
            return status;
        };

        this.getData = function(){
            return data;
        };

        this.setAnswer = function(answer_number){
            this.element.find('input').each(function(){
                if(this.value == answer_number){
                    $(this).addClass('active');
                }else{
                    $(this).removeClass('active');
                }
            });
            this.element.find('button').each(function(){
                if(this.value == answer_number){
                    $(this).addClass('active');
                }else{
                    $(this).removeClass('active');

                }
            });
        };

        this.getAnswer = function () {
            return this.element.find('.active').first().val();
        };

        this.hideAnswer = function (answer_number) {
            this.element.find('input').each(function(){
                $(this).removeClass('active');
            });
            this.element.find('button').each(function(){
                $(this).removeClass('active');
            })
        };


        this.setEnabled = function(enabled_status){
            if(!canIAnswer) // enabled_status == false
                this.element.find('.highLight').fadeIn(2000);
            else
                this.element.find('.highLight').fadeOut(1000);
        }

        this.highlightMe = function(){
            this.setEnabled(true);
        }

        this.handle = function(){
            var answer = data['answer'];
            this.setAnswer(answer);
            this.setEnabled(true);
            this.setEnabled(canIAnswer);
        }
    }

    function questionsArray(){
        var arr = {};
        var notify = [];
        /*
        Adds new question if it's not there and change it's view if needs.
         */
        this.add = function(question){
            var id = question['questionId'].toString();
            if(arr[id] === undefined){
                arr[id] = new questionClass();
                arr[id].init(question);
            }
            var status = arr[id].setData(question);
            if(status){
                notify.push(id);
            }
            arr[id].handle();
        }


        this.getById = function(id){
            return arr[id];
        }

        this.setEnabledAll = function(state){
            for(var i in arr){
                arr[i].setEnabled(state);
                arr[i].setAnswer(arr[i].getData()['answer']);
            }
        }
    }

    function footerClass(el){
        var element=el;
        var holdingUp = false;
        var that = this;
        element.hide();

        this.displayMessage = function(message){
            setMessage(message);
            this.anim(3);

        }

        function setMessage(message){
            var er = element.find('.error');
            er.html(message);
        }

        function setTimer(tmrMsg){
            //
        }
        this.anim = function(seconds){
            if(!holdingUp)
                this.animateUp(100);
        }

        function endFooter(){
            setMessage('');
        }

        this.animateUp = function(value){
            element.show();
            element.animate({
                bottom: value+'px'
            }, 500);
        }

        this.animateDown = function(value){
            element.animate({
                bottom: -value+'px'
            }, 500, function(){endFooter();});
            element.show();
        }

        this.staticMessage = function(msg){
            setMessage(msg);
            holdOn();
        }

        function holdOn(){
            if(holdingUp)return;
            holdingUp = true;
            that.animateUp(100);

        }

        this.removeStatic = function(){
            if(!holdingUp)return;
            holdingUp = false;
            this.animateDown(100);
            endFooter();
        }

    }

    function showSpinner(){
        shadow.show();
        loader.show();
    }
    function hideSpinner(){
        shadow.hide();
        loader.hide();
    }



    run();

}

