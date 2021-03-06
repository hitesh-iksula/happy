Event.observe(window, 'load', function(){
    window.amImportObj = new amImport();
});

var amImport = new Class.create();

amImport.prototype = {
    initialize: function(){

    },
    error: function(error, processer){
//        console.log('error: ', error);

        if (processer)
            $(processer.parentNode).remove();

    },
    tracePosition: function(position, processer){

        processer.setStyle({
            'width': position + '%'
        });

        processer.down('span').innerHTML = '&nbsp;' + position + '%';

//      console.log('position:', position, '%');
    },
    done: function(response, processer){
//        console.log('done');

        if (processer)
            $(processer.parentNode).remove();

        if (response.full_import_done == 1){
            location.reload();
        }
    },
    start: function(response, input){
        var container = new Element('div');
        var processer = new Element('div');
        var position = new Element('span');

        processer.addClassName('am_processer');
        container.addClassName('am_processer_container');

        processer.setStyle({
            'width': '0%'
        });

        container.appendChild(processer);

        input.parentNode.appendChild(container);

        processer.innerHTML = response.file;
        processer.appendChild(position);

        return processer;

    },
    commit: function(commitUrl, processer){
        var _caller = this;

        var request = new Ajax.Request(
            commitUrl,
            {
                method: 'post',
                onSuccess: function(transport){
                    var response = eval('(' + transport.responseText + ')');

                    if (response.status == 'done'){
                        _caller.done(response, processer)
                    } else if (response.error){
                        _caller.error(response.error, processer);
                    }
                }
            }
        );
    },
    process: function(processUrl, commitUrl, processer){
        var _caller = this;

        var request = new Ajax.Request(
            processUrl,
            {
                method: 'post',
                onSuccess: function(transport){
                    var response = eval('(' + transport.responseText + ')');

                    if (response.status == 'processing'){

                        _caller.tracePosition(response.position, processer);

                        if (response.position == 100){
                            _caller.commit(commitUrl, processer);
                        } else {
                            _caller.process(processUrl, commitUrl, processer);
                        }


                    } else if (response.error){
                        _caller.error(response.error, processer);
                    }
                }
            }
        );
    },
    run: function(startUrl, processUrl, commitUrl, input){
        var _caller = this;

        var request = new Ajax.Request(
            startUrl,
            {
                method: 'post',
                onSuccess: function(transport){
                    var response = eval('(' + transport.responseText + ')');

                    if (response.status == 'started'){
                        var processer = _caller.start(response, input);

                        _caller.process(processUrl, commitUrl, processer);

                    } else if (response.error){
                        _caller.error(response.error);
                    }
                }
            }
        );
    }
}
