<script type="text/javascript">
    var ticker_url = '';
    var interval_ticker;
    /* Event handlers */ 
    $(function() {

        $(".do-calibration").on('click', do_calibration);
	$("#ButtonLoadLastMeasurement").on('click', load_last_measurement);
        /*interval_ticker = setInterval(ticker, 500);*/


    });



    function ticker() {

        if (ticker_url != '') {

            $.get(ticker_url, function(data) {

                if (data != '') {

                    waitContent(data);

                }
            }).fail(function() {

            });
        }
    }

$.fn.textWidth = function(text, font) {
    if (!$.fn.textWidth.fakeEl) $.fn.textWidth.fakeEl = $('<span>').hide().appendTo(document.body);
    $.fn.textWidth.fakeEl.text(text || this.val() || this.text()).css('font', font || this.css('font'));
    return $.fn.textWidth.fakeEl.width();
};

    var lastTriggeredTimestamp = jQuery.now();

    function do_calibration() {
        var buttonStartStop = $("#ButtonStartStop");
        if (buttonStartStop.text().trim() == "Start") {

            additionalProgressLabelText = "";
            buttonStartStop.text("Stop");
            var progressbar = $("#progressbar"),
                progressLabel = $(".progress-label");
            progressbar.progressbar("value", 0);
            progressbar.progressbar("option", "value", false);

            progressbar.show();
            progressLabel.show();

            var now = jQuery.now();
            /* now = 1417816594544;	*/
            lastTriggeredTimestamp = now;
            setTimeout(updateCalibProgress, 2000);


            /* openWait('Calibration in process'); */

            ticker_url = '/temp/advancedBedCalibration_' + now + '.trace';

            /* console.log(upperLeftSelected+" "+upperRightSelected+" "+lowerLeftSelected+" "+lowerRightSelected+" "+currentAccuracy+" "+calibrationMethod+" "+bedScanGranularityNr); */
            $.ajax({
                type: "POST",
                url: "/fabui/application/plugins/advancedBedCalibration/assets/js/ajax/advancedBedCalibration.php",
                data: {
                    time: now,
                    accuracy: currentAccuracy,
                    calibration_method: calibrationMethodStr,
                    bedScanGranularity: bedScanGranularityNr,
                    pointsToMeasure: JSON.stringify(points)
                },
                dataType: "html"
            }).done(function(data) {
                updateMeasurementProgress(data);
            });


        } else {
            openWait('Stopping process'); 
            $.ajax({
                type: "POST",
                url: "/fabui/application/plugins/advancedBedCalibration/assets/js/ajax/advancedBedCalibrationStop.php",
                data: {
                    time: now
                },
                dataType: "html"
            }).done(function(data) {
                console.log(data);
                var buttonStartStop = $("#ButtonStartStop");
                buttonStartStop.text("Start");
		closeWait();

            });

        }
    }


    var paper;
    var width = 331;
    var height = 331;

    var posX = 0;
    var posY = 0;

    var imageWidth = 331;
    var imageHeight = 331;

    var upperLeftScrew;
    var lowerLeftScrew;

    var upperRightScrew;
    var lowerRightScrew;

    var upperLeftSelected = false;
    var upperRightSelected = false;

    var lowerLeftSelected = false;
    var lowerRightSelected = false;

    var selectedColor = "#fff";
    var selectedFill = "#f00";

    var unselectedColor = "#fff";
    var unselectedFill = "#f00";

    var mouseOverColor = "#000000";
    var mouseOverFill = "#f00";
    var spacing = 50;

    var onClick = function(event) {
        var x = event.offsetX;
        var y = event.offsetY;

        /* Upper left */
        if (x < width / 2 - spacing + posX && y < width / 2 - spacing + posY) {
            upperLeftSelected = !upperLeftSelected;
        }

        /* Upper Right */
        if (x > width / 2 + spacing + posX && y < width / 2 - spacing + posY) {
            upperRightSelected = !upperRightSelected;
        }

        /* Lower left */
        if (x < width / 2 - spacing + posX && y > width / 2 + spacing + posY) {
            lowerLeftSelected = !lowerLeftSelected;
        }

        /* Lower Right */
        if (x > width / 2 + spacing + posX && y > width / 2 + spacing + posY) {
            lowerRightSelected = !lowerRightSelected;
        }

        updateVisibility();
        calculatePointsForMeasurement(bedScanGranularityNr, calibrationMethodStr);
        onMouseMove(event);


    };

    function updateVisibility() {
        /* Hide all elements which are currently not selected */
        if (!upperLeftSelected) {
            upperLeftScrew.hide().attr("stroke", unselectedColor);
        } else {
            upperLeftScrew.attr("stroke", selectedColor);
        }

        if (!upperRightSelected) {
            upperRightScrew.hide().attr("stroke", unselectedColor);
        } else {
            upperRightScrew.attr("stroke", selectedColor);
        }

        if (!lowerLeftSelected) {
            lowerLeftScrew.hide().attr("stroke", unselectedColor);
        } else {
            lowerLeftScrew.attr("stroke", selectedColor);
        }

        if (!lowerRightSelected) {
            lowerRightScrew.hide().attr("stroke", unselectedColor);
        } else {
            lowerRightScrew.attr("stroke", selectedColor);

        }
    }

    function hideScrews() {
        upperLeftScrew.hide();
        upperRightScrew.hide();
        lowerLeftScrew.hide();
        lowerRightScrew.hide();
    }

    var onMouseMove = function(event) {
        if (!disableScrewSelection) {
            var x = event.offsetX;
            var y = event.offsetY;

            updateVisibility();

            /* Upper left */
            if (x < width / 2 - spacing + posX && y < width / 2 - spacing + posY) {
                upperLeftScrew.show();
                upperLeftScrew.attr({
                    "stroke": mouseOverColor,
                    "fill": mouseOverFill
                });
            }

            /* Upper Right */
            if (x > width / 2 + spacing + posX && y < width / 2 - spacing + posY) {
                upperRightScrew.show();
                upperRightScrew.attr({
                    "stroke": mouseOverColor,
                    "fill": mouseOverFill
                });
            }

            /* Lower left */
            if (x < width / 2 - spacing + posX && y > width / 2 + spacing + posY) {
                lowerLeftScrew.show();
                lowerLeftScrew.attr({
                    "stroke": mouseOverColor,
                    "fill": mouseOverFill
                });
            }

            /* Lower Right */
            if (x > width / 2 + spacing + posX && y > width / 2 + spacing + posY) {
                lowerRightScrew.show();
                lowerRightScrew.attr({
                    "stroke": mouseOverColor,
                    "fill": mouseOverFill
                });
            }
        }


    };

    function updateCalibProgress() {
        $.ajax({
            type: "POST",
            url: "/fabui/application/plugins/advancedBedCalibration/assets/js/ajax/advancedBedCalibrationProgressUpdater.php",
            data: {
                time: lastTriggeredTimestamp,
            },
            dataType: "html"
        }).done(function(data) {
            updateMeasurementProgress(data);
        });
    }

    function updateMeasurementProgress(measurementVals) {
        var currentProgress = 0;
        var repeat = true;

        if (measurementVals != null && measurementVals.trim().length > 0) {
            try {
                var measurementValues = JSON.parse(measurementVals)['bed_calibration']['point_measurements'];
                var measurementProgress = JSON.parse(measurementVals)['progress'];
                var measurementInformation = JSON.parse(measurementVals)['measurementInformation'];

                /* console.log(measurementProgress); */
                var currentPointsMeasured = measurementProgress['pointsMeasured'];
                var currentPointsToMeasure = measurementProgress['pointsToMeasure'];
                currentProgress = currentPointsMeasured * 100.0 / currentPointsToMeasure;
                currentProgress = parseFloat(currentProgress.toFixed(2));

                var currentBedScanGranularity = measurementInformation['bedscanGranularity'];
                var currentFeedrate = measurementInformation['feedrate'];
                var currentProbesPerPoint = measurementInformation['probesPerPoint'];
                var currentETA = measurementInformation['time_left'];

                var currentPoint = 0;
                var currentProbe = 0;

                if (currentProbesPerPoint > 0) {
                    currentPoint = parseFloat((currentPointsMeasured / currentProbesPerPoint).toFixed(0));
                    currentProbe = (currentPointsMeasured % currentProbesPerPoint) + 1;
                }

                /* Update ProgressBar */
                var progressbar = $("#progressbar");

                additionalProgressLabelText = "Measuring Point: " + currentPoint + " of " + (currentPointsToMeasure / currentProbesPerPoint) + " Probe: " + currentProbe + " / " + currentProbesPerPoint + " Time left: " + currentETA + " ";
                progressbar.progressbar("value", currentProgress);


                /* Update GUI */

                var precision = 10;
                var coordinates = [
                    [posX + 40, posY - 40 + height, "start"],
                    [posX + width - 50, posY - 40 + height, "end"],
                    [posX + 40, posY + 30, "start"],
                    [posX + width - 50, posY + 30, "end"]
                ];


                var singleValsCoords = [
                    [posX, posY + height - 30, "end"],
                    [posX + width, posY + height - 30, "start"],
                    [posX, posY + 10, "end"],
                    [posX + width, posY + 10, "start"]
                ];

                for (var i = 0; i < measurementValues.length; i++) {
                    if (measurementValues[i][3] == "True") {
                        var valid = 0;
                        var meanText = "";
                        var measuredValsText = "";
                        var mean = 0;
                        var additionalTextLines = 0;
                        var zValues = measurementValues[i][2];
                        var textHeight = 10;
                        var maxValsPerLine = 7;
                        for (var measurementIdx = 0; measurementIdx < zValues.length; measurementIdx++) {
                            var val = zValues[measurementIdx];
                            if (val != null && val != "" && val != "N/A") {
                                measuredValsText += val;
                                var fVal = parseFloat(val);

                                mean += fVal;
                                valid++;
                                if (measurementIdx < zValues.length - 1) {
                                    if (valid > 1 && valid % maxValsPerLine == 0) {
                                        measuredValsText += "\r\n";
                                        additionalTextLines++;
                                    } else {
                                        measuredValsText += ", ";
                                    }
                                }
                            }
                        }
                        if (valid > 0) {
                            mean /= valid;
                            mean = parseFloat(mean.toFixed(precision));
                            meanText = mean + "";


                            /* Search ifx idx is on a corner point */
                            var idxCoords = coordinates.length;
                            if (i == 0) idxCoords = 0;
                            if (i == currentBedScanGranularity + 1) idxCoords = 1;
                            if (i == (currentBedScanGranularity + 2) * (currentBedScanGranularity + 1)) idxCoords = 2;
                            if (i == (currentBedScanGranularity + 2) * (currentBedScanGranularity + 2) - 1) idxCoords = 3;

                            if (idxCoords < coordinates.length) {
                                meanXPos = coordinates[idxCoords][0];
                                meanYPos = coordinates[idxCoords][1];
                                textAnchorMean = coordinates[idxCoords][2];

                                measuredValsXPos = singleValsCoords[idxCoords][0];
                                measuredValsYPos = singleValsCoords[idxCoords][1];
                                textAnchorSingleVals = singleValsCoords[idxCoords][2];

                                displayVals = true;
                            } else {
                                /*
					meanXPos = 0;
					meanYPos = 0;
					textAnchorMean = "start";

					measuredValsXPos = singleValsCoords[i][0];
                                            measuredValsYPos = singleValsCoords[i][1];
                                            textAnchorSingleVals = singleValsCoords[i][2];
					*/

                                displayVals = false;

                            }

                            if (meanTextHashMap[measurementValues[i][0] + " " + measurementValues[i][1]] != null) meanTextHashMap[measurementValues[i][0] + " " + measurementValues[i][1]].remove();

                            if (singleValsTextHashMap[measurementValues[i][0] + " " + measurementValues[i][1]] != null) singleValsTextHashMap[measurementValues[i][0] + " " + measurementValues[i][1]].remove();

                            if (displayVals) {
                                /* Mean Values */
                                /* *********** */
                                meanTextHashMap[measurementValues[i][0] + " " + measurementValues[i][1]] = paper.text(meanXPos,
                                    meanYPos,
                                    meanText).attr({
                                    "font-family": "Arial",
                                    "font-size": "15px",
                                    "font-weight": "normal",
                                    fill: "#000000",
                                    stroke: "black",
                                    "stroke-width": "0px",
                                    "text-anchor": textAnchorMean,
                                    "font-style": "normal"
                                });


                                /* Single Values */
                                /* ************* */
                                if (measuredValsYPos + additionalTextLines * textHeight > posY + height - 30) {
                                    measuredValsYPos -= additionalTextLines * textHeight;
                                }

                                singleValsTextHashMap[measurementValues[i][0] + " " + measurementValues[i][1]] = paper.text(measuredValsXPos,
                                    measuredValsYPos,
                                    measuredValsText).attr({
                                    "font-family": "Arial",

                                    "font-size": "10px",

                                    "font-weight": "normal",

                                    fill: "#000000",

                                    stroke: "black",
                                    "stroke-width": "0px",

                                    "text-anchor": textAnchorSingleVals,
                                    "font-style": "normal"
                                });
                            }
                        }

                    }
                    /* Update Visualisation */
                    setupVisualisation(currentBedScanGranularity, measurementValues, currentPoint, currentProbe, currentProbesPerPoint);

                }
            } catch (e) {
                console.log(e);
                /* repeat = false; */
            }

        }



        if (repeat && currentProgress < 99.99) {
            setTimeout(updateCalibProgress, 1000);
        } else {
            var buttonStartStop = $("#ButtonStartStop");
            buttonStartStop.text("Start");
        }


    }

    function searchMinVal(measurementValues, measuredUpToPoint, measuredUpToProbeNr, nrOfProbesPerPoint) {
        var minSeen = 0;
        if (measurementValues != null) {
            var minSet = false;
            var idx = 0;

            for (var i = 0; i < measurementValues.length; i++) {
                if (measurementValues[i][3] == "True") {
                    var zValues = measurementValues[i][2];
                    for (var measurementIdx = 0; measurementIdx < zValues.length; measurementIdx++) {
                        var val = zValues[measurementIdx];
                        if (idx < measuredUpToPoint * nrOfProbesPerPoint - (nrOfProbesPerPoint - measuredUpToProbeNr))
                            if (val != null && val != "" && val != "N/A") {
                                /* Search for minVal we need this later for a proper Visualisation */
                                var fVal = parseFloat(val);
                                if (!minSet) {
                                    minSet = true;
                                    minSeen = fVal;
                                } else if (minSeen > fVal) {
                                    minSeen = fVal;
                                }
                            }
                        idx++;
                    }
                }

            }
        }

        return minSeen;
    }

    var meanTextHashMap = [];
    var singleValsTextHashMap = [];


    var imagePositionX = 0;
    var image = null;
    var screwInfoText = null;
    var bedScanInfoText = null;

    function isRunning(timestamp) {
        if (timestamp.trim().length > 0) {
            lastTriggeredTimestamp = parseFloat(timestamp);
            setTimeout(updateCalibProgress, 2000);

            additionalProgressLabelText = "";
            var buttonStartStop = $("#ButtonStartStop");
            buttonStartStop.text("Stop");
            var progressbar = $("#progressbar"),
                progressLabel = $(".progress-label");
            progressbar.show();
            progressLabel.show();

        }
    }

    function load_last_measurement() {
        $.ajax({
            type: "POST",
            url: "/fabui/application/plugins/advancedBedCalibration/assets/js/ajax/advancedBedCalibrationLoadLastMeasurement.php",
            data: {
                time: jQuery.now()
            },
            dataType: "html"
        }).done(function(data) {
	    if (data.trim().length>0) {
	    lastTriggeredTimeStamp = "";
            updateMeasurementProgress(data);

	    }
        });

	
    }

    function initialize() {
        /* Check if Calibration is already running */
        $.ajax({
            type: "POST",
            url: "/fabui/application/plugins/advancedBedCalibration/assets/js/ajax/advancedBedCalibrationCheckIfRunning.php",
            data: {
                time: jQuery.now()
            },
            dataType: "html"
        }).done(function(data) {
            isRunning(data);
        });



        var progressbar = $("#progressbar"),
            progressLabel = $(".progress-label");

        progressbar.hide();
        progressLabel.hide();

        paper = Raphael("drawingArea");

        var drawingAreaCenter = $("#drawingArea").width();

        var imagePositionX = 0; /* drawingAreaCenter / 2 - width / 2; */
        posX = imagePositionX;

        image = paper.image("/fabui/application/plugins/advancedBedCalibration/assets/img/bed.png", imagePositionX, 0, 321, 321);

        screwInfoText = paper.text(width / 2 + posX, height / 2 + posY, "Select screws to calibrate").attr({
            "font-family": "Arial",
            "font-size": "20px",
            "font-weight": "normal",
            fill: "#ffffff",
            stroke: "black",
            "stroke-width": "0px",
            "text-anchor": "center",
            "font-style": "normal"
        });


        bedScanInfoText = paper.text(width / 2 + posX, height / 2 + posY, "Select granularity of Scan").attr({
            "font-family": "Arial",
            "font-size": "20px",
            "font-weight": "normal",
            fill: "#ffffff",
            stroke: "black",
            "stroke-width": "0px",
            "text-anchor": "center",
            "font-style": "normal"
        });

        bedScanInfoText.hide();
        var circleSize = 5;

        upperLeftScrew = paper.circle(posX + 30, posY + 10, circleSize).attr({
            "fill": "#f00",
            "stroke": "#fff"
        }).hide();
        lowerLeftScrew = paper.circle(posX + 30, posY + height - 20, circleSize).attr({
            "fill": "#f00",
            "stroke": "#fff"
        }).hide();

        upperRightScrew = paper.circle(posX + width - 40, posY + 10, circleSize).attr({
            "fill": "#f00",
            "stroke": "#fff"
        }).hide();
        lowerRightScrew = paper.circle(posX + width - 40, posY + height - 20, circleSize).attr({
            "fill": "#f00",
            "stroke": "#fff"
        }).hide();


        /* Event Handlers... */
        image.mousemove(onMouseMove);
        image.click(onClick);
        upperLeftScrew.click(onClick);
        upperRightScrew.click(onClick);
        lowerLeftScrew.click(onClick);
        lowerRightScrew.click(onClick);

        /* Hide GUI Elements */
        $("#SliderBedScanGranularityText").hide();
        $("#SliderBedScanGranularity").hide();

        calibrationMethodStr = "SCREW_CALIBRATION";
        calculatePointsForMeasurement(0, calibrationMethodStr);

    }

    var lines = [];

    function removeLines() {
        for (var i = 0; i < lines.length; i++) {
            lines[i].remove();
        }
        lines = [];
    }

    var points = [];

    function calculatePointsForMeasurement(nrOfDivides, method) {
        if (calibrationMethodStr == "SCREW_CALIBRATION") {
            nrOfDivides = 0;
        }
        points = new Array(nrOfDivides * nrOfDivides + 2);
        /* Calculate Points for Bed Scan measurement */
        var maxXPhys = 184; /* originally 195 */
        var minXPhys = 22;
        var maxYPhys = 210;
        var minYPhys = 66.5;

        var ptsIdx = 0;
        for (var y = 0; y < (nrOfDivides + 2); y++) {
            for (var x = 0; x < (nrOfDivides + 2); x++) {
                divXPhys = x * (maxXPhys - minXPhys) / (nrOfDivides + 1);
                divYPhys = y * (maxYPhys - minYPhys) / (nrOfDivides + 1);
                points[ptsIdx] = [
                    minXPhys + divXPhys,
                    minYPhys + divYPhys,
                    0,
                    true /* YES, measure all points */
                ];
                ptsIdx++;
            }
        }

        if (calibrationMethodStr == "SCREW_CALIBRATION") {
            points[0][3] = lowerLeftSelected;
            points[1][3] = lowerRightSelected;
            points[2][3] = upperLeftSelected;
            points[3][3] = upperRightSelected;
        }
    }

    function updateBedScanGranularity(nrOfDivides) {
        removeLines();
        var idx = 0;
        var drawingAreaCenter = $("#drawingArea").width();
        var imagePositionX = 0; /* drawingAreaCenter / 2 - width / 2; */
        posX = imagePositionX;

        var deltaX = 27;
        var deltaY = 18;

        var startX = posX + deltaX;
        var endX = posX + image.getBBox().width - deltaX;

        var startY = deltaY;
        var endY = (image.getBBox().height - deltaY);

        for (var i = 0; i < (nrOfDivides + 2); i++) {
            divX = i * (endX - startX) / (nrOfDivides + 1);
            divY = i * (endY - startY) / (nrOfDivides + 1);
            lines[idx++] = paper.path("M " + (posX + deltaX + divX) + " " + deltaY + " L " + (posX + deltaX + divX) + " " + (image.getBBox().height - deltaY));
            lines[idx++] = paper.path("M " + (posX + deltaX) + " " + (deltaY + divY) + " L " + (posX + image.getBBox().width - deltaX) + " " + (deltaY + divY));
        }

        calculatePointsForMeasurement(nrOfDivides, "BED_MEASUREMENT");
    }



    var bedScanGranularityNr = 0;

    $("#calibrationMethod").change(function() {
        var selectedMethod = $(this).val();
        $("#SliderBedScanGranularityText").hide();
        $("#SliderBedScanGranularity").hide();

        calibrationMethodStr = "NONE";
        removeLines();
        disableScrewSelection = false;
        updateVisibility();

        if (selectedMethod === "MeasureWholeBed") {
            $("#SliderBedScanGranularityText").show();
            $("#SliderBedScanGranularity").show();
            calibrationMethodStr = "BED_MEASUREMENT";
            updateBedScanGranularity(bedScanGranularityNr);
            disableScrewSelection = true;
            hideScrews();
            screwInfoText.hide();
            bedScanInfoText.show();
            $("#SliderBedScanGranularityText").text("Granularity of Bed Scan (" + (bedScanGranularityNr + 1) + " x " + (bedScanGranularityNr + 1) + " squares)");
        }
        if (selectedMethod === "ScrewCalibration") {
            calibrationMethodStr = "SCREW_CALIBRATION";
            screwInfoText.show();
            bedScanInfoText.hide();
            calculatePointsForMeasurement(0, "SCREW_CALIBRATION");
        }
        if (selectedMethod === "CheckCalibration") {
            calibrationMethodStr = "CHECK_CALIBRATION";
        }
        if (selectedMethod === "PerformTestPrint") {
            calibrationMethodStr = "PERFORM_TEST_PRINT";
        }
        if (selectedMethod === "None") {
            calibrationMethodStr = "NONE";
        }
        /* alert(selectedMethod); */

    });

    $(document).ready(function() {
        initialize();
    });

    var currentAccuracy = 0;
    var disableScrewSelection = false;
    $("#slider").slider({
        value: 0,
        min: 0,
        max: 200,
        step: 50,
        slide: function(event, ui) {
            var valStr = "";
            switch (ui.value) {
                case 0:
                    valStr = "(low, but quick)";
                    break;
                case 50:
                    valStr = "(medium)";
                    break;
                case 100:
                    valStr = "(slow but very precise)";
                    break;
                case 150:
                    valStr = "(even slower awesome precision)";
                    break;
                case 200:
                    valStr = "(insane, best that you can get)";
                    break;
                default:
                    valStr = "(low, but quick)";
            }
            $("#AccuracyOfScan").text("Accuracy of individual measurements " + valStr);
            currentAccuracy = ui.value;
        }
    });

    function callbackOnSliderChange(event, ui) {
        var valStr = "";
        switch (ui.value) {}
        bedScanGranularityNr = ui.value;
        $("#SliderBedScanGranularityText").text("Granularity of Bed Scan (" + (bedScanGranularityNr + 1) + " x " + (bedScanGranularityNr + 1) + " squares)");
        updateBedScanGranularity(bedScanGranularityNr);
    }

    $("#SliderBedScanGranularity").slider({
        value: 0,
        min: 0,
        max: 100,
        step: 1,
        slide: callbackOnSliderChange
    });


    var additionalProgressLabelText = "";
    var progressbar = $("#progressbar"),
        progressLabel = $(".progress-label");
    progressLabelWithID = $("#progressLabel");

    progressbar.progressbar({
        value: false,
        change: function() {
            if (additionalProgressLabelText == null) additionalProgressLabelText = "";
            if (progressbar.progressbar("value") == false) {
                progressLabel.text("Setting up mechanics...");
            } else {
                progressLabel.text(additionalProgressLabelText + "(" + progressbar.progressbar("value") + "%)");
            }
            var myWidthTxt = "-" + (($.fn.textWidth(progressLabel.text(), '13px arial bold')/2).toFixed(0)) + 'px'; 
            progressLabel.css("margin-left", myWidthTxt);
        },
        complete: function() {
            progressLabel.text("Complete!");
            var myWidthTxt = "-" + (($.fn.textWidth(progressLabel.text(), '13px arial bold')/2).toFixed(0)) + 'px'; 
            progressLabel.css("margin-left", myWidthTxt);
	    

            progressbar.hide();
            progressLabel.hide();
        }
    });


    /* Surface Plot */

    google.load("visualization", "1");
    /* google.setOnLoadCallback(setUpVisualisation);*/
    var surfacePlot = new greg.ross.visualisation.SurfacePlot(document.getElementById("surfacePlotDiv"));
    var options = null;
    var data = null;
    var tooltipStrings = new Array();
    var drawNotCalled = true;
    var prevNumberOfDivides = -1;

    function setupVisualisation(nrOfDivides, pointData, measuredUpToPoint, measuredUpToProbeNr, nrOfProbesPerPoint) {
        if (pointData != null) {
            var minSeen = searchMinVal(pointData, measuredUpToPoint, measuredUpToProbeNr, nrOfProbesPerPoint);

            if (calibrationMethodStr == "SCREW_CALIBRATION") {
                nrOfDivides = 0;
            }

            /* Calculate Points for Bed Scan measurement */
            var maxXPhys = 184; /* originally 195 */
            var minXPhys = 22;
            var maxYPhys = 210;
            var minYPhys = 66.5;

            /* Setup Data Table */

            var mul = 1.0;

            /* If nrOfDivides has changed, rebuild the dataset */
            if (prevNumberOfDivides != nrOfDivides) data = null;

            if (data == null) {
                prevNumberOfDivides = nrOfDivides;
                data = new google.visualization.DataTable();
                tooltipStrings = new Array();
                for (var i = 0; i < (nrOfDivides + 2) * mul; i++) {
                    data.addColumn('number', 'col' + i);
                }

                data.addRows((nrOfDivides + 2) * mul);
            }
            var idx = 0;
            for (var y = 0; y < (nrOfDivides + 2); y++) {
                for (var x = 0; x < (nrOfDivides + 2); x++) {
                    divXPhys = x * (maxXPhys - minXPhys) / (nrOfDivides + 1);
                    divYPhys = y * (maxYPhys - minYPhys) / (nrOfDivides + 1);
                    var zValues = pointData[idx][2];
                    var mean = 0;
                    var valid = 0;
                    for (var measurementIdx = 0; measurementIdx < zValues.length; measurementIdx++) {
                        var val = zValues[measurementIdx];

                        if (idx < measuredUpToPoint * nrOfProbesPerPoint - (nrOfProbesPerPoint - measuredUpToProbeNr))
                            if (val != null && val != "" && val != "N/A") {
                                mean += parseFloat(val);
                                valid++;
                            }
                    }
                    if (valid > 0) {
                        mean /= valid;
                        data.setValue(x * mul, y * mul, mean - minSeen);
                        /* console.log("data.setValue("+(x*mul)+","+(y*mul)+","+ (mean-minSeen)+"); Mean:"+mean+" MinSeen:"+minSeen); */
                        tooltipStrings[idx] = "x:" + (minXPhys + divXPhys) + ", y:" + (minYPhys + divYPhys) + " = " + mean;
                    } else {
                        tooltipStrings[idx] = "x:" + (minXPhys + divXPhys) + ", y:" + (minYPhys + divYPhys) + " = N/A";
                    }
                    idx++;
                }
            }

            /* Don't fill polygons in IE. It's too slow. */
            var fillPly = true;

            /* Define a colour gradient. */
            var colour1 = {
                red: 0,
                green: 0,
                blue: 255
            };
            var colour2 = {
                red: 0,
                green: 255,
                blue: 255
            };
            var colour3 = {
                red: 0,
                green: 255,
                blue: 0
            };
            var colour4 = {
                red: 255,
                green: 255,
                blue: 0
            };
            var colour5 = {
                red: 255,
                green: 0,
                blue: 0
            };
            var colours = [colour1, colour2, colour3, colour4, colour5];

            /* Axis labels. */
            var xAxisHeader = "X";
            var yAxisHeader = "Y";
            var zAxisHeader = "Z";

            if (options == null) {
                options = {
                    xPos: 300,
                    yPos: 50,
                    width: 500,
                    height: 500,
                    colourGradient: colours,
                    fillPolygons: fillPly,
                    tooltips: tooltipStrings,
                    xTitle: xAxisHeader,
                    yTitle: yAxisHeader,
                    zTitle: zAxisHeader,
                    restrictXRotation: false
                };
            }

            surfacePlot.draw(data, options);
        }
    }
</script>
