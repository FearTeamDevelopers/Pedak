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
        $host = RequestMethods::server('HTTP_HOST');
        $cache = Registry::get('cache');

        if ($year == null) {
            $year = date('Y');
            $canonical = 'http://' . $host . '/gallerie';
        } else {
            $canonical = 'http://' . $host . '/gallerie/' . $year;
        }

        $content = $cache->get('galerie');
        $contentYears = $cache->get('galerie-year');

        if ($content !== null && $contentYears !== null) {
            $galleries = $content;
            $years = $contentYears;
        } else {
            $galleries = App_Model_Gallery::fetchGalleriesByYear($year);
            $cache->set('galerie', $galleries);
            
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
        $host = RequestMethods::server('HTTP_HOST');

        $gallery = App_Model_Gallery::fetchActivePublicGalleryByUrlkey($urlkey);
        
        if($gallery !== null){
            $canonical = 'http://' . $host . '/galerie/r/' . $urlkey;
            $layoutView->set('canonical', $canonical)
                    ->set('metatitle', 'Peďák - '.$gallery->getTitle());
        }
        
        $view->set('gallery', $gallery);
    }

}
