<?php
/**
 * Created by PhpStorm.
 * User: felix
 * Date: 18.09.2017
 * Time: 15:22
 */

namespace Home\SubscribeeBundle\Resources\contao\elements;

use Home\SubscribeeBundle\Resources\contao\models\Subscribee;

class SignOutDetailElement extends \ContentElement
{
    /**
     * @var string
     */
    protected $strTemplate = 'cte_sign_out_detail';

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
        $this->Template->wildcard   = "### Sign-Out Detailansicht ###";
    }

    /**
     * generate frontend for module
     */
    private function generateFrontend()
    {
        if (!isset($_GET['item']) && \Config::get('useAutoItem') && isset($_GET['auto_item'])) {
            \Input::setGet('item', \Input::get('auto_item'));
        }

        if($_GET['item']){

            $eventAlias = $_GET['item'];
            $eventModel = \Contao\CalendarEventsModel::findByIdOrAlias($eventAlias);

            if($eventModel instanceof \Contao\CalendarEventsModel){
                $this->Template->pins = self::getSignedOutUserList($eventModel->id);
            }
        }
    }

    public static function getSignedOutUserList($eventId)
    {
        $return = array();
        $participants = Subscribee::getAllParticipantsByEventId($eventId);

        if(is_array($participants) && count($participants) > 0){
            foreach ($participants as $p){
                if($p['subscribe_data']['status'] === 'signOut'){
                    $return[] = $p['subscribe_data']['name'];
                }
            }
        }

        return $return;
    }
}