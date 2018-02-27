/*
 * October CMS plugin: Adrenth.Redirect
 *
 * Copyright (c) 2016 - 2018 Alwin Drenth
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software
 * and associated documentation files (the "Software"), to deal in the Software without restriction,
 * including without limitation the rights to use, copy, modify, merge, publish, distribute,
 * sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all copies or
 * substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

var testerShouldStop = false;

function testerExecute(offset, total, button) {
    if (testerShouldStop) {
        testerDone();
        testerShouldStop = false;
        return;
    }

    $.request('onTest', {
        data: {
            offset: offset
        },
        success: function (data) {
            if (data.result === '' || typeof data.result === 'undefined') {
                testerDone();
                updateStatusBar(total, total);
                return;
            }

            $('#testerResults').prepend(data.result);

            updateStatusBar(total, offset);

            if (offset + 1 !== total) {
                testerExecute(offset + 1, total, button);
            }
        },
        error: function() {
            if (offset + 1 !== total) {
                testerExecute(offset + 1, total, button);
            }
        }
    });
}

function testerDone() {
    $('#testButton').prop('disabled', false);

    var loader = $('#loader');
        loader.removeClass('loading');

    setTimeout(function () {
        loader.addClass('hidden');
    }, 500);
}

function testerStart(button) {
    updateStatusBar(0);

    $('#testerResults').html('');

    button.prop('disabled', true);

    var loader = $('#loader');
        loader.removeClass('hidden');
        loader.addClass('loading');

    testerExecute(0, $('#redirectCount').val(), button);
}

function testerStop() {
    testerShouldStop = true;
}

function updateStatusBar(total, offset) {
    var width = 0;

    if (total > 0) {
        width = Math.ceil(100 / total * offset);
    }

    var progress = $('#progress');
    progress.html(width + '% complete (' + offset + ' of ' + total + ')');

    var progressBar = $('#progressBar');
    progressBar.attr('aria-valuenow', width);
    progressBar.css('width', width + '%');

    if (width === 0) {
        progress.html(progress.data('initial'));
    }
}
