<?php
/**
 * Created by PhpStorm.
 * User: felix
 * Date: 27.09.2017
 * Time: 16:33
 */

use Home\PearlsBundle\Resources\contao\Helper\Dca as Helper;

$moduleName = 'tl_content';

$tl_content = new Helper\DcaHelper($moduleName);

try{
$tl_content
    ->addField('select_template', 'hm_template', array(
        'tempPrefix' => 'ce',
    ))
    ->addField('select_table','hm_select_table')

    #-- event_personal_cte
    ->copyPalette('default', 'event_personal_cte')
    ->addPaletteGroup('event_personal_cte', array(
        'hm_template'
    ), 'event_personal_cte')

;
}catch(\Exception $e){
    var_dump($e);
}


