<?php

/**
 * Broker_Subscribee
 *
 * Wird das überhaupt gebraucht ????
 *
 * @package    subscribee
 * @copyright  HOME - HolsteinMedia
 * @author     Dirk Holstein <dh@holsteinmedia.com>
 */

namespace Home\SubscribeeBundle\Resources\contao\brokers;

class Subscribee extends \Controller
{	
	/**
	 * the constructor
	 */
	public function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * getEventById - return event object
	 *
	 * @param int $id - the article id
     * @throws \Exception
	 * @return \Contao\CalendarEventsModel
	 */
	public function getEventById($id)
	{
		if (!is_int($id) && $id < 1) {
			throw new \Exception ('Parameter ID must be integer and > 0');
		}

		return \Contao\CalendarEventsModel::findByIdOrAlias($id);
	}
}