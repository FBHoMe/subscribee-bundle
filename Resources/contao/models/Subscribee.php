<?php

/**
 * Subscribee - the subscribee model
 * 
 * @package    subscribee
 * @copyright  HOME - HolsteinMedia
 * @author     Dirk Holstein <dh@holsteinmedia.com>
 */

namespace Home\SubscribeeBundle\Resources\contao\models;

use Home\PearlsBundle\Resources\contao\Helper\DataHelper;

class Subscribee extends \Contao\Model
{
    /**
     * Table name
     * @var string
     */
    protected static $strTable = 'tl_subscribee';

	public static function getAllEventsByMemberId($memberId)
    {
        $return = array();
        if($memberId && $memberId > 0){
            $db = \Database::getInstance();
            $sql = '
                SELECT *
                FROM tl_subscribee
                WHERE tl_subscribee.subscribe_data LIKE  \'%"userId";s:_:"' . $memberId . '%\'
            ';
            $result = $db->prepare($sql)->execute()->fetchAllAssoc();
            $eventIds = array();
            if(is_array($result) && count($result) > 0){
                foreach ($result as $row){
                    $return[$row['pid']]['subscribee_data'] = DataHelper::convertValue($row);
                    $eventIds[$row['pid']] = $row['pid'];
                }
            }
            if(is_array($eventIds) && count($eventIds) > 0){
                $events = \Contao\CalendarEventsModel::findBy(array(
                    \Contao\CalendarEventsModel::getTable() . '.id IN (' . implode(',', $eventIds) . ') '
                ), null);
                if($events instanceof \Contao\Model\Collection){
                    $eventsResult = DataHelper::convertValue($events->fetchAll());
                    if(is_array($eventsResult) && count($eventsResult) > 0){
                        foreach ($eventsResult as $event){
                            $return[$event['id']]['event'] = $event;
                        }
                    }
                }
            }
        }
        return $return;
    }

    /**
     * get all participants of an event
     * @param $eventId
     * @return array
     */
	public static function getAllParticipantsByEventId($eventId)
    {
        $return = array();

        if($eventId && $eventId > 0){
            $db = \Database::getInstance();
            $sql = '
                SELECT *
                FROM tl_subscribee
                WHERE tl_subscribee.pid = ' . $eventId . '
            ';
            $result = $db->prepare($sql)->execute()->fetchAllAssoc();
            if(is_array($result) && count($result) > 0){
                $return = DataHelper::convertValue($result);
            }
        }

        return $return;
    }

    /**
     * return the number of free places
     * @param int $eventId
     * @return bool|int
     */
	public static function getFreePlacesByEventId($eventId)
	{
		$eventId = self::_checkEventId($eventId);
		
		$maxPlaces = self::getMaxPlacesByEventId($eventId);
		$resPlaces = self::getReservedPlacesByEventId($eventId);
		
		if (is_int($maxPlaces) && $maxPlaces >= 0 && is_int($resPlaces) && $resPlaces >= 0) {
			return $maxPlaces - $resPlaces;
		} else {
			return false;
		}
	}

    /**
     * return the number of maximal places
     * @param int $eventId
     * @return int
     */
	public static function getMaxPlacesByEventId($eventId)
	{
		$eventId = self::_checkEventId($eventId);
		
		$obj = \Database::getInstance()
			->prepare('
				SELECT subscribee_places
				FROM tl_calendar_events
				WHERE id = '.$eventId
			)
			->execute();
		
		return (int) $obj->subscribee_places; 
	}

    /**
     * return the number of reserved places
     * @param int $eventId
     * @return int
     */
	public static function getReservedPlacesByEventId($eventId)
	{
		$eventId = self::_checkEventId($eventId);
		
		$obj = \Database::getInstance()
				->prepare('
					SELECT sum(places) as sum
					FROM '.static::$strTable.'
					WHERE pid = '.$eventId
				)
				->execute();
		
		return (int) $obj->sum;
	}

	public static function getSignedInOutPlacesByEventId($eventId, $status)
    {
        $eventId = self::_checkEventId($eventId);

        $db = \Database::getInstance();
        $sql = 'SELECT subscribe_data FROM tl_subscribee WHERE pid = ' . $eventId;
        $result = $db->prepare($sql)->execute()->fetchAllAssoc();

        if(is_array($result) && count($result) > 0){
            $count = 0;
            foreach ($result as $row){
                $data = deserialize($row['subscribe_data']);
                if($data['status'] === $status){
                    $count++;
                }
            }
            if($count > 0){
                return $count;
            }
        }

        return false;
    }

	/**
	 * return true if Subscribee is enables else false
	 * @param int $eventId
	 * @return bool
	 */
	public static function getSubscribeeEnabledStatus($eventId)
	{
		$eventId = self::_checkEventId($eventId);

		$obj = \Database::getInstance()
			->prepare('
					SELECT enable_subscribee as enable_subscribee
					FROM tl_calendar_events
					WHERE id = '.$eventId
			)
			->execute();

		return $obj->enable_subscribee;
	}

    /**
     * @param $eventPid
     * @return mixed|null
     */
	public static function getSubscribeeForm($eventPid)
    {
        $eventPid = self::_checkEventId($eventPid);

        $obj = \Database::getInstance()
            ->prepare('
                SELECT subscribee_form as subscribee_form
                FROM tl_calendar
                WHERE id = '.$eventPid
            )
            ->execute();

        return $obj->subscribee_form;
    }

	/**
	 * return taxonomies category
	 * @param int $eventId
	 * @return bool
	 */
	public static function getCategory($eventId)
	{
		$eventId = self::_checkEventId($eventId);

		$obj = \Database::getInstance()
			->prepare('
					SELECT category as category
					FROM tl_calendar_events
					WHERE id = '.$eventId
			)
			->execute();

		return $obj->category;
	}
	
	/**
	 * check the event id and return an int
	 * @param int|string $eventId
	 * @return int
	 */
	protected static function _checkEventId($eventId)
	{
		if (is_string($eventId)) {
			$eventId = intval($eventId);
		}
		
		if (!is_int($eventId) || $eventId < 0) {
			return null;
		}
		
		return $eventId;
	}
}
