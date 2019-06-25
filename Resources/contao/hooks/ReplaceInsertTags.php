<?php
/**
 * The ReplaceInsertTags wird beim Antreffen eines unbekannten Insert-Tags ausgeführt
 *
 * Created by PhpStorm.
 * User: dirk
 * Date: 13.12.2017
 * Time: 14:47
 */

namespace Home\SubscribeeBundle\Resources\contao\hooks;
use Home\SubscribeeBundle\Resources\contao\models as Models;

class ReplaceInsertTags
{
    /**
     * @param \
     */
    public function subscribeeInsertTags($strTag)
    {
        $tag = explode('::',$strTag);

        #-- return number of max places
        if ($tag[0] == "ssee_max") {
            return Models\Subscribee::getMaxPlacesByEventId($tag[1]);
        }

        #-- return number of free places
        if ($tag[0] == "ssee_free") {
            return Models\Subscribee::getFreePlacesByEventId($tag[1]);
        }

        #-- return number of reserved places
        if ($tag[0] == "ssee_res") {
            return Models\Subscribee::getReservedPlacesByEventId($tag[1]);
        }

        if ($tag[0] == 'generateForm' && isset($tag[1]) && $tag[1] > 0) {
            return $this->generateForm($tag[1]);
        }

        return false;
    }

    /**
     * generate subscribee form for calender event and set eventId in from
     * @param $eventId
     * @return mixed
     */
    public function generateForm($eventId)
    {
        $objEvent = \CalendarEventsModel::findByIdOrAlias($eventId);
        $objForm = \FormModel::findByIdOrAlias($objEvent->__get('subscribee_form'));

        $strClass = \ContentElement::findClass('form');

        $objForm->typePrefix =  'ce_';

        $objForm->form = $objForm->id;

        $objElement = new $strClass($objForm, 'main');
        $strBuffer = $objElement->generate();

        $strBuffer = str_replace('name="eventId" value="', 'name="eventId" value="'.$eventId, $strBuffer);

        return $strBuffer;
    }
}