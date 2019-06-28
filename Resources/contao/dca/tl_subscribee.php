<?php

/**
 * dca definition for tl_subscribee
 * 
 * @package    subscribee
 * @copyright  HOME - HolsteinMedia
 * @author     Dirk Holstein <dh@holsteinmedia.com>
 *
 */

namespace Home\SubscribeeBundle\Resources\contao\dca;

use Home\PearlsBundle\Resources\contao\Helper\Dca\DcaHelper as DcaHelper;
use Home\PearlsBundle\Resources\contao\Helper\DataHelper as DataHelper;

$moduleName = 'tl_subscribee';

$tl_subscribee = new DcaHelper($moduleName);

try{
$tl_subscribee
    ->addConfig('table', array('ptable' => 'tl_calendar_events'))

    ->addList('base', array(
        'sorting' => array (
            'mode'                  => 0,
            'fields'                => array('tstamp DESC'),
            'panelLayout'           => 'filter;sort,search,limit'
        ),
        'label' => array (
            'fields'                => array('tstamp'),
            'label_callback'        => array('Home\SubscribeeBundle\Resources\contao\dca\tl_subscribee', 'labelCallback'),
        )))

    ->addGlobalOperation('all')
    ->addGlobalOperation(null, 'export', array(
            'href'			=> 'key=export',
            'class'			=> 'header_xls_export',
            'icon'          => 'expand.gif',
            'attributes'	=> 'onclick="Backend.getScrollOffset();"'
    ))

    ->addOperation('delete')
    ->addOperation('show')

    ->addField('id', 'id')
    ->addField('pid', 'pid', array(
        'foreignKey' => 'tl_calendar_events.id',
    ))
    ->addField('tstamp', 'tstamp')
    ->addField('integer','places')
    ->addField(blob, 'subscribe_data');

}catch(\Exception $e){
    var_dump($e);
}

class tl_subscribee extends \Backend
{
    public function labelCallback($row, $label)
    {
        #-- get event data
        if (!key_exists('pid', $row) && $row['pid'] < 1) {
            throw Exception('Event-Id is missing in tl_subscribee');
        }

        #-- handle data
        $data = DataHelper::deserialize($row['subscribe_data']);
        $strPlaces = ($row['places'] < 2) ? $GLOBALS['TL_LANG']['tl_subscribee']['place'] : $GLOBALS['TL_LANG']['tl_subscribee']['places'];
        $date = new \Date($row['tstamp']);

        #-- handle fields for list view
        $fieldnames = array();
        $calendarEvents = \CalendarEventsModel::findByIdOrAlias($row['pid']);

        if ($calendarEvents instanceof \CalendarEventsModel) {
            $calendar = \CalendarModel::findByPk($calendarEvents->pid);

            if ($calendar instanceof \CalendarModel) {
                #-- get field names
                if ($calendar->subscribee_listFields != "") {
                    $fieldnames = explode(',', $calendar->subscribee_listFields);
                } else {
                    if (array_key_exists('name', $data)) {
                        $fieldnames[] = 'name';
                    }
                    if (array_key_exists('email', $data)) {
                        $fieldnames[] = 'email';
                    }
                    if (array_key_exists('company', $data)) {
                        $fieldnames[] = 'company';
                    }
                    if (array_key_exists('phone', $data)) {
                        $fieldnames[] = 'phone';
                    }
                }

                if ($calendar->subscribee_withSignOut === "1") {
                    $fieldnames[] = 'status';
                }

                #-- get field values
                $v = array();
                foreach ($fieldnames as $field) {
                    if (array_key_exists($field, $data)) {
                        $v[] = $data[$field];
                    }
                }

                $vString = (count($v) < 1) ? "" : implode(', ', $v);

                return '<span style="color:#b3b3b3;padding-right:3px">[' . $date->datim . ']</span> ' . substr($vString, 0, 60) . ': ' . $row['places'] . ' ' . $strPlaces;
            }
        }
    }
}