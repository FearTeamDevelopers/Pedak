<?php

use App\Etc\Controller as Controller;

/**
 * Description of IndexController
 *
 * @author Tomy
 */
class App_Controller_Index extends Controller
{

    /**
     * 
     */
    public function index()
    {
        $view = $this->getActionView();

        //get last 4 news
        $news = App_Model_News::all(
                        array('active = ?' => true), 
                array('id', 'author', 'title', 'body', 'created'), 
                array('created' => 'DESC'), 4
        );

        $view->set('news', $news);

        //get next 4 matches
        $nextMatchA = App_Model_Match::all(
                        array(
                    'active = ?' => true,
                    'date >= ?' => date('Y-m-d'),
                    'team = ?' => 'a'
                        ), 
                array('id', 'home', 'host', 'hall', 'date'), 
                array('date' => 'ASC'), 4
        );

        $nextMatchB = App_Model_Match::all(
                        array(
                    'active = ?' => true,
                    'date >= ?' => date('Y-m-d'),
                    'team = ?' => 'b'
                        ), 
                array('id', 'home', 'host', 'hall', 'date'), 
                array('date' => 'ASC'), 4
        );

        $view->set('nextMatchA', $nextMatchA)
                ->set('nextMatchB', $nextMatchB);

        //get last 2 matches results
        $lastMatch = App_Model_Match::all(
                        array(
                    'active = ?' => true,
                    'scoreHome <> ?' => -1,
                    'date <= ?' => date('Y-m-d')
                        ), 
                array('id', 'home', 'host', 'date', 'hall', 'scoreHome', 'scoreHost'), 
                array('date' => 'DESC'), 2
        );

        $view->set('lastMatch', $lastMatch);

        //get gelleries
        //get sponsors
        $sponsors = App_Model_Sponsor::all(
                        array('active = ?' => true), 
                array('title', 'url', 'logo')
        );

        $view->set('sponsors', $sponsors);
    }

    /**
     * 
     * @param type $id
     */
    public function newsDetail($id)
    {
        $view = $this->getActionView();
        $view->set('photos', array());

        $news = App_Model_News::first(
                        array(
                            'id = ?' => $id,
                            'active = ?' => true
        ));

        if ($news) {
            $photosId = App_Model_NewsPhoto::all(
                            array(
                        'newsId = ?' => $id,
                        'active = ?' => true
                            ), array('photoId'));

            if ($photosId) {
                $ids = array();
                foreach ($photosId as $photo) {
                    $ids[] = $photo->photoId;
                }

                $photos = App_Model_Photo::all(
                                array(
                                    'id in ?' => $ids,
                                    'active = ?' => TRUE
                ));
                $view->set('photos', $photos);
            }
        }

        $view->set('news', $news);
    }

}
