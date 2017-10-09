<?php
namespace Lego\Modular; 

use Lego\Interfaces\ViewInterface;
use Slim\Http\Response;
use Slim\Collection;
use SimpleXMLElement;

class View implements ViewInterface
{
    protected $response;
    protected $data;
    protected $options;
    protected $modeDisplay;
    public function __construct()
    {
        $this->data = new Collection();
        $this->options = new Collection();
        $this->options['headers'] = array();
    }
    
    public function assign($data = [])
    {
       if(!is_array($data))
       {
           $data = array($data);
       }
       if(count($data))
       {
           foreach($data as $key => $value)
           {
               $this->data[$key] = $value;
           }
       }
       return $this;
    }
    
    public function assignOption($options = [])
    {
        if(!is_array($options))
        {
            $options = array($options);
        }
        if(count($options))
        {
            foreach($options as $key => $value)
            {
                $this->options[$key] = $value;
            }
        }
        return $this;
    }
    
    public function setOption($options = [])
    {
        if(count($options))
        {
            foreach($options as $key => $value)
            {
                $this->options->set($key,$value);
            }
        }
        return $this;
    }
    
    public function set($data = [])
    {
        if(count($data))
        {
            foreach($data as $key => $value)
            {
                $this->data->set($key,$value);
            }
        }
        return $this;
    }
   
    public function getResponse($mode = null)
    {
        if($mode)
        {
            $this->setMode($mode);
        }
        $data = $this->data->all();
        $statusCode = $this->options->get('httpStatus',HTTP_CODE_OK);
        $headers = isset($this->options['headers']) ? $this->options['headers'] : array(); 
        switch($this->modeDisplay)
        {
            case ViewInterface::MODE_RENDER_JSON:
                $encodingOptions = $this->options->get('encodingOptions',0);
                $response =  $this->response->withJson($data,$statusCode,$encodingOptions);
                break;
            case ViewInterface::MODE_RENDER_XML:
                $response = $this->response->withHeader("Content-Type","application/xml");
                $response->write($this->convertArray2XML($data));
                $response->withStatus($statusCode);
                break;
            case ViewInterface::MODE_RENDER_HTML:
                break;
            case ViewInterface::MODE_RENDER_RAW:
            default:
                $response = $this->response;
                break;
        }
        if(count($headers))
        {
            foreach($headers as $key => $value)
            {
                $response = $response->withHeader($key, $value);
            }
        }
        $this->response = $response; 
        return $this->response;
    }
    
    public function setMode($mode)
    {
        $this->modeDisplay = $mode;
        return $this;
    }
    
    public function setResponse(Response $response)
    {
        $this->response = $response;
        return $this;
    }
    public function setHTTPStatus($status )
    {
        return $this->setOption(array('httpStatus' => $status));
    }
    public function convertArray2XML($data = [] , SimpleXMLElement $xml = null, $returnObject = false)
    {
        if($xml == null)
        {
            $xml = new SimpleXMLElement('<?xml version="1.0"?><data></data>');
        }
        foreach( $data as $key => $value ) 
        {
            if( is_numeric($key) )
            {
                $key = 'item'.$key; //dealing with <0/>..<n/> issues
            }
            if( is_array($value) ) 
            {
                $subnode = $xml->addChild($key);
                array_to_xml($value, $subnode);
            } else 
            {
                $xml->addChild("$key",htmlspecialchars("$value"));
            }
        }
        if($returnObject)
        {
            return $xml;
        }
        return $xml->asXML();
    }
    
    public function __get($name)
    {
        return $this->data->get($name,null); 
    }
    
    public function __set($name, $value = null)
    {
        $this->data->set($name,$value); 
        return $this; 
    }
    public function determineResponseMode($contentType = "")
    {
        $mode = ViewInterface::MODE_RENDER_JSON;
        switch ($contentType) {
            
            case 'text/xml':
            case 'application/xml':
                $mode = ViewInterface::MODE_RENDER_XML;
                break;
            case 'text/html':
                $mode = ViewInterface::MODE_RENDER_HTML;
                break;
            case 'application/json':
                $mode = ViewInterface::MODE_RENDER_JSON;
                break;
            default:
                $mode = ViewInterface::MODE_RENDER_RAW;
        }
        return $mode;
    }
   
    public function setHeaderOptions($options = [])
    {
        $headers = $this->options->get('headers',array()); 
        if(!is_array($options))
        {
            $options = array($options);
        }
        if(count($options))
        {
            foreach($options as $key => $value)
            {
                $headers[$key] = $value;
            }
        }
        $this->options->set('headers',$headers);
        return $this;
    }

   

}
?>