<?php
/**
 * This file is part of
 * pragmaMx - Web Content Management System.
 * Copyright by pragmaMx Developer Team - http://www.pragmamx.org
 *
 * pragmaMx is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * $Revision: 314 $
 * $Author: svenlang $
 * $Date: 2016-12-31 13:47:53 +0100 (Sa, 31. Dez 2016) $
 */

defined( 'mxMainFileLoaded' ) or die( 'access denied' );

/**
 * Class pmxCharts
 *
 * @class pmxCharts
 * @api https://developers.google.com/chart/
 * @author Sven Hedström-Lang <SvenLang07@gmail.com>
 * @copyright 2016-12-06
 * @since pragmaMx 2.4.0
 * @phpversion >=5.6
 * @require
 *      pmxHeader::add_script()
 *      pmxHeader::add_style_code()
 *      load_class( 'Template' )
 *      mxTranslate( '_DOC_LANGUAGE' )
 *      MX_IS_ADMIN
 * @files
 *      ./includes/classes/Charts.php (this file)
 *      ./layout/templates/includes/classes/Charts/*
 * @changelog
 *      2016-12-15 - update setData()
 *      2016-12-15 - setData() add to each columns optional an extra color style
 *      2016-12-15 - getTemplate() is any error display no chart only error message for MX_IS_ADMIN
 *      2016-12-28 - delete legend, set default to 'none
 *      2016-12-30 - init add 'Twig Template'
 *      2016-12-31 - getTemplate() show all errors in charts, but error message by developer show to MX_IS_ADMIN
 */
class pmxCharts {

	const DEFAULT_CHART = 'PieChart';
	const DEFAULT_ANIMATION_DURATION = 1000;
	const DEFAULT_ANIMATION_EASING = 'linear';
	const DEFAULT_OPTIONS = array(
		'title'     => null,
		'legend'    => 'none',
		'is3D'      => true,
		'animation' => null
		// some more options
		// 'bar' => array( 'groupWidth' => '60%' ) // breite des balkens
		// 'width' => '100%', // require for CSS - same is set or not
		// 'height' => '100%' // require for CSS - same is set or not
		//'chartArea' => array( 'width'=> '75%' ) // same as 'width'
	);

	private $_error = array();
	private $_domId;
	private $_options = array();
	private $_chart = self::DEFAULT_CHART;
	private $_data = array();

	/**
	 * pmxCharts constructor.
	 *
	 * INFO: 'PieChart' have no animation and cant set styles.
	 *
	 * @example
	 *      // minimal
	 *      $charts = load_class( 'Charts' );
	 *      $charts->setData( array(
	 *          array('Antwort','Stimmen',array('role'=>'style')),
	 *          array('sehr gut',8,'red'),
	 *          array('ganz nett',21,'blue')
	 *      ));
	 *      echo $charts->getTemplate();
	 *
	 *      // maximal
	 *      $charts = load_class( 'Charts' );
	 *      $charts->setChart( 'BarChart' );
	 *      $charts->setAnimation( 2000, 'in' );
	 *      $charts->setBackgroundColor( '#F4F4F4' );
	 *      $charts->setTitle( mxTranslate( '_QUESTION' ) );
	 *      $charts->setData( array(
	 *          array('Antwort','Stimmen',array('role'=>'style')),
	 *          array('sehr gut',8,'red'),
	 *          array('ganz nett',21,'blue')
	 *      ));
	 *      echo $charts->getTemplate( false );
	 */
	public function __construct() {
		$this->_setDomId();
	}

	/**
	 * Set chart display.
	 *
	 * @param string $chart -
	 *      ( See different as image in ./layout/templates/includes/classes/Charts/example/* )
	 *      'BarChart': horizontal
	 *      'ColumnChart': vertical
	 *      'PieChart': circle
	 *      'LineChart': lines
	 *      'AreaChart': fill lines
	 *      'SteppedAreaChart': stepped fill lines
	 */
	public function setChart( $chart ) {
		switch ( $chart ) {
			case 'BarChart':
			case 'ColumnChart':
			case 'PieChart':
			case 'LineChart':
			case 'AreaChart':
			case 'SteppedAreaChart':
				$this->_chart = $chart;
				break;

			default:
				$this->_chart = self::DEFAULT_CHART;
				break;
		}
	}

