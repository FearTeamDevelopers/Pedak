<?php

use App\Etc\Controller as Controller;
use THCFrame\Registry\Registry;
use THCFrame\Request\RequestMethods;

/**
 * Description of App_Controller_Gallery
 *
 * @author Tomy
 */
class App_Controller_Gallery extends Controller
{

    /**
     * 
     * @param type $year
     */
    public function index($year = null)
    {
        $view = $this->getActionView();
        $layoutView = $this->getLayoutView();

        if ($year == null) {
            $year = date('Y');
            $canonical = 'http://' . $this->getServerHost() . '/gallerie';
        } else {
            $canonical = 'http://' . $this->getServerHost() . '/gallerie/' . $year;
        }

        $content = $this->getCache()->get('galerie');
        $contentYears = $this->getCache()->get('galerie-year');

        if ($content !== null && $contentYears !== null) {
            $galleries = $content;
            $years = $contentYears;
        } else {
            $galleries = App_Model_Gallery::fetchGalleriesByYear($year);
            $this->getCache()->set('galerie', $galleries);
            
            $galleryYears = App_Model_Gallery::all(
                    array('active = ?' => true), 
                    array('DISTINCT(EXTRACT(YEAR FROM created))' => 'year'), 
                    array('year' => 'ASC')
            );

            $years = array();

            foreach ($galleryYears as $gallery) {
                $years[] = $gallery->getYear();
            }
        }
        
        $view->set('galleries', $galleries)
                ->set('years', $years);

        $layoutView->set('canonical', $canonical)
            ->set('metatitle', 'Peďák - Galerie');
    }

    /**
     * 
     * @param type $urlkey
     */
    public function detail($urlkey)
    {
        $view = $this->getActionView();
        $layoutView = $this->getLayoutView();

        $gallery = App_Model_Gallery::fetchActivePublicGalleryByUrlkey($urlkey);
        
        if($gallery !== null){
            $canonical = 'http://' . $this->getServerHost() . '/galerie/r/' . $urlkey;
            $layoutView->set('canonical', $canonical)
                    ->set('metatitle', 'Peďák - '.$gallery->getTitle());
        }
        
        $view->set('gallery', $gallery);
    }

}
