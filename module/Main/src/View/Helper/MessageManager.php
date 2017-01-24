<?php

/**
 * @copyright	Copyright (c) 2015 - 2016
 * @license		https://creativecommons.org/licenses/by-nc-sa/4.0/legalcode This work is licensed under a Creative Commons Attribution-NonCommercial-ShareAlike 4.0 International License.
 * @author		Mulot
 * @link		http://life.je.gfns.ru/
 * @version		0.1 alpha
 * @since		File available since 0.1 alpha
 */

namespace Main\View\Helper;

use Zend\View\Helper\FlashMessenger;

/**
 * @package		Main\View\Helper
 * @copyright	Copyright (c) 2015 - 2016
 * @license		https://creativecommons.org/licenses/by-nc-sa/4.0/legalcode This work is licensed under a Creative Commons Attribution-NonCommercial-ShareAlike 4.0 International License.
 * @author		Mulot
 * @version		0.1 alpha
 * @since		File available since 0.1 alpha
 */
class MessageManager extends FlashMessenger
{	
	public function renderHtml() {
		$str = '';
		if ($this->hasCurrentInfoMessages()) {
			$str .= '<div class="alert alert-info alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
			$str .= $this->renderMessages('info', $this->getCurrentInfoMessages());
			$str .= '</div>';
		}
		
		if ($this->hasCurrentWarningMessages()) {
			$str .= '<div class="alert alert-warning alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
			$str .= $this->renderMessages('warning', $this->getCurrentWarningMessages());
			$str .= '</div>';
		}
		
		if ($this->hasCurrentErrorMessages()) {
			$str .= '<div class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
			$str .= $this->renderMessages('danger', $this->getCurrentErrorMessages());
			$str .= '</div>';
		}
		
		if ($this->hasCurrentSuccessMessages()) {
			$str .= '<div class="alert alert-success alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
			$str .= $this->renderMessages('success', $this->getCurrentSuccessMessages());
			$str .= '</div>';
		}
		return $str;
	}
}