<?php
/*
Plugin Name: AdvancedBedCalibration
Plugin URI: http://www.thingiverse.com/thing:35248
Version: 0.5
Description: Advanced Bed Calibration is a more sophisticated Bed Calibration than the original FabToTum printer currently offers. You can measure individual screws of your bed but you can also measure the whole bed with different levels of granularity and accuracy.
Author: Wolfgang Meyerle
Author URI: http://www.thingiverse.com/hudbrog/designs
Plugin Slug: advancedBedCalibration
*/
 
class advancedBedCalibration extends Plugin {

public function __construct()
	{
		parent::__construct();
		$this->layout->add_css_file(array('src'=>'application/plugins/advancedBedCalibration/views/index.css', 'comment'=>'ADVANCED_BED_CALIBRATION'));
		$this->layout->add_js_file(array('src'=>'application/plugins/advancedBedCalibration/assets/js/ColourGradient.js', 'comment'=>'ADVANCED_BED_CALIBRATION'));
		$this->layout->add_js_file(array('src'=>'application/plugins/advancedBedCalibration/assets/js/jsapi.js', 'comment'=>'ADVANCED_BED_CALIBRATION'));
		$this->layout->add_js_file(array('src'=>'application/plugins/advancedBedCalibration/assets/js/raphael-min.js', 'comment'=>'ADVANCED_BED_CALIBRATION'));
		$this->layout->add_js_file(array('src'=>'application/plugins/advancedBedCalibration/assets/js/SurfacePlot.js', 'comment'=>'ADVANCED_BED_CALIBRATION'));

	}

	public function index(){

		$this->layout->add_js_in_page(array('data'=> $this->load->view('js', '', TRUE), 'comment' => 'INDEX FUNCTIONS'));
		$this->layout->view('index');
	}




}

?>
