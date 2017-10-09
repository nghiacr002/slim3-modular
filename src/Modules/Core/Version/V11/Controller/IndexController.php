<?php 
namespace LegoAPI\Modules\Core\Version\V11\Controller;
use LegoAPI\Modules\Core\Controller\IndexController as IndexBaseController;

class IndexController extends IndexBaseController
{
    public function Hello()
    {
        $v = $this->request->getParam('v');
        $this->view->set(array(
            'v11' => $v
        ));
    }
}
