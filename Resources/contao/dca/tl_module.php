<?php

/**
 * tl_module - module element definition
 * 
 * @package    subscribee
 * @copyright  HOME - HolsteinMedia
 * @author     Dirk Holstein <dh@holsteinmedia.com>
 *
 */

use Home\PearlsBundle\Resources\contao\Helper\Dca as Helper;

$moduleName = 'tl_module';

$tl_module = new Helper\DcaHelper($moduleName);

$tl_module
    ->addField('select_table', 'subscribee_formular', array('foreignKey' => 'tl_form.title'))
    ->addField('select_article', 'subscribee_bookedUpPage')

#-- eventreader with subscription formular
    ->copyPalette('eventreader','subscribee_eventReaderWithSubscription')
    //->addField('subscribee_eventReaderWithSubscription','config_legend','subscribee_formular')
    //->addField('subscribee_eventReaderWithSubscription','config_legend','subscribee_bookedUpPage')

#-- eventlist with subscription formular
    ->copyPalette('eventreader','subscribee_eventListWithSubscription')
    //->addField('subscribee_eventListWithSubscription','config_legend','subscribee_formular')
    //->addField('subscribee_eventListWithSubscription','config_legend','subscribee_bookedUpPage')
;