<?php 

	class geocoder{ 
        public static $url = 'http://maps.googleapis.com/maps/api/geocode/xml';
		private $searchTerm;
		private $top;
		private $left;
		private $bottom;
		private $right;
		
		function __construct($searchTerm, $top, $left, $bottom, $right){
			$this->searchTerm = $searchTerm;
			$this->top = $top;
			$this->left = $left;
			$this->bottom = $bottom;
			$this->right = $right;
		}
		
		private function performRequest($search, $output = 'xml')
        {
            $url = sprintf('%s?',
                           self::$url,
                           urlencode($search),
                           $output);
 
			if(isset($this->searchTerm) && isset($this->top) && isset($this->left) && isset($this->bottom) && isset($this->right))
				$url .= '&address=' . $this->searchTerm . '&bounds=' . $this->top . ',' . $this->left . '|' . $this->bottom . ',' . $this->right . '&sensor=false';
			
            $ch = curl_init(urlencode ($url));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_PROXY, 'http://138.85.224.137:8080');
			
            $response = curl_exec($ch);
			
            curl_close($ch);
           // echo $response;
		//	exit;
            return $response;
        }
		
		public function geocode($search)
        {
            $response = $this->performRequest($search, 'xml');
			echo $response;
			exit;
			$xml      = new SimpleXMLElement($response);
            $status   = $xml->status;
            switch ($status) {
                case 'OK':
                    $placemarks = $xml;
                    return $placemarks;
 
                default:
					return array();
            }
        }
    }
?>
 
