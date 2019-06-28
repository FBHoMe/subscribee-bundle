<?php
/**
 * @package    subscribee
 * @copyright  HOME - HolsteinMedia
 * @author     Dirk Holstein <dh@holsteinmedia.com>
 *
 */

use Home\PearlsBundle\Resources\contao\Helper\Dca as Helper;

$moduleName = 'tl_subscribee_data';

$tl_subscribee_data = new Helper\DcaHelper($moduleName);

$GLOBALS['TL_DCA'][$moduleName] = [
    'palettes' => [
        '__selector__' => [],
    ],
    'subpalettes' => [
        '' => ''
    ]
];

$tl_subscribee_data
    #-- Config
    ->addConfig('liste', array('ptable' => 'tl_personalee_portfolio'))
    #-- List
    ->addList('base')
    #-- Sorting
    ->addSorting('liste')
    #-- Fields -----------------------------------------------------------------------------------------------------
    ->addField('id', 'id')
    ->addField('pid', 'pid', array('foreignKey' => 'tl_productee_portfolio.id'))
    ->addField('alias','alias')
    ->addField('tstamp', 'tstamp')
    ->addField('name', 'name')

    ->addField('text', 'note')
    ->addField('text', 'eventId')
    ->addField('text', 'eventTitle')
    ->addField('text', 'eventLocation')
    ->addField('text', 'eventStartTime')
    ->addField('text', 'eventStartDate')
    ->addField('text', 'eventEndTime')
    ->addField('text', 'eventEndDate')
    #-- Palettes ---------------------------------------------------------------------------------------------------
    ->addPaletteGroup('data', array('note','eventId','eventTitle','eventLocation','eventStartTime',
        'eventStartDate','eventEndTime','eventEndDate'))
    #-- Operations
    ->addOperation('edit', 'edit', array(),'_first')
    ->addOperation('copy')
    ->addOperation('delete','delete',array('attributes' => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;"'))
    ->addOperation('show')
;

