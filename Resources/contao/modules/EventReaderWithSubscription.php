<?php

/**
 * EventReaderWithSubscription - an event reader module with the subscribing possibility
 * 
 * @package    subscribee
 * @copyright  HOME - HolsteinMedia
 * @author     Dirk Holstein <dh@holsteinmedia.com>
 */

namespace Home\SubscribeeBundle\Resources\contao\modules;
use Home\PearlsBundle\Resources\contao\Helper\DataHelper;
use Home\SubscribeeBundle\Resources\contao\models as Model;

class EventReaderWithSubscription extends \ModuleEventReader
{
	/**
	 * Template
	 * @var string
	 */
    protected $strTemplate = 'mod_eventreader';
	
	
	/**
	 * Do not display the module if there are no menu items
	 * @return string
	 */
	public function generate()
	{
		return parent::generate();
	}
	
	
	/**
	 * Generate the module
	 */
	protected function compile()
	{
		parent::compile();


		#-- get the event id - das Model wird zwar in der parent class \ModuleEventReader schon geladen, jedoch habe ich hier im Chield keinen Zugriff darauf :-(
		$objEvent = \CalendarEventsModel::findPublishedByParentAndIdOrAlias(\Input::get('events'), $this->cal_calendar);
		$this->Template->event = $objEvent->row();
		
		#-- plan the form output
		$this->Template->enableSubscribee = Model\Subscribee::getSubscribeeEnabledStatus($objEvent->id);
		$this->Template->subscribeeForm = Model\Subscribee::getSubscribeeForm($objEvent->pid);
		#$this->Template->category = Model\Subscribee::getCategory($objEvent->id);
		$this->Template->loadPage = null;
		$this->Template->formularId = null;
		$this->Template->freePlaces = Model\Subscribee::getFreePlacesByEventId($objEvent->id);
		$this->Template->maxPlaces = Model\Subscribee::getMaxPlacesByEventId($objEvent->id);
		$this->Template->resPlaces = Model\Subscribee::getReservedPlacesByEventId($objEvent->id);
		$start = ($objEvent->startTime) ? $objEvent->startTime : $objEvent->startDate;
		if ($start >= time()) {
			if (Model\Subscribee::getFreePlacesByEventId($objEvent->id) > 0) {
				$this->Template->formularId = $this->subscribee_formular;
			} else if ($this->subscribee_bookedUpPage > 0) {
				$this->Template->articleId = $this->subscribee_bookedUpPage;
			}
		}

		$user = \FrontendUser::getInstance();
		$this->Template->loggedIn = \FrontendUser::getInstance()->authenticate();
		$this->Template->singedIn = self::getUserSignInStatus($user->id, $objEvent->id);
		$this->Template->pin = DataHelper::convertValue($objEvent->row());
	}

	public static function getUserSignInStatus($userId, $eventId)
    {
        $return = false;

        if($userId && $userId > 0 && $eventId && $eventId > 0){
            $participants = Model\Subscribee::getAllParticipantsByEventId($eventId);
            if(is_array($participants) && count($participants) > 0){
                foreach ( $participants as $p){
                    if($p['subscribe_data']['userId'] === $userId){
                        $return = true;
                    }
                }
            }
        }

        return $return;
    }
}
