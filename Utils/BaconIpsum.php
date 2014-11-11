<?php
/**
 * Created by PhpStorm.
 * User: Rudak
 * Date: 11/11/2014
 * Time: 09:27
 */

namespace Rudak\BlogBundle\Utils;

/**
 * Description of BaconIpsum
 *
 * @author rudak
 */
Class BaconIpsum
{
    private $url = 'http://baconipsum.com/api/?';
    private $params = array(
        'type'             => 'all-meat',
        'paras'            => 6,
        'start-with-lorem' => true,
        //'sentences' => 2
    );

    public function get_content()
    {
        $counter = 0;
        foreach ($this->params as $param => $value) {
            $this->url .= $param . '=' . $value;
            if (++$counter < count($this->params)) {
                $this->url .= '&';
            }
        }
        $json_data  = file_get_contents($this->url);
        $bacon_data = json_decode($json_data);
        $p_tag      = "<p>%s</p>\n";
        $html_out   = '';
        foreach ($bacon_data as $p) {
            $html_out .= sprintf($p_tag, $p);
        }
        return $html_out;
    }
}