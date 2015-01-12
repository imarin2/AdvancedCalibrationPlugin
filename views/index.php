<div class="row">
    <div class="col-xs-6 col-sm-4 col-md-4 col-lg-4">
        <h1 class="page-title txt-color-blueDark">Maintenance <span>&gt; Advanced Bed Levelling</span></h1>
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <div class="well">
            <div class="row" id="drawingArea"></div>
            <div>
                <select id="calibrationMethod">
                    <!--option value="None">Select Calibration Method...</option-->
                    <option value="ScrewCalibration">Calibrate selected screws</option>
                    <option value="MeasureWholeBed">Measure whole bed for accuracy</option>
                    <option value="CheckCalibration">Check calibration</option>
                    <option value="PerformTestPrint">Perform test print</option>
                </select>
            </div>
            <p id="SliderBedScanGranularityText" class="text-center">Granularity of Bed Scan</p>
            <div id="SliderBedScanGranularity" style="left:50%; margin-left: -150px; width: 300px;"></div>
            <p id="AccuracyOfScan" class="text-center" val="0">Accuracy of measurements (low, but quick)</p>
            <div id="slider" style="left:50%; margin-left: -150px; width: 300px;"></div>
            <p class="text-center" style="margin-top: 10px;">
                <a id="ButtonStartStop" href="javascript:void(0);" class="btn btn-primary btn-default do-calibration" name="ButtonStartStop">Start new measurement</a>
            </p>
            <p class="text-center" style="margin-top: 10px;">
                <a id="ButtonLoadLastMeasurement" href="javascript:void(0);" class="btn btn-primary btn-default load-last-measurement" name="ButtonLoadLastMeasurement">Load last measurement</a>
            </p>
            <div id="progressbar">
                <div id="progressLabel" class="progress-label">Loading...</div>
            </div>
        </div>
    </div>
</div>
<div id="updateDiv"></div>
<div id="surfacePlotDiv">
    <!-- SurfacePlot goes here... -->
</div>

