<?php

use App\Libraries\Controller as Controller;
use THCFrame\Registry\Registry;

/**
 * Description of TeamController
 *
 * @author Tomy
 */
class App_Controller_Team extends Controller {

    /**
     * 
     */
    public function index() {
        $view = $this->getActionView();

        $teamA = App_Model_User::all(
                        array(
                    'active = ?' => true,
                    'team = ?' => 'a'
                        ), array(
                    'id', 'firstname', 'lastname', 'dob',
                    'playerNum', 'cfbuPersonalNum', 'team',
                    'nickname', 'photo', 'position', 'grip', 'other'
                        ), 'lastname', 'asc');

        $view->set("teamA", $teamA);

        $teamB = App_Model_User::all(
                        array(
                    'active = ?' => true,
                    'team = ?' => 'b'
                        ), array(
                    'id', 'firstname', 'lastname', 'dob',
                    'playerNum', 'cfbuPersonalNum', 'team',
                    'nickname', 'photo', 'position', 'grip', 'other'
                        ), 'lastname', 'asc');

        $view->set("teamB", $teamB);
    }

}