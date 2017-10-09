<?php
namespace Lego\Interfaces;
use Slim\Http\Response;
/**
 * Interface to declare render information
 * @author nghia
 *
 */
interface ViewInterface
{
    const MODE_RENDER_JSON = "JSON";
    const MODE_RENDER_XML = "XML";
    const MODE_RENDER_RAW = "RAW";
    const MODE_RENDER_HTML = "HTML";
    /**
     * Assign param to view 
     * @param array $data
     */
    public function assign($data = []);
    /**
     * Set content to response and forward to next middleware
     * @param unknown $mode
     */
    public function getResponse($mode = self::MODE_RENDER_JSON);
    /**
     * Set data return to content
     * @param array $data
     */
    public function set($data = []);
    /**
     * Set Extra options for next actions
     * @param array $options
     */
    public function setOption($options = []);
    /**
     * Assign param to options 
     * @param array $options
     */
    public function assignOption($options = []);
    /**
     * Set default mode to display content
     * @param string $mode
     */
    public function setMode($mode);
    /**
     * Set response message from previous middleware
     * @param Response $response
     */
    public function setResponse(Response $response);
    /**
     * Convert from content type header to display mode
     * @param string $contentType
     */
    public function determineResponseMode($contentType = "");
    /**
     * Set Special key&value to header of response
     * @param array $options
     */
    public function setHeaderOptions($options = []);
    
}
?>