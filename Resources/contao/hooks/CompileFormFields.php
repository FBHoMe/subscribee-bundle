<?php
/**
 * Created by PhpStorm.
 * User: Felix
 * Date: 06.04.2018
 * Time: 12:22
 */

namespace Home\SubscribeeBundle\Resources\contao\hooks;


use Home\SubscribeeBundle\Resources\contao\models\Subscribee;

class CompileFormFields
{
    public function preFillForm($arrFields, $formId, $me)
    {
        if($formId === 'auto_form_2'){

            if (!isset($_GET['item']) && \Config::get('useAutoItem') && isset($_GET['auto_item'])) {
                \Input::setGet('item', \Input::get('auto_item'));
            }

            $user = \FrontendUser::getInstance();
            $event = $_GET['auto_item'];

            $preFillData = self::getEventPreFillData($user->id, $event);

            if($preFillData){
                foreach ($arrFields AS $objFields) {
                    #--- name ------------------------------------------------------------------------------------------
                    if ($objFields->name == 'name') {
                        $objFields->value = $user->firstname . ' ' . $user->lastname;
                    }
                    #--- status ----------------------------------------------------------------------------------------
                    if ($objFields->name == 'status') {
                        $options = deserialize($objFields->options);

                        if($preFillData['status'] === 'signIn'){
                            $options[0]['default'] = '1';
                        }else{
                            $options[1]['default'] = '1';
                        }
                        $objFields->options = serialize($options);
                    }
                    #--- zimmer ----------------------------------------------------------------------------------------
                    if ($objFields->name == 'zimmer') {
                        $options = deserialize($objFields->options);

                        if($preFillData['zimmer'] === '1'){
                            $options[0]['default'] = '1';
                        }
                        $objFields->options = serialize($options);
                    }
                    #--- vegetarisch -----------------------------------------------------------------------------------
                    if ($objFields->name == 'vegetarisch') {
                        $options = deserialize($objFields->options);

                        if($preFillData['vegetarisch'] === '1'){
                            $options[0]['default'] = '1';
                        }
                        $objFields->options = serialize($options);
                    }
                    #--- anreise / abreise -----------------------------------------------------------------------------
                    if ($objFields->name == 'anreise') {
                        $objFields->value = $preFillData['anreise'];
                    }
                    if ($objFields->name == 'abreise') {
                        $objFields->value = $preFillData['abreise'];
                    }
                    #--- nachricht -------------------------------------------------------------------------------------
                    if ($objFields->name == 'nachricht') {
                        $objFields->value = $preFillData['nachricht'];
                    }
                }
            }else{
                foreach ($arrFields AS $objFields) {
                    if ($objFields->name == 'name') {
                        $objFields->value = $user->firstname . ' ' . $user->lastname;
                    }
                }
            }
        }
        #exit;

        return $arrFields;
    }

    private static function getEventPreFillData($userId, $eventAlias)
    {
        $eventModel = \Contao\CalendarEventsModel::findByIdOrAlias($eventAlias);
        $subscribeeEntries = Subscribee::getAllEventsByMemberId($userId);

        if(is_array($subscribeeEntries) && count($subscribeeEntries) > 0 && array_key_exists($eventModel->id, $subscribeeEntries)){
            return $subscribeeEntries[$eventModel->id]['subscribee_data']['subscribe_data'];
        }

        return array();
    }
}