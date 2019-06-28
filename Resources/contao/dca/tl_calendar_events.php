<?php

/**
 * tl_calendar_events_subscribee - dca definitions for subscribee additional fields
 * 
 * @package    subscribee
 * @copyright  HOME - HolsteinMedia
 * @author     Dirk Holstein <dh@holsteinmedia.com>
 *
 */

namespace Home\SubscribeeBundle\Resources\contao\dca;

use Home\PearlsBundle\Resources\contao\Helper\Dca as Helper;

$moduleName = 'tl_calendar_events';

try{
    $tl_calendar_event_subscribee = new Helper\DcaHelper($moduleName);
}catch(\Exception $e){
    var_dump($e);
}

$GLOBALS['TL_DCA'][$moduleName]['config']['ctable'][] = "tl_subscribee";

$GLOBALS['TL_DCA'][$moduleName]['config']['onload_callback'][] = array('Home\SubscribeeBundle\Resources\contao\dca\tl_calendar_events', 'setDca');
$GLOBALS['TL_DCA'][$moduleName]['config']['oncreate_callback'][] = array('Home\SubscribeeBundle\Resources\contao\dca\tl_calendar_events', 'setDefaultValues');
$GLOBALS['TL_DCA']['tl_calendar_events'] ['list']['sorting']['child_record_callback'] = array('Home\SubscribeeBundle\Resources\contao\dca\tl_calendar_events', 'setLabel');
//$GLOBALS['TL_DCA'][$moduleName]['config']['onload_callback'][] = array(get_class($this),'onLoadCallback');
//$GLOBALS['TL_DCA'][$moduleName]['list']['sorting']['child_record_callback'] = array(get_class($this),'childRecordCallback');

try{
$tl_calendar_event_subscribee
    ->addField('checkbox','enable_subscribee', array('default'=> true))

    /** TODO: Prüfung auf ganze, positive Zahl */
    ->addField('text', 'subscribee_places', array(
        //'save_callback'=>array(array(get_class($this),'saveCallbackPlaces'))
    ))
    ->addField('checkbox', 'subscribee_withLimit')
    ->addField('checkbox', 'subscribee_withSignOut')

    ->addOperation(null,'bookings',array(
            'label'     => 'bookings',
            'href'      => 'table=tl_subscribee',
            'icon'      => 'bundles/homesubscribee/ico_subscribee.png'
        ),
        4);

}catch(\Exception $e){
    var_dump($e);
}

