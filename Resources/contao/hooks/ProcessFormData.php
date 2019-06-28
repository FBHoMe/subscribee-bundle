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

            #-- user
            $user = \FrontendUser::getInstance();
            $data['userId'] = $user->id;

            $memberEvents = Models\Subscribee::getAllEventsByMemberId($user->id);

            if(is_array($memberEvents) && count($memberEvents) > 0 && array_key_exists($objEvent->id, $memberEvents)){
                #-- delete existing subscribee data
                $db = \Database::getInstance();
                $sql = '
                    DELETE
                    FROM tl_subscribee
                    WHERE tl_subscribee.subscribe_data LIKE  \'%"userId";s:_:"' . $user->id . '%\'
                    AND pid = ' . $objEvent->id . '
                ';
                $db->prepare($sql)->execute();
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

            #-- Anmeldung / Abmeldung
            if($data['status'] === 'signOut'){
                $statusStr = 'Abmeldung';
            }else{
                $statusStr = 'Anmeldung';
            }

            #-- Anreise / Abreise
            if($data['anreise'] === ''){
                $anreise = ' - ';
            }else{
                $tempdate = new \Date($data['anreise']);
                $anreise = $tempdate->date;
            }
            if($data['abreise'] === ''){
                $abreise = ' - ';
            }else{
                $tempdate = new \Date($data['abreise']);
                $abreise = $tempdate->date;
            }

            #-- send mails
            $mailer = new \Contao\Email();
            $mailer->subject = $subjectCustomer;
            // todo html content for email
            $mailer->html =
                '<h1>'. $statusStr . ' zur Veranstaltung: ' . $objEvent->title . '</h1>' .
                'Mitgleid: ' . $data['name'] . '<br>' .
                'Ort: ' . $objEvent->location . '<br>' .
                'Datum: ' . $startDate->date . ' - ' . $endDate->date . '<br>' .
                'Uhrzeit: ' . $startTime->time . ' - ' . $endTime->time . '<br>' .
                'Benötige ein Zimmer: ' . ($data['zimmer'] === '' ? 'Nein' : 'Ja') . '<br>' .
                'Anreise: ' . $anreise . '<br>' .
                'Abreise: ' . $abreise . '<br>' .
                'Vegetarische Verpflegung erwünscht: ' . ($data['vegetarisch'] === '' ? 'Nein' : 'Ja') . '<br>' .
                'Nachricht: ' . ($data['nachricht'] === '' ? ' - ' : $data['nachricht'])
            ;
            $mailer->from = $from;
            $mailer->fromName = $fromName;
            #-- send to user
            // todo get user email
            $mailer->sendTo($user->email);
            #-- send to system
            $mailer->subject = $subjectSystem;
            $mailer->sendTo($recipientSystem);
        }
        return $arrSubmitted;
    }
}