<?php

use App\Etc\Controller as Controller;
use THCFrame\Registry\Registry;
use THCFrame\Request\RequestMethods;

/**
 * Description of App_Controller_Match
 *
 * @author Tomy
 */
class App_Controller_Match extends Controller
{

    /**
     * 
     */
    public function index()
    {
        $view = $this->getActionView();
        $host = RequestMethods::server('HTTP_HOST');

        $canonical = 'http://' . $host . '/zapasy';

        $this->getLayoutView()->set('metatitle', 'Peďák - Zápasy')
                ->set('canonical', $canonical);

        $cache = Registry::get('cache');

        $contentA = $cache->get('matchesa');

        if ($contentA !== null) {
            $matchesA = $contentA;
        } else {
            $matchesA = App_Model_Match::all(
                            array(
                        'team = ?' => 'a',
                        'active = ?' => true
                            ), array('id', 'home', 'away', 'hall', 'date', 'scoreHome', 'scoreAway'), array('date' => 'ASC')
            );
            $cache->set('matchesa', $matchesA);
        }

        $contentB = $cache->get('matchesb');

        if ($contentB !== null) {
            $matchesB = $contentB;
        } else {
            $matchesB = App_Model_Match::all(
                            array(
                        'team = ?' => 'b',
                        'active = ?' => true
                            ), array('id', 'home', 'away', 'hall', 'date', 'scoreHome', 'scoreAway'), array('date' => 'ASC')
            );
            $cache->set('matchesb', $matchesB);
        }

        $view->set('matchesA', $matchesA)
                ->set('matchesB', $matchesB);
    }

    /**
     * @before _secured
     */
    public function detail($id)
    {
        $view = $this->getActionView();
        $host = RequestMethods::server('HTTP_HOST');

        $canonical = 'http://' . $host . '/zapasy';

        $this->getLayoutView()->set('metatitle', 'Peďák - Detail zápasu')
                ->set('canonical', $canonical);

        $match = App_Model_Match::first(array('id = ?' => (int) $id, 'active = ?' => true));

        if (NULL === $match) {
            $view->warningMessage('Match not found');
            self::redirect('/zapasy');
        }

        $messages = App_Model_MatchChat::all(array(
                    'matchId = ?' => $id,
                    'active = ?' => true,
                    'reply = ?' => 0
                        ), array('*'), array('created' => 'asc'));

        $view->set('messages', $messages)
                ->set('matchDet', $match);
    }

    /**
     * 
     * @param number $id    matchid
     * @before _secured
     */
    public function addMessage($id)
    {
        if (RequestMethods::post('sendMessage')) {
            $user = $this->getUser();

            $message = new App_Model_MatchChat(array(
                'matchId' => $id,
                'author' => $user->getWholeName(),
                'title' => RequestMethods::post('title'),
                'body' => RequestMethods::post('body'),
                'reply' => RequestMethods::post('reply')
            ));

            if ($message->validate()) {
                $message->save();
                self::redirect('/zapasy/detail/{$id}');
            }
        }
    }

}
