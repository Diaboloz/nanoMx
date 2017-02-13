<?php
/**
 * Numbers_Words
 *
 * PHP version 4
 *
 * Copyright (c) 1997-2006 The PHP Group
 *
 * This source file is subject to version 3.0 of the PHP license,
 * that is bundled with this package in the file LICENSE, and is
 * available at through the world-wide-web at
 * http://www.php.net/license/3_0.txt.
 * If you did not receive a copy of the PHP license and are unable to
 * obtain it through the world-wide-web, please send a note to
 * license@php.net so we can mail you a copy immediately.
 *
 * Authors: Piotr Klaban <makler@man.torun.pl>
 *
 * @category Numbers
 * @package Numbers_Words
 * @author Piotr Klaban <makler@man.torun.pl>
 * @license PHP 3.0 http://www.php.net/license/3_0.txt
 * @version CVS: Id: Words.php 295090 2010-02-15 06:38:34Z clockwerx
 * @link http://pear.php.net/package/Numbers_Words
 * @pragmaMx CVS: $Id: Numbers_Words.php 6 2015-07-08 07:07:06Z PragmaMx $
 */
// {{{ Numbers_Words
/**
 * The Numbers_Words class provides method to convert arabic numerals to words.
 *
 * @category Numbers
 * @package Numbers_Words
 * @author Piotr Klaban <makler@man.torun.pl>
 * @license PHP 3.0 http://www.php.net/license/3_0.txt
 * @link http://pear.php.net/package/Numbers_Words
 * @since PHP 4.2.3
 * @access public
 */
class Numbers_Words {
    // {{{ Numbers2Words()
    /**
     * Converts a number to its word representation
     *
     * @param integer $num An integer between -infinity and infinity inclusive :)
     *                         that should be converted to a words representation
     * @param string $locale Language name abbreviation. Optional. Defaults to en_US.
     * @access public
     * @author Piotr Klaban <makler@man.torun.pl>
     * @since PHP 4.2.3
     * @return string The corresponding word representation
     */
    function Numbers2Words($num, $locale = 'en')
    {
        include_once dirname(__FILE__) . "/Numbers_Words/lang.${locale}.php";

        $classname = "Numbers_Words_${locale}";

        if (!class_exists($classname)) {
            return Numbers_Words::raiseError("Unable to include the Numbers_Words/lang.${locale}.php file");
        }

        $methods = get_class_methods($classname);

        if (!in_array('toWords', $methods) && !in_array('towords', $methods)) {
            return Numbers_Words::raiseError("Unable to find toWords method in '$classname' class");
        }

        @$obj = new $classname;

        if (!is_int($num)) {
            // cast (sanitize) to int without losing precision
            $num = preg_replace('/^[^\d]*?(-?)[ \t\n]*?(\d+)([^\d].*?)?$/', '$1$2', $num);
        }

        return trim($obj->toWords($num));
    }
    // }}}
    // {{{ raiseError()
    /**
     * Trigger a PEAR error
     *
     * To improve performances, the PEAR.php file is included dynamically.
     *
     * @param string $msg error message
     * @return PEAR_Error
     */
    function raiseError($msg)
    {
        // include_once 'PEAR.php';
        // return PEAR::raiseError($msg);
        $trace = debug_backtrace();
        trigger_error($msg . ' in ' . mx_strip_sysdirs($trace[0]['file']) . ' line ' . $trace[0]['line'], E_USER_NOTICE);
    }
    // }}}
}
// }}}
?>
