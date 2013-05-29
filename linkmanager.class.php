<?php 
class linkManager { 
	public $db;
    public $expirationDate; 
	public $totalQuota;
	public $dailyQuota; 
	
    function __construct($db) {
      $this->db = $db;
    }
	
	/*
	 * Saves an array containing values regarding a link to the db
	 */
	function save($data){
		$query = $this->db->links();
		$result = $query->insert($data);
		return $result["id"];
	}
	
	
	/*
	 * Fetch an array containing values regarding a link from its id
	 */
	function fetch($id){
		$query = $this->db->links()->where("id LIKE ?", $id);
		$result = $query->fetch();
		$this->totalQuota = $result["total_cap"];
		$this->expirationDate = strtotime($result["expiration_time"].' + 1 day');
		
		//no entry
		if($result == null){
			return null;
		}
		if($result["total_cap"] != 0){
			if($this->checkIfTotalQuotaExceeded($result["current_total_cap"])){
				//Delete this entry as it has exceeded its cap
				$query->delete();
				return null;
			}
			if($this->checkIfDateExceeded()){
				//Delete this entry as it has exceeded its cap
				$query->delete();
				return null;
			}
		}
		$this->increment($query,$result["current_total_cap"]);
		return $result["url"];
		//return null;
	}
	
	function increment($query,$current){
		//increment
		$data = array(
			"current_total_cap" => ($current+ 1)
		);
		$query->update($data);
	}
    function checkIfTotalQuotaExceeded($currentClicks) { 
		if($this->totalQuota == 0){
			return false;
		}
		if($currentClicks>$this->totalQuota){
			return true;
		}
		return false;
    } 
	function checkIfDateExceeded() { 
		if($this->expirationDate == 0 OR (!isset($this->expirationDate))){
			return false;
		}
		if($this->expirationDate < time()){
			return true;
		}
		return false;
    } 
	function setTotalQuota($Clicks) { 
		$this->totalQuota = $Clicks;
		return;
    } 
	
	//taken from http://briancray.com/posts/free-php-url-shortener-script/
	function encodeFromID ($integer){
		//base 62
		$base=ALLOWED_CHARS;
		$length = strlen($base);
		$out = null;
		while($integer > $length - 1){
			$out = $base[fmod($integer, $length)] . $out;
			$integer = floor( $integer / $length );
		}
		return $base[$integer] . $out;
	}
	//taken from http://briancray.com/posts/free-php-url-shortener-script/
	function decodeShortenedURL ($string){
	$base=ALLOWED_CHARS;
    $length = strlen($base);
    $size = strlen($string) - 1;
    $string = str_split($string);
    $out = strpos($base, array_pop($string));
    foreach($string as $i => $char){
        $out += strpos($base, $char) * pow($length, $size - $i);
    }
    return $out;
	}
} 