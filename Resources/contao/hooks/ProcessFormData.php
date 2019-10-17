<?php
/**
 * The processFormData wird nach dem Abschicken eines Formulars ausgeführt
 *
 * Created by PhpStorm.
 * User: dirk
 * Date: 13.12.2017
 * Time: 14:47
 */

namespace Home\SubscribeeBundle\Resources\contao\hooks;
use Home\SubscribeeBundle\Resources\contao\models as Models;

class ProcessFormData
{
    /**
     * HOOK: doBeforeSendEMail - things to do before send the e-mail
     *
     * if field "places" exists, then the value from this field is taken for the number of places from this subscription
     *
     * @param $arrSubmitted
     * @param $arrFiles
     * @param $intOldId
     * @param $arrForm
     * @param $arrLabels
     * @return mixed
     * @throws \ErrorException
     */
    public function doBeforeSendEMail($arrSubmitted, $arrFiles, $intOldId, $arrForm, $arrLabels)
    {

        if ( $arrFiles['useSubscribee'] == 1 ) {
            #-- mailer info

            $recipientSystem = $GLOBALS['objPage']->__get('adminEmail');
            $subjectCustomer = $arrSubmitted['subjectCustomer'];
            $subjectSystem = $arrSubmitted['subjectSystem'];
            $from = $GLOBALS['objPage']->__get('adminEmail');
            $fromName = $arrSubmitted['fromName'];

            #-- get Event
            $eventId = $arrSubmitted['eventId'] ? $arrSubmitted['eventId'] : basename($_SERVER['REQUEST_URI'],".html");
            $objEvent = \CalendarEventsModel::findByIdOrAlias($eventId);

            if ($objEvent === null) {
                throw new \ErrorException('Event could not be found');
            }

            #-- set the number of places if field exists
            $places =  (key_exists('places', $arrSubmitted) && is_int(intval($arrSubmitted['places'])) && $arrSubmitted['places'] > 0) ? $arrSubmitted['places'] : $places = 1;

            #-- data
            $data = $arrSubmitted;
            if (array_key_exists('FORM_SUBMIT', $data)) {
                unset($data['FORM_SUBMIT']);
            }

            #-- write new subscribee data
            $objSubscribee = new Models\Subscribee();
            $objSubscribee->pid 			= $objEvent->id;
            $objSubscribee->ptable 			= "tl_calendar_events";
            $objSubscribee->tstamp 			= time();
            $objSubscribee->subscribe_data 	= serialize($data);
            $objSubscribee->places 			= $places;
            $objSubscribee->save();

            #-- add additional info to submitted array
            $arrSubmitted['eventId'] 			= $objEvent->id;
            $arrSubmitted['eventTitle'] 		= $objEvent->title;
            $arrSubmitted['eventLocation'] 		= $objEvent->location;
            $startDate = new \Date($objEvent->startDate);
            $startTime = new \Date($objEvent->startTime);
            $arrSubmitted['eventStartTime']		= $startTime->time;
            $arrSubmitted['eventStartDate'] 	= $startDate->date;
            $endDate = new \Date($objEvent->endDate);
            $endTime = new \Date($objEvent->endTime);
            $arrSubmitted['eventEndTime'] 		= $endTime->time;
            $arrSubmitted['eventEndDate'] 		= $endDate->date;

            #-- send mails
            $mailer = new \Contao\Email();
            $mailer->subject = 'Anmeldung zur Veranstaltung:'. $objEvent->title;
            // todo html content for email
            $mailData = "";
            foreach($data as $key=>$value) {
                if ($value !== "") {
                    $mailData .= $key . ': ' . $value . '<br>';
                }
            }

            $mailer->html =
                '<h1>Anmeldung zur Veranstaltung: ' . $objEvent->title . '</h1>' .
                'Ort: ' . $objEvent->location . '<br>' .
                'Datum: ' . $startDate->date . ' - ' . $endDate->date . '<br>' .
                'Uhrzeit: ' . $startTime->time . ' - ' . $endTime->time . '<br>' .
                'Formulardaten <br>' . $mailData
            ;
            $mailer->from = $from;
            $mailer->fromName = $fromName;
            #-- send to user
            $mailer->sendTo($arrSubmitted['email'] );
            #-- send to system
            $mailer->subject = 'Anmeldung zur Veranstaltung:'. $objEvent->title;
            $mailer->sendTo($recipientSystem);
        }
        return $arrSubmitted;
    }
}
