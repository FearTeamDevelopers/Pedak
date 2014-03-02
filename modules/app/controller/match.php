<?php

use App\Libraries\Controller as Controller;
use THCFrame\Registry\Registry;
use THCFrame\Request\RequestMethods;

/**
 * Description of MatchController
 *
 * @author Tomy
 */
class App_Controller_Match extends Controller {

    /**
     * 
     */
    public function index() {
        $view = $this->getActionView();
        $security = Registry::get("security");

        $matchesA = App_Model_Match::all(
                        array(
                    'team = ?' => 'a',
                    'active = ?' => true
                        ), array(
                    'id', 'home', 'host', 'hall', 'date', 'scoreHome', 'scoreHost'
                        ), "date", "ASC");

        $matchesB = App_Model_Match::all(
                        array(
                    'team = ?' => 'b',
                    'active = ?' => true
                        ), array(
                    'id', 'home', 'host', 'hall', 'date', 'scoreHome', 'scoreHost'
                        ), "date", "ASC");

        $member = $security->isGranted("role_member");

        $view->set("matchesA", $matchesA)
                ->set("matchesB", $matchesB)
                ->set("member", $member);
    }

    /**
     * @before _secured
     */
    public function detail($id) {
        $view = $this->getActionView();
        $security = Registry::get("security");

        $match = App_Model_Match::first(
                        array("id = ?" => $id, 'active = ?' => true));

        if (NULL === $match) {
            $view->flashMessage("Match not found");
            self::redirect("/zapasy");
        }

        $messages = App_MatchChatModel::all(array(
                    "matchId = ?" => $id,
                    "active = ?" => true,
                    "reply = ?" => 0
                        ), array("*"), "created", "asc");

        $view->set("messages", $messages)
                ->set("matchDet", $match);
    }

    /**
     * 
     * @param number $id    matchid
     * @before _secured
     */
    public function addMessage($id) {

        if (RequestMethods::post("sendMessage")) {
            $user = $this->getUser();

            $message = new App_Model_MatchChat(array(
                "matchId" => $id,
                "author" => $user->getWholeName(),
                "title" => RequestMethods::post("title"),
                "body" => RequestMethods::post("body"),
                "reply" => RequestMethods::post("reply")
            ));

            if ($message->validate()) {
                $message->save();
                self::redirect("/zapasy/detail/{$id}");
            }
        }
    }

}