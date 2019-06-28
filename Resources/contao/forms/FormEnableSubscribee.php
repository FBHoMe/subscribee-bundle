<?php

namespace Home\SubscribeeBundle\Resources\contao\forms;


class FormEnableSubscribee extends \Contao\FormHidden
{

	/**
	 * Submit user input
	 *
	 * @var boolean
	 */
	protected $blnSubmitInput = true;

	/**
	 * Template
	 *
	 * @var string
	 */
	protected $strTemplate = 'form_enable_subscribee';

	protected $strLabel = 'Subscribee';

	protected $strName = 'enableSubscribee';

    /**
	 * Generate the widget and return it as string
	 *
	 * @return string The widget markup
	 */
	public function generate()
	{
		return sprintf('<input type="hidden" name="%s" value="%s"%s',
						$this->strName,
						\StringUtil::specialchars($this->varValue),
						$this->strTagEnding);
	}
}
