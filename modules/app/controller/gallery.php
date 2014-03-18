<?php

use App\Etc\Controller as Controller;

/**
 * Description of GalleryController
 *
 * @author Tomy
 */
class App_Controller_Gallery extends Controller
{

    /**
     * 
     */
    public function index()
    {
        $view = $this->getActionView();

        $galleries = App_Model_Gallery::all(
                        array('active = ?' => true), 
                array('*'), 
                array('created' => 'ASC')
        );

        $view->set('galleries', $galleries);
    }

    /**
     * 
     */
    public function detail($id)
    {
        $view = $this->getActionView();

        $gallery = App_Model_Gallery::first(array(
                    'id = ?' => $id
        ));

        if (NULL === $gallery) {
            $view->flashMessage('Gallery not found');
            self::redirect('/galerie');
        } else {
            $photos = App_Model_Photo::all(array(
                        'galleryId = ?' => $id
            ));

            $view->set('gallery', $gallery)
                    ->set('photos', $photos);
        }
    }

}
