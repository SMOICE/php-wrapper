<?php

namespace smoice;

class Wrapper
{
  private $clientId;
  private $clientSecret;
  private $refreshToken;
  private $url;
  private $body;

  public function __construct ( $refreshToken, $clientId, $clientSecret, $server = 'https://easy.smoice.com/' )
  {
    $this->refreshToken = $refreshToken;
    $this->clientId = $clientId;
    $this->clientSecret = $clientSecret;
    $this->url = $server;
  }

  /*
   * ask for user information providing login credentials
   * result include api key for further requests
   */
  public function login ( $email, $passwordHash )
  {
    return $this->executeRequest('login','POST',array('email' => $email, 'passwordHash' => $passwordHash));
  }

  /*
   * ask for user information providing
   */
  public function findUser ( )
  {
    return $this->executeRequest('user','GET');
  }

  /*
   * customer related methods
   */
  public function createCustomer ( Customer $customer )
  {
    return $this->executeRequest('customers','POST',$customer);
  }

  public function findCustomer ( $id )
  {
    return $this->findOne('customers',$id);
  }

  public function findCustomerByNumber ( $number )
  {
    $result = $this->executeRequest('customers','GET',array('numbers' => array($number)));
    if ( isset($result->errorCode) )
      return $result;

    $row = reset($result);
    $customer = new Customer($row);

    return $customer;
  }

  public function findCustomers ( $ids = null )
  {
    return $this->findMany('customers',$ids);
  }

  public function updateCustomer ( Customer $customer )
  {
    return $this->executeRequest('customers/'.$customer->id,'PUT',$customer);
  }





  
  /*
   * project related methods
   */
  public function createProject ( Project $project )
  {
    return $this->executeRequest('projects','POST',$project);
  }

  public function findProjects ( $ids = null )
  {
    return $this->findMany('projects',$ids);
  }

  public function findProject ( $id )
  {
    return $this->findOne('projects',$id);
  }

