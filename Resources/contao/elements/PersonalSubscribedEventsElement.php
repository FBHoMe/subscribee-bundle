<?php
/**
 * Created by PhpStorm.
 * User: felix
 * Date: 18.09.2017
 * Time: 15:22
 */

namespace Home\SubscribeeBundle\Resources\contao\elements;

use Home\SubscribeeBundle\Resources\contao\models\Subscribee;

class PersonalSubscribedEventsElement extends \ContentElement
{
    /**
     * @var string
     */
    protected $strTemplate = 'ce_personal_sign-in';

    /**
     * @return string
     */
    public function generate()
    {
        return parent::generate();
    }

    /**
     * generate module
     */
    protected function compile()
    {
        if (TL_MODE == 'BE') {
            $this->generateBackend();
        } else {
            $this->generateFrontend();
        }
    }

    /**
     * generate backend for module
     */
    private function generateBackend()
    {
        $this->strTemplate          = 'be_wildcard';
        $this->Template             = new \BackendTemplate($this->strTemplate);
        $this->Template->title      = $this->headline;
        $this->Template->wildcard   = "### Eventliste des Mitglieds ###";
    }

    /**
     * generate frontend for module
     */
    private function generateFrontend()
    {
        if($this->hm_template){
            $this->Template = new \FrontendTemplate($this->hm_template);
        }

        $user = \FrontendUser::getInstance();

        if($user->authenticate()){
            $this->Template->pins = self::getSubscribedEvents($user->id);
        }
    }

    public static function getSubscribedEvents($userId)
    {
        $return = array();
        $events = Subscribee::getAllEventsByMemberId($userId);

        if(is_array($events) && count($events) > 0){
            $return = $events;
        }

        return $return;
    }

}