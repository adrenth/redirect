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

$(document).ready(function () {

    $.request('onRedirectHitsPerDay', {
        data: {},
        success: function (data) {
            var items = eval(data.result);
            var container = document.getElementById('visualization');

            var groups = new vis.DataSet();
                groups.add({id: 0, content: "Crawlers"});
                groups.add({id: 1, content: "Users"});

            new vis.DataSet(items);

            var options = {
                style: 'bar',
                barChart: {
                    width: 30,
                    align: 'center',
                    sideBySide: false
                }, // align: left, center, right
                drawPoints: false,
                dataAxis: {
                    visible: true
                },
                legend: true,
                orientation: 'top',
                graphHeight: '230px',
                clickToUse: true
            };

            new vis.Graph2d(container, items, groups, options);
        }
    });
});
