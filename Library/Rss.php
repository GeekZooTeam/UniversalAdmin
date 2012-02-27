<?php
class Rss
{
    private $dom = null;
    private $root = null;
    private $channel = null;

    function __construct()
    {
        $this->dom = new DOMDocument("1.0", 'UTF-8');
        // print_r($this->dom);exit;
        $this->root = $this->dom->createElement('rss');
        $this->root = $this->dom->appendChild($this->root);
        $this->root->setAttribute('version','2.0');
    }

    public function setChannel($array)
    {
        $this->channel = $this->dom->createElement("channel");
        $this->channel = $this->root->appendChild($this->channel);
        $this->createElement($this->channel, $array);
    }

    public function addItem($array)
    {
        // print_r($array);exit;
        $item = $this->dom->createElement("item");
        $item = $this->channel->appendChild($item);
        $this->createElement($item, $array);
    }

    private function createElement($em, $array)
    {
        foreach ($array as $key => $val)
        {
            if ($key == 'title' || $key == 'description') {
                $item = $this->dom->createElement($key);
                $ct = $item->ownerDocument->createCDATASection($val);
                $item->appendChild($ct);
            } else {
                $item = $this->dom->createElement($key, $val);
            }
            $em->appendChild($item);
        }
    }

    public function genarate()
    {
        header('Content-Type: text/xml');
        echo $this->dom->saveXML();
    }
}
?>