class tl_calendar_events extends \Backend
{
    public static function setLabel($arrRow)
    {
        $span = \Calendar::calculateSpan($arrRow['startTime'], $arrRow['endTime']);

        if ($span > 0)
        {
            $date = \Date::parse($GLOBALS['TL_CONFIG'][($arrRow['addTime'] ? 'datimFormat' : 'dateFormat')], $arrRow['startTime']) . ' - ' . \Date::parse($GLOBALS['TL_CONFIG'][($arrRow['addTime'] ? 'datimFormat' : 'dateFormat')], $arrRow['endTime']);
        }
        elseif ($arrRow['startTime'] == $arrRow['endTime'])
        {
            $date = \Date::parse($GLOBALS['TL_CONFIG']['dateFormat'], $arrRow['startTime']) . ($arrRow['addTime'] ? ' ' . \Date::parse($GLOBALS['TL_CONFIG']['timeFormat'], $arrRow['startTime']) : '');
        }
        else
        {
            $date = \Date::parse($GLOBALS['TL_CONFIG']['dateFormat'], $arrRow['startTime']) . ($arrRow['addTime'] ? ' ' . \Date::parse($GLOBALS['TL_CONFIG']['timeFormat'], $arrRow['startTime']) . '-' . \Date::parse($GLOBALS['TL_CONFIG']['timeFormat'], $arrRow['endTime']) : '');
        }

        // subscribe code - add the number of reserved and maximum places
        $places = "";

        if ($arrRow['enable_subscribee'] === '1' && $arrRow['subscribee_withSignOut'] === '' && $arrRow['subscribee_withLimit'] === '1' ) {
            $resPlaces = \Home\SubscribeeBundle\Resources\contao\models\Subscribee::getReservedPlacesByEventId($arrRow['id']);
            $resPlaces = ($resPlaces)?:"-";
            $maxPlaces = \Home\SubscribeeBundle\Resources\contao\models\Subscribee::getMaxPlacesByEventId($arrRow['id']);
            $maxPlaces = ($maxPlaces)?:"-";
            $places = " (".$resPlaces."/".$maxPlaces.")";
            return '<div class="tl_content_left">' . $arrRow['title'] .$places. ' <span style="color:#b3b3b3;padding-left:3px">[' . $date . ']</span></div>';
        }

        if ($arrRow['enable_subscribee'] === '1' && $arrRow['subscribee_withSignOut'] === '' && $arrRow['subscribee_withLimit'] === '' ) {
            $resPlaces = \Home\SubscribeeBundle\Resources\contao\models\Subscribee::getReservedPlacesByEventId($arrRow['id']);
            $resPlaces = ($resPlaces)?:"-";
            $places = " (Angemeldet: ".$resPlaces." )";
            return '<div class="tl_content_left">' . $arrRow['title'] .$places. ' <span style="color:#b3b3b3;padding-left:3px">[' . $date . ']</span></div>';
        }

        if ($arrRow['enable_subscribee'] === '1' && $arrRow['subscribee_withSignOut'] === '1' && $arrRow['subscribee_withLimit'] === '' ) {
            $resPlaces = \Home\SubscribeeBundle\Resources\contao\models\Subscribee::getReservedPlacesByEventId($arrRow['id']);
            $resPlaces = ($resPlaces)?:"-";
            $singedOut = \Home\SubscribeeBundle\Resources\contao\models\Subscribee::getSignedInOutPlacesByEventId($arrRow['id'],'signOut');
            $singedOut = ($singedOut)?:"-";
            $singedIn = \Home\SubscribeeBundle\Resources\contao\models\Subscribee::getSignedInOutPlacesByEventId($arrRow['id'],'signIn');
            $singedIn = ($singedIn)?:"-";
            $places = " (Angemeldet: ".$singedIn." | Abgemeldet: ".$singedOut." )";
            return '<div class="tl_content_left">' . $arrRow['title'] .$places. ' <span style="color:#b3b3b3;padding-left:3px">[' . $date . ']</span></div>';
        }

        if ($arrRow['enable_subscribee'] === '1' && $arrRow['subscribee_withSignOut'] === '1' && $arrRow['subscribee_withLimit'] === '1' ) {
            $resPlaces = \Home\SubscribeeBundle\Resources\contao\models\Subscribee::getReservedPlacesByEventId($arrRow['id']);
            $resPlaces = ($resPlaces)?:"-";
            $singedOut = \Home\SubscribeeBundle\Resources\contao\models\Subscribee::getSignedInOutPlacesByEventId($arrRow['id'],'signOut');
            $singedOut = ($singedOut)?:"-";
            $maxPlaces = \Home\SubscribeeBundle\Resources\contao\models\Subscribee::getMaxPlacesByEventId($arrRow['id']);
            $maxPlaces = ($maxPlaces)?:"-";
            $singedIn = \Home\SubscribeeBundle\Resources\contao\models\Subscribee::getSignedInOutPlacesByEventId($arrRow['id'],'signIn');
            $singedIn = ($singedIn)?:"-";
            $places = " (Angemeldet: ".$singedIn."/".$maxPlaces." | Abgemeldet: ".$singedOut." )";
            return '<div class="tl_content_left">' . $arrRow['title'] .$places. ' <span style="color:#b3b3b3;padding-left:3px">[' . $date . ']</span></div>';
        }

        return '<div class="tl_content_left">' . $arrRow['title'] . ' <span style="color:#b3b3b3;padding-left:3px">[' . $date . ']</span></div>';
    }

    public static function getModelInstance($strTable, $intId)
    {
        $strItemClass = \Model::getClassFromTable($strTable);

        return class_exists($strItemClass) ? $strItemClass::findByPk($intId) : null;
    }

    public static function setDefaultValues($strTable, $intId, $arrRow, $dc)
    {
        $objModel = static::getModelInstance($strTable, $intId);

        if($objModel instanceof \Contao\CalendarEventsModel){
            $parentModel = static::getModelInstance('tl_calendar', $objModel->pid);

            if($parentModel instanceof \Contao\CalendarModel){
                $objModel->__set('subscribee_withLimit', $parentModel->subscribee_withLimit);
                $objModel->__set('subscribee_withSignOut', $parentModel->subscribee_withSignOut);
                $objModel->__set('subscribee_places', $parentModel->subscribee_defaultPlaces > 0 ? $parentModel->subscribee_defaultPlaces : 0);
                $objModel->save();
            }
        }
    }

    public function setDca($dc)
    {
        $paletteFields = array('enable_subscribee');
        $calendarEventsModel = \Contao\CalendarEventsModel::findByIdOrAlias($dc->id);

        if ($calendarEventsModel instanceof \Contao\CalendarEventsModel) {
            $calendarModel = \Contao\CalendarModel::findByIdOrAlias($calendarEventsModel->pid);

            if ($calendarModel instanceof \Contao\CalendarModel) {
                if ($calendarModel->subscribee_withLimit === '1') {
                    $paletteFields[] = 'subscribee_withLimit';
                    $paletteFields[] = 'subscribee_places';
                }
                if ($calendarModel->subscribee_withSignOut === '1') {
                    $paletteFields[] = 'subscribee_withSignOut';
                }
            }
        }

        $tl_calendar_event_subscribee = new Helper\DcaHelper('tl_calendar_events');
        $tl_calendar_event_subscribee->addPaletteGroup('subscribee', $paletteFields,'default', -1);
    }
}
