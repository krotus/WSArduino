<?php 

require_once("ViewApi.php");

class ViewXML extends ViewApi{

	public function prints($body){
		if($this->state){
			http_response_code($this->state);
		}
		header('Content-Type: text/xml');

        $xml = new SimpleXMLElement('<response/>');
        self::parseArray($body, $xml);
        print $xml->asXML();

        exit;
	}

	/* Converteix un array a XML */
	public function parseArray($data, &$xml_data){
		foreach ($data as $key => $value) {
			if(is_array($value)){
				if(is_numeric($key)){
					$key = "item" . $key;
				}
				$subnode = $xml_data->addChild($key);
				self::parseArray($value, $subnode);
			}else{
				$xml_data->addChild("$key", htmlspecialchars("$value"));
			}
		}
	}

}

?>