	/**
	 * Add an animation. Works not with 'PieChart'.
	 *
	 * @param int [$duration=1000] - In milliseconds.
	 * @param string [$easing] -
	 * * 'linear': Constant speed.
	 * * 'in': Ease in, start slow and speed up.
	 * * 'out': Ease out, start fast and slow down.
	 * * 'inAndOut': Ease in and out, start slow, speed up, then slow down.
	 *
	 * @example
	 *      // activate default animation
	 *      setAnimation();
	 *
	 *      // customer animation
	 *      setAnimation( 2000, 'in' );
	 */
	public function setAnimation( $duration = self::DEFAULT_ANIMATION_DURATION, $easing = self::DEFAULT_ANIMATION_EASING ) {
		$this->_setOptions( 'animation', array(
			'startup'  => true,
			'duration' => is_numeric( $duration ) && $duration >= 0 ? $duration : self::DEFAULT_ANIMATION_DURATION,
			'easing'   => $this->_checkEasing( $easing )
		) );
	}

	/**
	 * Set background color in hex color.
	 *
	 * @param string $hexColor - Accept a 7 hex color sign.
	 *
	 * @example
	 *      setBackgroundColor( '#ff0000' );
	 *      setBackgroundColor( '#FF0000' );
	 *      // set to 'red'
	 *      setBackgroundColor( '#F00' );
	 *      // nothing set: Get an error message in HTML.
	 */
	public function setBackgroundColor( $hexColor ) {
		if ( $this->_isHexColor( $hexColor ) ) {
			$this->_setOptions( 'backgroundColor', array( 'fill' => $hexColor ) );
		} else {
			$this->_setErrorMessage( 'Charts: Invalid hex-color.' );
		}
	}

	/**
	 * Set the title inside the charts.
	 *
	 * @param string $title
	 */
	public function setTitle( $title ) {
		$this->_setOptions( 'title', $title );
	}

	/**
	 * Set data to display.
	 *
	 * WARNING: Style not work wirh 'PieChart'.
	 *
	 * INFO: Style accept:
	 *      is any color invalid    e.g. 'null' display default API color 'blue'
	 *      hex color:              e.g. '#b87333' or '#ccc'
	 *      color name:             e.g. 'silver'
	 *      AND: some CSS styles: accept 'color', 'stroke-color', 'stroke-opacity', 'stroke-width', 'fill-color', 'fill-opacity'
	 *
	 * @param array $data - The columns.
	 *
	 * @return bool - Is correct returns true otherwise false.
	 * @example
	 *      // without style (simple)
	 *      setData(array(
	 *          array('Antwort', 'Stimmen'),
	 *          array('sehr gut', 12),
	 *          array('ganz nett', 21),
	 *          array('naja', 5)
	 *      ));
	 *      // -> true
	 *
	 *      // with style (simple)
	 *      setData(array(
	 *          array('Antwort', 'Stimmen', array('role' => 'style')),
	 *          array('sehr gut', 12, '#f00'),
	 *          array('ganz nett', 21, 'green'),
	 *          array('naja', 5, 'yellow')
	 *      ));
	 *      // -> true
	 *
	 *      // add some more results (simple)
	 *      setData(array(
	 *          array('Antwort', 'Letzte Woche', 'Diese Woche'),
	 *          array('sehr gut', 6, 12),
	 *          array('ganz nett', 14, 21),
	 *          array('naja', 2, 5)
	 *      ));
	 *      // -> true
	 *
	 *      // with style (complex)
	 *      setData(array(
	 *          array('Antwort', 'Stimmen', array('role' => 'style')),
	 *          array('sehr gut', 12, '#f00'),
	 *          array('ganz nett', 21, 'color: green; stroke-color: #871B47; stroke-opacity: 0.6; stroke-width: 8; fill-color: #BC5679; fill-opacity: 0.2'),
	 *          array('naja', 5, 'yellow')
	 *      ));
	 *      // -> true
	 */
	public function setData( $data ) {
		// JavaScript -> data = [['Antwort','Stimmen',{role:'style'}],['sehr gut',10,null],['ganz nett',21,'gray'],['naja',14,'#76A7FA']

		if ( ! is_array( $data ) || count( $data ) <= 1 ) {
			$this->_setErrorMessage( 'Chart: Require minimum one data.' );

			return false;
		}

		$checkLength = null;
		foreach ( $data as $item ) {
			$checkLength = $checkLength !== null ? $checkLength : count( $item );
			if ( $checkLength !== count( $item ) ) {
				$this->_setErrorMessage( 'Chart: Require same columns length.' );

				return false;
			}
		}

		$this->_data = $data;

		return true;
	}


	/**
	 * Get the dom id to e.g. manipulation the CSS.
	 *
	 * @return string
	 * @example
	 *      getDomId();
	 *      // -> look _setDomId() for more info.
	 */
	public function getDomId() {
		return $this->_domId;
	}

