<?php
namespace LegoAPI\Modules\Core\Controller;
class AuthController extends \Lego\Modular\APIController
{
    /**
     * Verity user information and generate access token
     */
    public function Authenticate()
    {
        $authInfo = array(
            'iat' => APP_TIME, 
            'nbf' => APP_TIME+1,
        );
        $accesToken = $this->container->get('authenticate')->requestAccessToken($authInfo);
        $this->view->set(array(
            'accesToken' => $accesToken,
        ));
    }
}

?>