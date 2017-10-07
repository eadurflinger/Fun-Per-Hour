<?php
/*URI: /pubfunc/params[0]/params[1]/...
***Allowed pubfuncs: #note that (stuff) is stored in API class under $input
* login($col, $val)
* forgotPass($user)
* newUser($col, $val)
* access($table, $col, $val, $orderby)
* accessAd($id,$table, $col, $val, $orderby)
* dump($table, $col, $val, $orderby) //returns excel file?
* toggleEntry() //start or stops entry
* inProjects($either in or not --an actual param i guess)
***COMPARE TABLE & COLUMN NAMES TO KNOWN NAMES BEFORE ALLOWING THEM TO BE USED TO AVOID MYSQL INJECTION
***DONT ALLOW USER INPUT TO GO INTO WHERE/TABLE (other than known values)
***no one has access to
* sql($id,$table, $col, $val, $orderby, $where)
*
***within the token...
* expiration date
* authority
* u_ID
*/
require_once 'API.class.php';
require_once 'user.class.php';
require_once 'token.class.php';
class mAPI extends API {
  protected $user,$mysqli,$data,$token;
  public function __construct($request, $origin, $mysqli) {
    parent::__construct($request);
    if (!is_a($mysqli, 'mysqli')) throw new Exception('Invalid MySQL Object');
    $this->mysqli = $mysqli;
    // TODO: validate API_key here
    if(!$this->input) throw new Exception("No Input!");
    if(!array_key_exists('newRand', $this->input)) throw new Exception("No Rand");
    if(!array_key_exists('token', $this->input)&&array_key_exists('newRand', $this->input)) {
      $this->user = new User();
      $this->token = new Token();
    } else if(array_key_exists('token', $this->input)&&array_key_exists('newRand', $this->input)&&array_key_exists('randKey', $this->input)){
      $this->token = new Token($this->input['randKey'], $this->input['token']);
      $this->user = new User($this->token->uid);
    }
    $this->data = $this->input['data'];
    //TODO make sure we've received all the $input $k=>$v that are necessary
    //TODO figure out AUTHENTICATION here...
    // $APIKey = new Models\APIKey();
    // $User = new Models\User();
    //
    // if (!array_key_exists('apiKey', $this->request)) {
    //     throw new Exception('No API Key provided');
    // } else if (!$APIKey->verifyKey($this->request['apiKey'], $origin)) {
    //     throw new Exception('Invalid API Key');
    // } else if (array_key_exists('token', $this->request) &&
    //      !$User->get('token', $this->request['token'])) {
    //
    //     throw new Exception('Invalid User Token');
    // }
    // $this->User = $User;
  }
  protected function buildResponse($res) {
    $out = ['sqlres' => $res];
    $out['token'] = $this->token->genToken();
    return $out;
  }
  protected function splitColsVals($data) {
    $col = $this->sqlEscapeArray(array_keys($data));
    $val = $this->sqlEscapeArray(array_values($data));
    return ['col' => $col, 'val' => $val];
  }
  protected function sqlEscapeArray($arr) {
    $result = Array();
    foreach($arr as $value) {
      array_push($result, $this->mysqli->real_escape_string($value));
    }
    return $result;
  }
  private function _buildSQL($data, $nullsep='=', $termsep=',') {
    $result = '';
    for($i=0; $i<count($data['col']);$i++) {
      $result .=($i>0?$termsep:'').'`'.$data['col'][$i].'`'.
      ($data['val'][$i]==null?$nullsep.'NULL':'="'.$data['val'][$i].'"');
    }
    return $result;
  }
  protected function makeQuery($table,$rawSet=null, $rawWhere=null, $rawOrderBy=null, $rawLimit = null) {
    $set = $where = $orderBy = $limit = null;
    if(!empty($rawSet)) $set = $this->_buildSQL($this->splitColsVals($rawSet));
    if(!empty($rawWhere)) $where = $this->_buildSQL($this->splitColsVals($rawWhere), ' IS ', ' AND ');
    if(!empty($rawOrderBy)) $orderBy = $rawOrderBy;
    if(!empty($rawLimit)) $limit = $rawLimit;
    $sql = "";
    switch ($this->method) {
      case 'GET':
        $sql = "SELECT * FROM `$table`".($where?" WHERE $where":'') . ($orderBy?"ORDER BY $orderBy":'') .($limit? "LIMIT $limit[0], $limit[1]" : '');
        break;
      case 'PUT':
        $sql = "UPDATE `$table` SET $set WHERE $where";
        break;
      case 'POST':
        $sql = "INSERT INTO `$table` SET $set";
        break;
      case 'DELETE':
        $sql = "DELETE FROM `$table` WHERE $where";
        break;
    }
    return $sql;
  }
  private function _sendQuery($sql) {
    $res = $this->mysqli->query($sql);
    if(!$res) {
      $status = 404; return 'MySQL Error: '. $this->mysqli->error.' '. $sql;
    } else if(is_bool($res)&& $res) return ['result'=>'success'];
    else {
      $out = Array();
        while($row = $res->fetch_assoc()) {
          array_push($out, $row);
        }
      return $out;
    }
  }
  private function _arrayKeysExist(array $keys, array $haystack ) {
    return !array_diff_key(array_flip($keys), $haystack);
  }
  protected function access() {
    if ($this->user->loggedIn===false) {
      $this->status = 401;
      return ['error' => 'Access Denied'];
    }
    if (empty($this->data['set'])) $this->data['set'] = Array();
    if (empty($this->data['where'])) $this->data['where'] = Array();
    if (empty($this->data['orderBy'])) $this->data['orderBy'] = Array();
    if (empty($this->data['limit'])) $this->data['limit'] = Array();
    $rawSet = array_merge(['u_id' => $this->user->uid],$this->data['set']);
    $rawWhere = array_merge(['u_id' => $this->user->uid],$this->data['where']);
    $sql = $this->makeQuery($this->data['table'], $rawSet, $rawWhere, $this->data['orderBy'],$this->data['limit']);
    $out = $this->_sendQuery($sql);
    return $this->buildResponse($out);
  }
  protected function login() {
    if($this->method != 'GET') {$this->status=404; return ['error'=>'Wrong Method'];}
    //TODO: salt and hash password
    $whereUser = Array();
    $whereUser['usr'] = $this->data['where']['usr'];
    $sql = $this->makeQuery('users', null, $whereUser);
    if (!($res = $this->_sendQuery($sql))) throw new Exception("User Doesn't Exist");//might be a syntax error
    $this->data['where']['pass'] .= $res[0]['salt'];
    $this->data['where']['pass'] = hash('sha256', $this->data['where']['pass']);
    $sql = $this->makeQuery('users', null, $this->data['where'], null);
    if($res = $this->_sendQuery($sql)) {
      $this->token->uid = $res[0]['u_ID'];
      return $this->buildResponse($res[0]);
    }
    $this->status = 401;
    return ['error' => 'Login Failed'];
  }
  protected function newUser() {
    if($this->method != 'POST') {$this->status=404; return 'Wrong Method'; die();}
    $sql = $this->makeQuery('users', null , ['usr'=>$this->data['set']['usr']]);
    if($res = $this->_sendQuery($sql)) throw new Exception("User Already Exists");
    // SALT & HASH GENERATOR
    $bytes = random_bytes('10');
    $salt = bin2hex($bytes);
    $this->data['set']['pass'] .= $salt;
    $this->data['set']['pass'] = hash('sha256', $this->data['set']['pass']);
    $saltedSet = array_merge(['salt' => $salt],$this->data['set']);
    $sql = $this->makeQuery('users', $saltedSet, null, null);
    return $this->buildResponse($this->_sendQuery($sql));
  }
  protected function forgot() {
  if($this->method != 'GET') {$this->status=404; return 'Wrong Method'; die();}
  }
  protected function dump() {
  }
}
 ?>
