<?php
/**
 * tl_calendar_subscribee - dca definitions for subscribee additional fields
 * 
 * @package    subscribee
 * @copyright  HOME - HolsteinMedia
 * @author     Dirk Holstein <dh@holsteinmedia.com>
 *
 */

use Home\PearlsBundle\Resources\contao\Helper\Dca as Helper;

$moduleName = 'tl_calendar';

try{
    $tl_calendar_subscribee = new Helper\DcaHelper($moduleName);
}catch(\Exception $e){
    var_dump($e);
}

try{
$tl_calendar_subscribee
    #-- add Fields -----------------------------------------------------------------------------------------------------
    /** TODO: Prüfung auf ganze, positive Zahl */
    ->addField('text', 'subscribee_defaultPlaces')
    ->addField('text', 'subscribee_listFields')
    ->addField('checkbox', 'subscribee_withLimit')
    ->addField('checkbox', 'subscribee_withSignOut')
    ->addField('select_from_table', 'subscribee_form', array(
        'foreignKey' => 'tl_form.title',
        'relation' => array(
            'type'=>'hasOne',
            'load'=>'eager',
            'table'=>'tl_form',
            'field'=>'id',
        ),
    ))
    #-- add Palettes ---------------------------------------------------------------------------------------------------
    ->addPaletteGroup('hasSubscribee', array(
        'subscribee_withLimit',
        'subscribee_defaultPlaces',
        'subscribee_withSignOut',
        'subscribee_listFields',
        'subscribee_form'
    ));
}catch(\Exception $e){
    var_dump($e);
}

