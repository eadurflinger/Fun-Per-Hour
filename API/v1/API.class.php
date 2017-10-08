<?php

/*
* URI: /pubfunc/params[0]/params[1]/...
*/

abstract class API {

  protected $method,$pubfunc,$content_type = '';
  protected $params = Array(),$args,$input = Array();
  protected $status = 200;

  public function __construct($request) {
    header("Access-Control-Allow-Orgin: *"); //NO CHANGE
    header("Access-Control-Allow-Methods: *");
    header("Access-Control-Allow-Headers: *");
    header("Content-Type: application/json");

    $this->args = explode('/', trim($request,'/'));
    $this->pubfunc = array_shift($this->args);
    while (!empty($this->args[0]) && !is_numeric($this->args[0])) {
      array_push($this->params,array_shift($this->args));
    }

    $this->method = $_SERVER['REQUEST_METHOD'];
    if ($this->method == 'POST' && array_key_exists('HTTP_X_HTTP_METHOD', $_SERVER)) {
        switch($_SERVER['HTTP_X_HTTP_METHOD']) {
          case 'DELETE':
            $this->method = 'DELETE'; break;
          case 'PUT':
            $this->method = 'PUT'; break;
          default:
            throw new Exception("Unexpected Header"); break;
        }
    }

    if(array_key_exists('CONTENT_TYPE', $_SERVER)) $this->content_type = $_SERVER['CONTENT_TYPE'];
    else {throw new Exception('No Content Type Provided');}
      if(strpos($this->content_type, 'application/json') !== false) {
          $this->input = $this->_sanitize(json_decode(file_get_contents('php://input'),true));
      } else if(strpos($this->content_type, 'application/x-www-form-urlencoded') !== false) {
          switch($method) {
              case "GET":
                  $this->input = $this->_sanitize($_GET);
                  break;
              case "POST":
                  $this->input = $this->_sanitize($_POST);
                  break;
              default:
                  $this->input = $this->_sanitize($_GET);
                  break;
          }
      } else throw new Exception("Unexpected Content Type");

  }

  public function runAPI() {
    if(method_exists($this, $this->pubfunc)) {
      return $this->_response($this->{$this->pubfunc}($this->params), $this->status);
    }
    return $this->_response($this->pubfunc . " Not Found", 404);
  }

  private function _response($out, $status = 200) {
    header("HTTP/1.1 " . $status . " " . $this->_requestStatus($status));
    return json_encode($out);
  }

  private function _sanitize($input) {
    if(is_array($input)) {
      $cleaned = Array();
      foreach($input as $k => $v) {
        $cleaned[$k] = $this->_sanitize($v);
      }
    } else {
      $cleaned = htmlspecialchars(trim($input));
    }
    return $cleaned;
  }

  private function _requestStatus($code) {
      switch($code) {
        case 200:
          return 'OK';
          break;
        case 401:
          return 'Unauthorized';
          break;
        case 404:
          return 'Not Found';
          break;
        case 405:
          return 'Method Not Allowed';
          break;
        default:
          return 'Internal Server Error';
          break;
      }
  }

}

 ?>