  public function updateProject ( Project $project )
  {
    return $this->executeRequest('projects/'.$project->id,'PUT',$project);
  }





  
  /*
   * invoice related methods
   */
  public function createInvoice ( $customerId, $details, $textBefore = null, $textAfter = null, $pricesIncludeVAT = false, $preview = false )
  {
    return $this->executeRequest('invoices',
                                 'POST',
                                 array('customerId' => $customerId,
                                       'textBefore' => $textBefore,
                                       'textAfter' => $textAfter,
                                       'pricesIncludeVAT' => $pricesIncludeVAT,
                                       'details' => $details,
                                       'preview' => $preview
                                       )
                                 );
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

  public function cancelInvoice ( $id )
  {
    return $this->executeRequest('cancelinvoice/'.$id,'GET');
  }
      




  /*
   * methods related to open items
   */
  public function findOpenItems ( )
  {
    return $this->executeRequest('openitems','GET');
  }






  /*
   * event related methods
   */
  public function createEvent ( Event $event )
  {
    return $this->executeRequest('events','POST',$event);
  }

  public function findEvent ( $id )
  {
    return $this->findOne('events',$id);
  }

  public function findEvents ( $fromDate = null, $tillDate = null )
  {
    return $this->findMany('events',null,array('fromDate' => $fromDate, 'tillDate' => $tillDate));
  }

  public function updateEvent ( Event $event )
  {
    return $this->executeRequest('events/'.$event->id,'PUT',$event);
  }

  public function findEventParticipants ( $eventId )
  {
    return $this->executeRequest('participants','GET',array('eventId' => $eventId));
  }






  /*
   * product related methods
   */
  public function createProduct ( Product $product )
  {
    return $this->executeRequest('products','POST',$product);
  }

  public function findProduct ( $id )
  {
    return $this->findOne('products',$id);
  }

  public function findProducts ( $ids = null, $fromDate = null, $tillDate = null )
  {
    return $this->findMany('products',$ids,array('fromDate' => $fromDate, 'tillDate' => $tillDate));
  }

  public function updateProduct ( Product $product )
  {
    return $this->executeRequest('products/'.$product->id,'PUT',$product);
  }

  public function deleteProduct ( $productOrProductId )
  {
    if ( is_numeric($productOrProductId) )
      return $this->executeRequest('products/'.$productOrProductId,'DELETE');

    return $this->executeRequest('products/'.$productOrProductId->id,'DELETE');
  }






  /*
   * methods related to time entries
   */
  public function createTimeEntry ( TimeEntry $timeEntry )
  {
    return $this->executeRequest('timeentries','POST',$timeEntry);
  }

  public function findTimeEntry ( $id )
  {
    $result = $this->executeRequest('timeEntries/'.$id,'GET');
    if ( isset($result->errorCode) )
      return $result;

    return new TimeEntry($result);
  }

  public function findTimeEntries ( $ids = null, $fromDate = null, $tillDate = null )
  {
    $result = $this->executeRequest('timeentries','GET',array('ids' => $ids, 'fromDate' => $fromDate, 'tillDate' => $tillDate));
    if ( isset($result->errorCode) )
      return $result;

    $timeEntries = array();
    foreach ( $result as $row )
      $timeEntries[] = new TimeEntry($row);

    return $timeEntries;
  }

  public function updateTimeEntry ( TimeEntry $timeEntry )
  {
    return $this->executeRequest('timeEntries/'.$timeEntry->id,'PUT',$timeEntry);
  }

  public function deleteTimeEntry ( $timeEntryOrTimeEntryId )
  {
    if ( is_numeric($timeEntryOrTimeEntryId) )
      return $this->executeRequest('timeentries/'.$timeEntryOrTimeEntryId,'DELETE');

    return $this->executeRequest('timeentries/'.$timeEntryOrTimeEntryId->id,'DELETE');
  }






  /*
   * find the next automatic number for customers, projects, invoices, quotes
   */
  public function nextNumber ( $type )
  {
    return $this->executeRequest('nextNumber','POST',array('type' => $type));
  }





  /*
   * helper methods
   */
  protected function findOne ( $endpoint, $id )
  {
    $result = $this->executeRequest($endpoint.'/'.$id,'GET');
    if ( isset($result->errorCode) )
      return $result;

    switch ( $endpoint )
      {
      case 'projects' :
        return new Project($result);
      case 'customers' :
        return new Customer($result);
      case 'events' :
        return new Event($result);
      case 'products' :
        return new Product($result);
      }
  }
  
  protected function findMany ( $endpoint, $ids = null, $alternatives = null )
  {
    $params = array();
    if ( $ids !== null )
      $params['ids'] = $ids;
    if ( $alternatives !== null && is_array($alternatives) )
      $params = array_merge($params,$alternatives);
    
    $result = $this->executeRequest($endpoint.'/','GET',$params);
    if ( isset($result->errorCode) )
      return $result;

    $return = array();
    foreach ( $result as $row )
      switch ( $endpoint )
        {
        case 'projects' :
          $return[] = new Project($row); break;
        case 'customers' :
          $return[] = new Customer($row); break;
        case 'events' :
          $return[] = new Event($row); break;
        case 'products' :
          $return[] = new Product($row); break;
        }

    return $return;
  }

  final protected function executeRequest ( $request, $method, $params = array())
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
    
    if ( $params > '' )
      $url .= '?'.$params;
    
    $headers = array("Authorization: Bearer ".$this->refreshToken);
    
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_ENCODING, "UTF-8");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    if ( $method == 'POST' )
      {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $this->body);
        $headers[] = 'Content-Type: application/json';
        $headers[] = 'Content-Length: ' . strlen($this->body);
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

    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    //echo "$method: $url\n".$this->body."\n";print_r($headers);

    return json_decode(curl_exec($ch));
  }

}

?>
