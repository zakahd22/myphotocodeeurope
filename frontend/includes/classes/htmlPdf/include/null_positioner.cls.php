<?php
/**
 * @package dompdf
 * @link    https://dompdf.github.com/
 * @author  Benj Carson <benjcarson@digitaljunkies.ca>
 * @license https://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 */

/**
 * Dummy positioner
 *
 * @access private
 * @package dompdf
 */
class Null_Positioner extends Positioner {

  function __construct(Frame_Decorator $frame) {
    parent::__construct($frame);
  }

  function position() { return; }
  
}
