<?php
/**
 * Created by PhpStorm.
 * User: felix
 * Date: 27.11.2017
 * Time: 14:48
 */

use Home\PearlsBundle\Resources\contao\Helper\Dca as Helper;

$moduleName = 'tl_form';

$tl_form_field = new Helper\DcaHelper($moduleName);

$tl_form_field
    ->addField('checkbox', 'useSubscribee')
    ->addPaletteGroup('subscribee',array('useSubscribee'))
;