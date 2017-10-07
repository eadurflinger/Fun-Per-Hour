<?php
class Token {
  const SERVER_KEY = '';
  protected $uid;
  private $TIME_ALLOTTED = 3600; //60 minutes before it expires
  public function __construct($randKey=null, $token = null){
    if($token) {
      $tokenArray = json_decode($this->_decrypt($token),true);
      if(!$this->_checkExp($tokenArray['exp'])) throw new Exception('Token Expired');
      $this->uid = $tokenArray['uid'];
    }
  }
  public function __get($var) {
    return $this->$var;
  }
  public function __set($var, $val) {
    $this->$var = $val;
  }
  private function _decrypt($ciphertext) {
    //TODO: add decoding algorithm here lol
    $key = self::SERVER_KEY;
    $c = base64_decode($ciphertext);
    $ivlen = openssl_cipher_iv_length($cipher="AES-128-CBC");
    $iv = substr($c, 0, $ivlen);
    $hmac = substr($c, $ivlen, $sha2len=32);
    $ciphertext_raw = substr($c, $ivlen+$sha2len);
    $plaintext = openssl_decrypt($ciphertext_raw, $cipher, $key, $options=OPENSSL_RAW_DATA, $iv);
    $calcmac = hash_hmac('sha256', $ciphertext_raw, $key, $as_binary=true);
    if (hash_equals($hmac, $calcmac))
    {
        return $plaintext;
    }
    throw new Exception("Authentication Error", 1);
  }
  private function _checkExp($exp) { //returns false if expired token
    $date = new DateTime();
    $cur_time = $date->getTimeStamp();
    $timeRemaining = $exp - $cur_time;
    if($timeRemaining>=0) {
      return true;
    } else {return false;}
  }
  private function _makeExp(){
    $date = new DateTime();
    $cur_time = $date->getTimeStamp();
    return $cur_time + $this->TIME_ALLOTTED;
  }
  public function genToken() {
    $rawToken = json_encode([
      'uid' => $this->uid,
      'exp' => $this->_makeExp()
    ]);
    $token = $this->_encrypt($rawToken);
    return $token;
  }
  /**
  * Encrypt token
  **/
  private function _encrypt($plaintext){
      $key = self::SERVER_KEY;
      $ivlen = openssl_cipher_iv_length($cipher="AES-128-CBC");
      $iv = openssl_random_pseudo_bytes($ivlen);
      $ciphertext_raw = openssl_encrypt($plaintext, $cipher, $key, $options=OPENSSL_RAW_DATA, $iv);
      $hmac = hash_hmac('sha256', $ciphertext_raw, $key, $as_binary=true);
      $ciphertext = base64_encode( $iv.$hmac.$ciphertext_raw );
      return $ciphertext;
  }
}
 ?>
