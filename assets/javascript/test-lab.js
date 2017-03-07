function testerExecute(offset, total, button) {
    $.request('onTest', {
        data: {
            offset: offset
        },
        success: function (data) {
            if (data.result === '' || typeof data.result === 'undefined') {
                $('#testButton').prop('disabled', false);
                updateStatusBar(total, total);
                return;
            }

            $('#testerResults').prepend(data.result);

            updateStatusBar(total, offset);

            if (offset + 1 !== total) {
                testerExecute(offset + 1, total, button);
            }
        }
    });
}

function start(button) {
    updateStatusBar(0);

    $('#testerResults').html('');
    $('#testButton').prop('disabled', true);

    testerExecute(0, $('#redirectCount').val(), button);
}

function updateStatusBar(total, offset) {
    var width = 0;

    if (total > 0) {
        width = Math.ceil(100 / total * offset);
    }

    var progress = $('#progress');
    progress.html(width + '% complete');

    var progressBar = $('#progressBar');
    progressBar.attr('aria-valuenow', width);
    progressBar.css('width', width + '%');

    if (width === 0) {
        progress.html(progress.data('initial'));
    }
}
