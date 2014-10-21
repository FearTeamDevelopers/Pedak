<?php

use App\Etc\Controller as Controller;
use THCFrame\Request\RequestMethods;
use THCFrame\Registry\Registry;

/**
 * Description of App_Controller_Index
 *
 * @author Tomy
 */
class App_Controller_Index extends Controller
{

    /**
     * Method replace specific strings whit their equivalent images
     * @param \App_Model_PageContent $news
     */
    private function _parseNewsBody(\App_Model_News $content, $parsedField = 'body')
    {
        preg_match_all('/\(\!(video|photo|read)_[0-9a-z]+\!\)/', $content->$parsedField, $matches);
        $m = array_shift($matches);

        foreach ($m as $match) {
            $match = str_replace(array('(!', '!)'), '', $match);
            list($type, $id) = explode('_', $match);

            $body = $content->$parsedField;
            if ($type == 'photo') {
                $photo = App_Model_Photo::first(
                                array(
                            'id = ?' => $id,
                            'active = ?' => true
                                ), array('photoName', 'imgMain', 'imgThumb')
                );

                $tag = "<a data-lightbox=\"img\" data-title=\"{$photo->photoName}\" "
                        . "href=\"{$photo->imgMain}\" title=\"{$photo->photoName}\">"
                        . "<img src=\"{$photo->imgThumb}\" height=\"250px\" alt=\"Peďák\"/></a>";

                $body = str_replace("(!photo_{$id}!)", $tag, $body);

                $content->$parsedField = $body;
            }
            
             if ($type == 'video') {
                $video = App_Model_Video::first(
                                array(
                            'id = ?' => $id,
                            'active = ?' => true
                                ), array('title', 'path', 'width', 'height')
                );

                $tag = "<iframe width=\"{$video->width}\" height=\"{$video->height}\" "
                        . "src=\"{$video->path}\" frameborder=\"0\" allowfullscreen></iframe>";

                $body = str_replace("(!video_{$id}!)", $tag, $body);
                $content->$parsedField = $body;
            }

            if ($type == 'read') {
                $tag = "<a href=\"/novinky/r/{$content->getUrlKey()}\" class=\"news-read-more\">[Celý článek]</a>";
                $body = str_replace("(!read_more!)", $tag, $body);
                $content->$parsedField = $body;
            }
        }

        return $content;
    }
    
    /**
     * 
     */
    public function index()
    {
        $view = $this->getActionView();

        $host = RequestMethods::server('HTTP_HOST');

        $canonical = 'http://' . $host;

        $this->getLayoutView()
                ->set('canonical', $canonical)
                ->set('activemenu', 'home');
        
        //get last 4 news
        $news = App_Model_News::all(
                        array('active = ?' => true, 'expirationDate >= ?' => date('Y-m-d H:i:s')), 
                    array('id', 'urlKey', 'author', 'title', 'shortBody', 'created', 'rank'), 
                    array('rank' => 'asc', 'created' => 'DESC'), 4);

        if ($news !== null) {
            foreach ($news as $_news) {
                $this->_parseNewsBody($_news, 'shortBody');
            }
        } else {
            $news = array();
        }

        //get next 4 matches
        $nextMatchA = App_Model_Match::all(
                        array(
                    'active = ?' => true,
                    'startDate >= ?' => date('Y-m-d'),
                    'team = ?' => 'a'
                        ), array('id', 'home', 'away', 'hall', 'startDate', 'startTime'), array('startDate' => 'ASC'), 4
        );

        $nextMatchB = App_Model_Match::all(
                        array(
                    'active = ?' => true,
                    'startDate >= ?' => date('Y-m-d'),
                    'team = ?' => 'b'
                        ), array('id', 'home', 'away', 'hall', 'startDate', 'startTime'), array('startDate' => 'ASC'), 4
        );

        //get last 2 matches results
        $lastMatchA = App_Model_Match::all(
                        array(
                    'active = ?' => true,
                    'team = ?' => 'a',
                    'scoreHome <> ?' => -1,
                    'startDate <= ?' => date('Y-m-d')
                        ), array('id', 'home', 'away', 'startDate', 'hall', 'scoreHome', 'scoreAway', 'startTime'), array('startDate' => 'DESC'), 2
        );
        
        $lastMatchB = App_Model_Match::all(
                        array(
                    'active = ?' => true,
                    'team = ?' => 'b',
                    'scoreHome <> ?' => -1,
                    'startDate <= ?' => date('Y-m-d')
                        ), array('id', 'home', 'away', 'startDate', 'hall', 'scoreHome', 'scoreAway', 'startTime'), array('startDate' => 'DESC'), 2
        );

        //get sponsors
        $sponsors = App_Model_Sponsor::all(
                        array('active = ?' => true), array('title', 'web', 'logo')
        );

        $view->set('news', $news)
                ->set('nextMatchA', $nextMatchA)
                ->set('nextMatchB', $nextMatchB)
                ->set('lastMatchA', $lastMatchA)
                ->set('lastMatchB', $lastMatchB)
                ->set('sponsors', $sponsors);
    }

    /**
     * 
     */
    public function team()
    {
        $view = $this->getActionView();
        $host = RequestMethods::server('HTTP_HOST');

        $canonical = 'http://' . $host . '/team';

        $this->getLayoutView()->set('metatitle', 'Peďák - Team')
                ->set('canonical', $canonical)
                ->set('activemenu', 'team');

        $cache = Registry::get('cache');

        $contentA = $cache->get('teama');

        if ($contentA !== null) {
            $teamA = $contentA;
        } else {
            $teamA = App_Model_User::all(
                            array(
                        'active = ?' => true,
                        'team = ?' => 'a'
                            ), array('id', 'firstname', 'lastname', 'dob',
                        'playerNum', 'cfbuPersonalNum', 'team',
                        'nickname', 'photoMain', 'photoThumb', 'position', 'grip', 'other'), array('lastname' => 'asc')
            );
            $cache->set('teama', $teamA);
        }

        $contentB = $cache->get('teamb');

        if ($contentB !== null) {
            $teamB = $contentB;
        } else {
            $teamB = App_Model_User::all(
                            array(
                        'active = ?' => true,
                        'team = ?' => 'b'
                            ), array('id', 'firstname', 'lastname', 'dob',
                        'playerNum', 'cfbuPersonalNum', 'team',
                        'nickname', 'photoMain', 'photoThumb', 'position', 'grip', 'other'), array('lastname' => 'asc')
            );
            $cache->set('teamb', $teamB);
        }

        $view->set('teamA', $teamA)
                ->set('teamB', $teamB);
    }

    /**
     * 
     */
    public function contact()
    {
        $host = RequestMethods::server('HTTP_HOST');

        $canonical = 'http://' . $host . '/kontakt';

        $this->getLayoutView()->set('metatitle', 'Peďák - Kontakt')
                ->set('canonical', $canonical)
                ->set('activemenu', 'concact');
    }

}
