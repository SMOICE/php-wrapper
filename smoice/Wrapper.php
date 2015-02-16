<?php

namespace smoice;

class Wrapper
{
  private $key;
  private $url;
  private $body;

  public function __construct ( $key, $server = 'https://easy.smoice.com/' )
  {
    $this->key = $key;
    $this->url = $server;
  }

  public function login ( $email, $passwordHash )
  {
    return $this->executeRequest('login','POST',array('email' => $email, 'passwordHash' => $passwordHash));
  }

  public function createCustomer ( Customer $customer )
  {
    return $this->executeRequest('customers','POST',$customer);
  }

  public function updateCustomer ( Customer $customer )
  {
    return $this->executeRequest('customers/'.$customer->id,'PUT',$customer);
  }

  public function findCustomer ( $id )
  {
    $result = $this->executeRequest('customers/'.$id,'GET');
    if ( isset($result->errorCode) )
      return $result;

    return new Customer($result);
  }

  public function findCustomers ( $ids = null )
  {
    $result = $this->executeRequest('customers','GET',array('ids' => $ids));
    if ( isset($result->errorCode) )
      return $result;

    $customers = array();
    foreach ( $result as $row )
      $customers[] = new Customer($row);
    return $customers;
  }

  public function createProject ( $projectName, $customerId )
  {
    return $this->executeRequest('projects','POST',array('projectName' => $projectName, 'customerId' => $customerId));
  }

  public function findProjects ( )
  {
    return $this->executeRequest('projects','GET');
  }

  public function getInvoicePDF ( $id, $includeBackground = true )
  {
    $result = $this->executeRequest('pdfinvoice/'.$id,'GET',array('includeBackground' => $includeBackground));
    if ( isset($result->errorCode) )
      return $result;

    return $result;
  }

  public function findInvoice ( $id )
  {
    $result = $this->executeRequest('invoices/'.$id,'GET');
    if ( isset($result->errorCode) )
      return $result;

    return new Invoice($result);
  }

  public function findInvoices ( $fromDate = null, $tillDate = null )
  {
    return $this->executeRequest('invoices','GET',array('fromDate' => $fromDate, 'tillDate' => $tillDate));
  }

  public function createInvoice ( $customerId, $details, $textBefore = null, $textAfter = null, $pricesIncludeVAT = false )
  {
    return $this->executeRequest('invoices','POST',array('customerId' => $customerId,
                                                         'textBefore' => $textBefore,
                                                         'textAfter' => $textAfter,
                                                         'pricesIncludeVAT' => $pricesIncludeVAT,
                                                         'details' => $details));
  }

  public function nextNumber ( $type )
  {
    return $this->executeRequest('nextNumber','POST',array('type' => $type));
  }

  protected function executeRequest ( $request, $method, $params = array())
  {
    $url = $this->buildUrl($request);
    $this->buildJsonData($params);
    return $this->executeCurl($url,$method);
  }

  private function buildUrl ( $request )
  {
    $url = $this->url;
    if ( substr($url,-1) != '/' )
      $url .= '/';

    return $url . 'api/2.0/'.$request;
  }

  private function buildJsonData ( $params )
  {
    $this->body = array();
    foreach ( $params as $key => $value )
      if ( $value !== null )
        $this->body[$key] = $value;
    $this->body = json_encode($this->body);
  }

  private function executeCurl ( $url, $method )
  {
    $ch = curl_init();    

    $params = '';
    if ( $method == 'GET' )
      $params = http_build_query(json_decode($this->body));
    if ( !empty($params) )
      $params .= '&';
    
    $url .= '?'.$params.'key='.$this->key;;
    
    //echo $url."\n".$this->body."\n";
    
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_ENCODING, "UTF-8");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    if ( $method == 'POST' )
      {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $this->body);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                                                   'Content-Type: application/json',
                                                   'Content-Length: ' . strlen($this->body)
                                                   )
                    );
      }
 
    if ( $method == 'DELETE' )
      {
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
      }

    if ( $method == 'PUT' )
      {
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $this->body);
      }

    return json_decode(curl_exec($ch));
  }

}

?>
