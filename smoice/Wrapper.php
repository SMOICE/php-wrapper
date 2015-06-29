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

  public function findUser ( )
  {
    return $this->executeRequest('user','GET');
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
    return $this->findOne('customers','Customer',$id);
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
    return $this->findMany('customers','Customer',$ids);
    
    $result = $this->executeRequest('customers','GET',array('ids' => $ids));
    if ( isset($result->errorCode) )
      return $result;

    $customers = array();
    foreach ( $result as $row )
      $customers[] = new Customer($row);

    return $customers;
  }

  public function createProject ( Project $project )
  {
    return $this->executeRequest('projects','POST',$project);
  }

  public function updateProject ( Project $project )
  {
    return $this->executeRequest('projects/'.$project->id,'PUT',$project);
  }

  public function findProjects ( $ids = null )
  {
    return $this->findMany('projects','Project',$ids);
  }

  public function findProject ( $id )
  {
    return $this->findOne('projects','Project',$id);
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

  public function findOpenItems ( )
  {
    return $this->executeRequest('openitems','GET');
  }

  public function createEvent ( Event $event )
  {
    return $this->executeRequest('events','POST',$event);
  }

  public function updateEvent ( Event $event )
  {
    return $this->executeRequest('events/'.$event->id,'PUT',$event);
  }

  public function findEvent ( $id )
  {
    $result = $this->executeRequest('events/'.$id,'GET');
    if ( isset($result->errorCode) )
      return $result;

    return new Event($result);
  }

  public function findEvents ( $fromDate = null, $tillDate = null )
  {
    $result = $this->executeRequest('events','GET',array('fromDate' => $fromDate, 'tillDate' => $tillDate));
    if ( isset($result->errorCode) )
      return $result;

    $events = array();
    foreach ( $result as $row )
      $events[] = new Event($row);
    return $events;
  }

  public function findEventParticipants ( $eventId )
  {
    return $this->executeRequest('participants','GET',array('eventId' => $eventId));
  }

  public function createProduct ( Product $product )
  {
    return $this->executeRequest('products','POST',$product);
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

  public function findProduct ( $id )
  {
    $result = $this->executeRequest('products/'.$id,'GET');
    if ( isset($result->errorCode) )
      return $result;

    return new Product($result);
  }

  public function findProducts ( $ids = null, $fromDate = null, $tillDate = null )
  {
    $result = $this->executeRequest('products','GET',array('ids' => $ids, 'fromDate' => $fromDate, 'tillDate' => $tillDate));
    if ( isset($result->errorCode) )
      return $result;

    $products = array();
    foreach ( $result as $row )
      $products[] = new Product($row);

    return $products;
  }

  public function createTimeEntry ( TimeEntry $timeEntry )
  {
    return $this->executeRequest('timeentries','POST',$timeEntry);
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

  public function nextNumber ( $type )
  {
    return $this->executeRequest('nextNumber','POST',array('type' => $type));
  }

  protected function findOne ( $endpoint, $className, $id )
  {
    $result = $this->executeRequest($endpoint.'/'.$id,'GET');
    if ( isset($result->errorCode) )
      return $result;

    switch ( $className )
      {
      case 'Project' :
        return new Project($result);
      case 'Customer' :
        return new Customer($result);
      }
  }
  
  protected function findMany ( $endpoint, $className, $ids = null )
  {
    $result = $this->executeRequest($endpoint.'/','GET',array('ids' => $ids));
    if ( isset($result->errorCode) )
      return $result;

    $return = array();
    foreach ( $result as $row )
      switch ( $className )
        {
        case 'Project' :
          $return[] = new Project($row); break;
        case 'Customer' :
          $return[] = new Customer($row); break;
        }

    return $return;
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
