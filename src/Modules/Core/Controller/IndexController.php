<?php
namespace LegoAPI\Modules\Core\Controller;


use \LegoAPI\Modules\Core\Model\Hello as HelloModel;

class IndexController extends \Lego\Modular\APIController
{
    public function Hello()
    {
        $oModel = new HelloModel();
        $v = $this->request->getParam('v');
        $info = $this->getAuth()->getVerifiedInfo();
        //$data = HelloModel::where('hello_id',1)->first();
        //$data = $oModel->newQuery()->where('hello_id','=',1)->first();
        $data = $oModel->all();
        $oModel->saveToCache('list_hello',$data->toArray());
        $this->view->set(array(
            'v' => $v,
            'h' => $info,
            'hello' => $data->toArray()
        ));
        //$this->view->setMode(ViewInterface::MODE_RENDER_RAW);
    }
    /**
     * Test Dynamic Action. With this type, the verification should do manually insite action process
     */
    public function Hello1()
    {
        $v = $this->request->getParam('v');
        $this->view->set(array(
            'v' => $v . " Hello 1"
        ));
    }
    /**
     * Test Cache Items
     */
    public function Cache()
    {
        $this->isAllowMethod('GET');
        $oModel = new HelloModel();
        $data = $oModel->getFromCache('list_hello'); 
        $this->view->set(array(
            'helloCache' => $data
        ));
    }
}

?>