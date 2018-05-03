var main = {
    dom: {
        links: {
            cont: 'section'
        }
    },
    settings: {
        inputs_tag: 'quitz_answers'
    },
    blocks: null,

    init: function (blocks) {
        this.blocks = blocks || [];
        var cont = document.querySelector(this.dom.links.cont);
        cont.appendChild(this._getBlockStartView(this.blocks[0]));

        return true;
    },

    _getBlockStartView: function (block) {
        var self = this;
        var cont = document.createElement('div');
        cont.className = 'start_block';
        cont.innerHTML = '<h2>Блок №' + (this.blocks.indexOf(block) + 1) + '</h2>';

        var button = document.createElement('button');
        button.innerHTML = 'Начать';
        button.onclick = function () {
            self.showQuestion(block, 0);
        };

        cont.appendChild(button);

        return cont;
    },

    showQuestion: function (block, num) {
        var cont = document.querySelector(this.dom.links.cont);
        var elem = cont.querySelector('.start_block');
        if (elem) {
            cont.removeChild(elem);
            cont.innerHTML = this._getBaseQuestionView(block);
        }
        elem = document.querySelector('.question_cont');
        if (elem) {
            elem.parentNode.removeChild(elem);
        }

        cont.appendChild(this._getQuestionView(block, block.questions[num]));
        this.updateProgress(num, block.questions.length);
    },

    _getBaseQuestionView: function (block) {
        var str = '<h3>' + block.title + '</h3>';
        // progress bar
        str += '<div class="question_counter">';
        str += '<div class="progress_count">';
        str += 'Вопрос <b>1</b> из <b>' + block.questions.length + '</b>';
        str += '</div>';
        str += '<div class="progress_bar">';
        str += '<div style="width: 0%"></div>';
        str += '</div>';
        str += '</div>';

        return str;
    },

    _getQuestionView: function (block, question) {
        var cont = document.createElement('div');
        cont.className = 'question_cont';
        var str = '<h4>' + question.title + '</h4><p>' + question.descr + '</p><font>Выберите один или несколько ответов:</font>';

        str += '<ul>';
        for (var x = 0; x < question.answers.length; x++) {
            str += '<li><label><input name="' + this.settings.inputs_tag + '" type="checkbox" value="' + x + '"><font>' + question.answers[x] + '</font></label></li>';
        }
        str += '</ul>';

        cont.innerHTML = str;

        var self = this;
        var but = document.createElement('button');
        but.innerHTML = 'Далее';
        but.onclick = function () {
            self.nextAction(block, question);
        };

        cont.appendChild(but);

        return cont;
    },

    nextAction: function (block, question) {
        // send user answer to server

        question.user_answers = this._getUserAnswers();
        if (!question.user_answers.length) {
            // User did not choose any option
            return false;
        }
        var next = block.questions.indexOf(question) + 1;
        if (!block.questions[next]) {
            // end block
            this.funishBlock(block);
            return true;
        }

        this.showQuestion(block, next);

    },

    _getUserAnswers: function () {
        var result = [];
        document.querySelectorAll('input[name=' + this.settings.inputs_tag + ']:checked').forEach(function (e) {
            result.push(e.value);
        });

        return result;
    },

    updateProgress: function (curr, total) {
        var counter = document.querySelector('.progress_count > b');
        if (counter) {
            counter.innerHTML = curr + 1;
        }

        var bar = document.querySelector('.progress_bar div');
        if (bar) {
            bar.setAttribute('style', 'width: ' + (curr / total) * 100 + '%');
        }
    },

    funishBlock(block) {
        var bar = document.querySelector('.progress_bar div');
        if (bar) {
            bar.setAttribute('style', 'width: 100%');
        }

        var elem = document.querySelector('.question_cont');
        if (elem) {
            elem.parentNode.removeChild(elem);
        }

        var cont = document.querySelector('section');
        cont.appendChild(this._getFinishBlockView(block));
    },

    _getFinishBlockView: function (block) {
        var cont = document.createElement('div');
        cont.innerHTML = '<p>Спасибо! Вы успешно завершили блок вопросов "' + block.title + '"</p>';

        return cont;
    },

    send: function (params) {
        var self = this;
        var xhr = new XMLHttpRequest();
        xhr.open(params.method, params.url, true);
        xhr.onload = xhr.onerror = function () {
            //console.log(this.responseType);
            //console.log(this.getResponseHeader('content-type'));
            if (Number(this.status) === 200) {
                var data = JSON.parse(this.responseText);
                if (data[0] === false) {
                    alert('error answer');
                    return false;
                }
            } else {
                console.log("error " + this.status);
                alert('error request: ' + this.status);
                return false;
            }

            if (params.func) {
                params.func(data, params);
            } else {
                self.render(data, params.key);
            }
        };
        xhr.send(params.form);
    }
};

window.onload = function () {
    main.init(issues);
};