	/**
	 * Get all error messages.
	 *
	 * @return array - If exists returns all messages otherwise an empty array.
	 */
	public function getError() {
		return $this->_error;
	}

	/**
	 * Check exists an error e.g. to not display title outside of the chart.
	 *
	 * @return bool - Exists an error return true otherwise false.
	 */
	public function isError() {
		return count( $this->getError() ) > 0;
	}

	/**
	 * Get the template.
	 *
	 * @param bool [$addStyle=true] - true display with 100% otherwise false disable default style of API.
	 *
	 * @return string - The template.
	 * @example
	 *      getTemplate();
	 *      // or
	 *      getTemplate( true );
	 *      // -> show the template width & height in 100%.
	 *
	 *      getTemplate( false );
	 *      // -> show the template in default.
	 */
	public function getTemplate( $addStyle = true ) {

		// Add Google API
		pmxHeader::add_script( 'https://www.gstatic.com/charts/loader.js' );

		if ( $addStyle ) {
			// BUGFIX: 'margin-bottom' require when show a text after
			pmxHeader::add_style_code(
				'
				#plugin-charts-wrapper {
				    margin-bottom: 1em;
					overflow: hidden;
					padding-bottom: 80%;
					position: relative;
				}
		
				#' . $this->getDomId() . ' {
					height: 100%;
					position: absolute;
					width: 100%;
				}
			'
			);
		}

		/*
		 * Savant3 Template
		 */
		$tpl = load_class( 'Template' );
		$tpl->init_path( __FILE__ );
		$tpl->assign( 'chart', $this->_getChart() );
		$tpl->assign( 'currentlang', mxTranslate( '_DOC_LANGUAGE' ) );
		$tpl->assign( 'data', json_encode( $this->_getData() ) );
		$tpl->assign( 'domId', $this->getDomId() );
		$tpl->assign( 'error', $this->getError() );
		$tpl->assign( 'options', $this->_getOptions() );

		return $tpl->fetch( 'charts.html' );

		/**
		 * Twig Template
		 *
		 * todo : switch to Twig Template
		 * delete charts.html
		 *
		 * $tpl = load_class( 'Template' );
		 * return $tpl->getFileContent( __FILE__, 'charts.twig.html', array(
		 * 'chart'       => $this->_getChart(),
		 * 'currentlang' => mxTranslate( '_DOC_LANGUAGE' ) || null,
		 * 'data'        => $this->_getData(),
		 * 'domId'       => $this->getDomId(),
		 * 'error'       => $this->getError(),
		 * 'options'     => $this->_getOptions()
		 * ) );
		 */

	}

	// ###########################
	// # PRIVATE GETTER & SETTER #
	// ###########################

	/**
	 * @return string
	 * @private
	 */
	private function _getChart() {
		return $this->_chart;
	}

	/**
	 * @return array
	 * @private
	 */
	private function _getData() {
		return $this->_data;
	}

	/**
	 * @return string
	 * @private
	 */
	private function _getOptions() {
		return array_merge( self::DEFAULT_OPTIONS, $this->_options );
	}

	/**
	 * @param string $key
	 * @param mixed $value
	 *
	 * @private
	 */
	private function _setOptions( $key, $value ) {
		$this->_options[ $key ] = $value;
	}

	/**
	 * @param string $message
	 *
	 * @private
	 */
	private function _setErrorMessage( $message ) {
		$this->_error[] = $message;
	}

	/**
	 * Set the domId. Add a number to the dom string.
	 */
	private function _setDomId() {
		usleep( 100 ); // require to get different '$this->_domId'
		$this->_domId = 'plugin-charts-' . (string) round( microtime( true ) );
	}

	// #####################
	// # PRIVATE FUNCTIONS #
	// #####################

	/**
	 * Check is the easing correct and return it or the default easing.
	 *
	 * @param string $easing
	 * * 'linear': Constant speed.
	 * * 'in': Ease in - Start slow and speed up.
	 * * 'out': Ease out - Start fast and slow down.
	 * * 'inAndOut': Ease in and out - Start slow, speed up, then slow down.
	 *
	 * @return string
	 * @private
	 */
	private function _checkEasing( $easing ) {
		switch ( $easing ) {
			case 'linear':
			case 'in':
			case 'out':
			case 'inAndOut':
				return $easing;

			default:
				return self::DEFAULT_ANIMATION_EASING;
		}
	}

	/**
	 * Check is a hex color.
	 *
	 * @param string $hexColor
	 *
	 * @return bool
	 * @private
	 */
	private function _isHexColor( $hexColor ) {
		return is_string( $hexColor ) && preg_match( '/^#[a-f0-9]{6}$/i', $hexColor ) === 1;
	}

}

?>
