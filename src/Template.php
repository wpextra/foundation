<?php

namespace Bridge\Foundation;


if (!defined('ABSPATH'))
	exit;

use WPE\Template\Twig\Twig_Function;

class Template {

	
	/**
	 *
	 *
	 * @param mixed   $arr
	 * @return array
	 */
	public function to_array( $arr ) {
		if ( is_array($arr) ) {
			return $arr;
		}
		$arr = array($arr);
		return $arr;
	}

	/**
	 *
	 *
	 * @param string  $function_name
	 * @return mixed
	 */
	public function exec_function( $function_name ) {
		$args = func_get_args();
		array_shift($args);
		if ( is_string($function_name) ) {
			$function_name = trim($function_name);
		}
		return call_user_func_array($function_name, ($args));
	}

	/**
	 *
	 *
	 * @param string  $content
	 * @return string
	 */
	public function twig_pretags( $content ) {
		return preg_replace_callback('|<pre.*>(.*)</pre|isU', array(&$this, 'convert_pre_entities'), $content);
	}

	/**
	 *
	 *
	 * @param array   $matches
	 * @return string
	 */
	public function convert_pre_entities( $matches ) {
		return str_replace($matches[1], htmlentities($matches[1]), $matches[0]);
	}

	/**
	 *
	 *
	 * @param string  $date
	 * @param string  $format (optional)
	 * @return string
	 */
	public function intl_date( $date, $format = null ) {
		if ( $format === null ) {
			$format = get_option('date_format');
		}

		if ( $date instanceof \DateTime ) {
			$timestamp = $date->getTimestamp() + $date->getOffset();
		} else if ( is_numeric($date) && (strtotime($date) === false || strlen($date) !== 8) ) {
			$timestamp = intval($date);
		} else {
			$timestamp = strtotime($date);
		}

		return date_i18n($format, $timestamp);
	}

	/**
	 * @param int|string $from
	 * @param int|string $to
	 * @param string $format_past
	 * @param string $format_future
	 * @return string
	 */
	public static function time_ago( $from, $to = null, $format_past = '%s ago', $format_future = '%s from now' ) {
		$to = $to === null ? time() : $to;
		$to = is_int($to) ? $to : strtotime($to);
		$from = is_int($from) ? $from : strtotime($from);

		if ( $from < $to ) {
			return sprintf($format_past, human_time_diff($from, $to));
		} else {
			return sprintf($format_future, human_time_diff($to, $from));
		}
	}

	/**
	 * @param array $arr
	 * @param string $first_delimiter
	 * @param string $second_delimiter
	 * @return string
	 */
	public function add_list_separators( $arr, $first_delimiter = ',', $second_delimiter = 'and' ) {
		$length = count($arr);
		$list = '';
		foreach ( $arr as $index => $item ) {
			if ( $index < $length - 2 ) {
				$delimiter = $first_delimiter.' ';
			} elseif ( $index == $length - 2 ) {
				$delimiter = ' '.$second_delimiter.' ';
			} else {
				$delimiter = '';
			}
			$list = $list.$item.$delimiter;
		}
		return $list;
	}

	

}
