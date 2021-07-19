<?php

use Http\Http;
use File\FileList;
use File\File;
use Salesforce\SalesforceAttachment;
use Salesforce\OAuthRequest;
use Http\HttpRequest;
use Salesforce\OAuthResponse;
use Salesforce\RestApiRequest;
use Http\HttpHeader;

class DirectoryModule extends Module
{
    private static $sandboxUrl = "https://api.sandbox.cloudconvert.com";
    private static $productionUrl = "https://api.cloudconvert.com";
    private static $whichToUse;
    private static $apiKey;
    private static $configPath; 
    private static $uploadsPath; 


    public function __construct() {

        parent::__construct();
    }

    ////////////////////////////////    TREVOR START    ///////////////////////////////////////////////////////////////
    public function directorySearch(){

        $params = $_POST;
        $searchBar = $this->getSearchBar($params);

        $selectedOccupation = $params["Ocdla_Occupation_Field_Type__c"] != "All Occupations/Fields" ? $params["Ocdla_Occupation_Field_Type__c"] : null;
        $selectedInterest = $params["areaOfInterest"] != "All Areas of Interest" ? $params["areaOfInterest"] : null;

        if($selectedOccupation == null) unset($params["Ocdla_Occupation_Field_Type__c"]);
        if($selectedInterest == null) unset($params["areaOfInterest"]);

        $query = $this->buildDirectoryQuery($params);

        $api = $this->loadForceApi();
        $result = $api->query($query);

        if(!$result->success()) throw new Exception($result->getErrorMessage());

        $records = $result->getRecords();

        
        $contacts = Contact::from_query_result_records($records);


        $tpl = new Template("directory-list");
        $tpl->addPath(__DIR__ . "/templates");

        return $tpl->render(array(
            "count"             => count($contacts),
            "search"            => $searchBar,
            "contacts"          => $contacts,
            "showQuery"         => true,
            "query"             => $query
        ));
    }


    public function showDirectoryContact($id){

        $api = $this->loadForceApi();

        $query = "SELECT Id, FirstName, LastName, MailingCity, Ocdla_Current_Member_Flag__c, MailingState, Phone, Email, Ocdla_Occupation_Field_Type__c, Ocdla_Organization__c, (SELECT Interest__c from AreasOfInterest__r) FROM Contact WHERE Id = '$id'";

        $records = $api->query($query)->getRecords();

        $contacts = Contact::from_query_result_records($records);


        $tpl = new Template("directory-single");
        $tpl->addPath(__DIR__ . "/templates");

        return $tpl->render(array(
            "contacts"          => $contacts,
            "isSingle"          => true,
            "showQuery"         => true,
            "query"             => $query
        ));


    }

    public function getSearchBar($params){

        $search = new Template("directory-search");
        $search->addPath(__DIR__ . "/templates");
        

        return $search->render(array(
            "occupationFields"   => $this->getOccupationFieldsDistinct(),
            "selectedOccupation" => $params["Ocdla_Occupation_Field_Type__c"],
            "areasOfInterest"    => $this->getAreasOfInterest(),
            "selectedInterest"   => $params["areaOfInterest"],
            "firstName"          => $params["FirstName"],
            "lastName"           => $params["LastName"],
            "companyName"        => $params["Ocdla_Organization__c"],
            "city"               => $params["MailingCity"],
            "includeExperts"     => $params["IncludeExperts"]
        ));
    }
    public function buildDirectoryQuery($params){

        $includeExperts = $params["IncludeExperts"];
        unset($params["IncludeExperts"]);

        $areaOfInterest = $params["areaOfInterest"];
        unset($params["areaOfInterest"]);

        $onlyExperts = $params["only-experts"];
        unset($params["only-experts"]);


        $query = "SELECT Id, FirstName, LastName, MailingCity, Ocdla_Current_Member_Flag__c, MailingState, Phone, Email, Ocdla_Occupation_Field_Type__c, Ocdla_Organization__c, (SELECT Interest__c from AreasOfInterest__r) FROM Contact";
        
        $conditions = array();
        foreach($params as $field => $value){

            if(!empty($value)){

                $conditions[] = "$field LIKE '%$value%'";
            }
        }

        if(!empty($includeExperts) && $includeExperts) $conditions[] = "Ocdla_is_expert_witness__c = false";

        if($onlyExperts) $conditions[] = "Ocdla_is_expert_witness__c = True";

        // If there is an area of interest selected, query for all of the contacts who have set that as one of their areas of intersts.
        // Only use those contacts in you query.
        if(!empty($areaOfInterest)){

            $conditions[] = "id IN (SELECT Contact__c FROM AreaOfInterest__c WHERE Interest__c = '$areaOfInterest')";
        }

        $conditions[] = "Ocdla_Current_Member_Flag__c = True";

        if(!empty($conditions)){

            $query .= " WHERE " . implode(" AND ", $conditions);
        }

        $query.= " AND (NOT Email LIKE '%qq.com%')";

        $query .= " ORDER BY LastName";

        return $query;
    }

    public function getOccupationFieldsDistinct(){

        $query = "SELECT Ocdla_Occupation_Field_Type__c FROM Contact ORDER BY Ocdla_Occupation_Field_Type__c DESC";

        $api = $this->loadForceApi();

        $result = $api->query($query);

        if(!$result->isSuccess()) throw new Exception($result->getErrorMessage());

        $records = $result->getRecords();

        $areas = array();

        foreach($records as $record){

            $area = $record["Ocdla_Occupation_Field_Type__c"];

            if(!in_array($area, $areas)){

                array_unshift($areas, $area);
            }
        }

        return $areas;
    }


    public function getAreasOfInterest(){

        $pickListId = "0Nt5b000000CbzK";

        $req = $this->loadForceApi();

        $url = "/services/data/v39.0/tooling/sobjects/GlobalValueSet/$pickListId";

        $resp = $req->send($url);

        $picklistValues = $resp->getBody()["Metadata"]["customValue"];

        $areasOfInterest = array();
        foreach($picklistValues as $value){

            $areasOfInterest[] = $value["valueName"];
        }

        return $areasOfInterest;
    }




    ////    Expert Witnesses    /////

    public function directorySearchExperts(){

        $search = new Template("expert-search");
        $search->addPath(__DIR__ . "/templates");

        $primaryFields = $this->getPrimaryFields();

        $search->render(array(
            "primaryFields" => $primaryFields
        ));


        $expertQuery = $this->buildDirectoryQuery(array("only-experts" => true));

        $expertRecords = $this->getExperts($expertQuery);

        $experts = Contact::from_query_result_records($expertRecords);

        $tpl = new Template("expert-list");
        $tpl->addPath(__DIR__ . "/templates");

        return $tpl->render(array(
            "search"   => $search,
            "contacts" => $experts
        ));
    }

    public function getPrimaryFields(){

        $endpoint = "/services/data/v23.0/sobjects/Contact/describe";
        $api = $this->loadForceApi();
        $resp = $api->send($endpoint);
        $primaryFieldObjs = $resp->getBody()["fields"];

        $primaryFields = array();

        foreach($primaryFieldObjs as $field){

            if($field["name"] == "Ocdla_Expert_Witness_Primary__c"){

                $values = $field["picklistValues"];

                foreach($values as $value){

                    $primaryFields[] = $value["value"];
                }
            }
        }

        return $primaryFields;
    }

    public function getExperts($query){

        
        $api = $this->loadForceApi();
        $resp = $api->query($query);

        if(!$resp->isSuccess()) throw new Exception($resp->getErrorMessage());

        return $resp->getRecords();
    }

    ////////////////////////////////    TREVOR END        ///////////////////////////////////////////////////////////////

    /******************CLoud Convert API Implementation***********************************/


    public function getDirectoryLinks(){

        try {

            $filenames = $this->listPdfFiles(self::$uploadsPath);
            
            $directoryLinks = array();        
            foreach ($filenames as $key => $filename) {
                
                $directoryLinks[$filenames[$key]] ="/directory/pdfs/".$filenames[$key];
            }

        }catch(\Throwable $th) {

            $error = "Error getting directory pdfs: " . $th->getMessage();
        }

		$tpl = new Template("directoryLinks");
		$tpl->addPath(__DIR__ . "/templates");

		return $tpl->render(array(
            "directoryLinks" => $directoryLinks,
            "error" => $error
        ));
    }

    public function listPdfFiles($path){

        if(!file_exists($path)){

            throw new Exception(" no files found in server");
        }
        
        $filenames = scandir($path);

        if($filenames === false){

            throw new Exception(" no files found in server");
        }

        $filenames = array_diff($filenames,array("." , ".."));

        foreach($filenames as $key => $filename){

            $filenames[$key] = substr($filename, 0, strrpos($filename,"."));
        }

        return $filenames;
    }

    public function admin(){

        $cloudConvertLinks = array();
        
        try {

            $filenames = $this->listPdfFiles(self::$configPath);

            foreach ($filenames as $key => $filename) {
                $cloudConvertLinks[$filenames[$key]] = $_SERVER["HTTP_HOST"]."/directory/execute/".$filenames[$key];
            }

        }catch(\Throwable $th) {

            $error = "Error getting Pdf configurations: " . $th->getMessage();
        }

        $tpl = new Template("createDirectoryLinks");
		$tpl->addPath(__DIR__ . "/templates");

		return $tpl->render(array(
            "cloudConvertLinks" => $cloudConvertLinks,
            "error" => $error
        ));
    }

    public function addCloudConvertJob($name){

        $modulePath = BASE_PATH. module_path();
        $body = file_get_contents(self::$configPath.DIRECTORY_SEPARATOR.$name.".json");

        $req = new \Http\HttpRequest();
        $req->setUrl(self::$whichToUse."/v2/jobs");
        $req->setMethod("POST");
        $req->addHeader(new HttpHeader("Authorization","Bearer ".self::$apiKey));
        $req->addHeader(new HttpHeader("Content-type","application/json"));
        $req->setBody($body);

        $config = array(
            "returntransfer" 		=> true,
            "useragent" 			=> "Mozilla/5.0",
            "followlocation" 		=> true,
            "ssl_verifyhost" 		=> false,
            "ssl_verifypeer" 		=> false
        );

        $http = new \Http\Http($config);
    
        $resp = $http->send($req, true);

        if($resp->getStatusCode() < 300){
            
            $body = json_decode($resp->getBody());
            $jobId = $body->data->id;
            $this->getCloudConvertJobStatus($jobId);
            
        }

        var_dump($resp);
        exit;
    }

    public function getCloudConvertJobStatus($jobId){

        //status = waiting || processing || finished || error

        $req = new \Http\HttpRequest();
        $req->setUrl(self::$whichToUse."/v2/jobs/".$jobId."/wait");
        $req->setMethod("GET");
        $req->addHeader(new HttpHeader("Authorization","Bearer ".self::$apiKey));
        //$req->addHeader(new HttpHeader("Content-type","application/json"));

        $config = array(
            "returntransfer" 		=> true,
            "useragent" 			=> "Mozilla/5.0",
            "followlocation" 		=> true,
            "ssl_verifyhost" 		=> false,
            "ssl_verifypeer" 		=> false
        );

        $http = new \Http\Http($config);

        $resp = $http->send($req, true);

        if($resp->getStatusCode() < 300){

            $body = json_decode($resp->getBody());

            if($body->data->status == "finished") {

                $files = $body->data->tasks[0]->result->files;
                $this->getCloudConvertPDF($files[0]->url,$files[0]->filename);

            } else {

                var_dump($resp);
            }
            
        } else {

            var_dump($resp);
        }
        
        exit;
    }

    public function getCloudConvertPDF($url,$filename){
        //echo ("<a href=\"".$url."\">URL</a><br/>");
        
        // $req = new \Http\HttpRequest();
        // $req->setUrl($url);
        // $req->setMethod("GET");
        // $req->addHeader(new HttpHeader("Content-type","text/\html"));
        // $config = array(
        //     "returntransfer" 		=> true,
        //     "useragent" 			=> "Mozilla/5.0",
        //     "followlocation" 		=> true,
        //     "ssl_verifyhost" 		=> false,
        //     "ssl_verifypeer" 		=> false
        // );

        // $http = new \Http\Http($config);

        // $resp = $http->send($req, true);
        // if($resp->getStatusCode() < 300){
        //     return $resp->getBody();
        // }
        // echo($resp->getBody());

        $PDFData = file_get_contents($url);        
        $this->saveCloudConvertPDF($PDFData,$filename);
        //$this->downloadPDF($filename);

        exit;
    }

    public function saveCloudConvertPDF($PDFData, $filename){

        if(!file_exists(self::$uploadsPath)){

            mkdir(self::$uploadsPath, 0777, true);
        }

        $filename = self::$uploadsPath.DIRECTORY_SEPARATOR.$filename;
        $result = file_put_contents($filename, $PDFData);

        if($result) echo("<h4>Added PDF: $filename</h4>");

        else echo("<h4>Error Adding PDF: $filename'</h4>");

        exit;
    }

    //Users story: download alpha pdf or city pdf, admin manualy click off city or alpha pdf.
    public function downloadPdf($filename){

        $PDFData = file_get_contents(self::$uploadsPath.DIRECTORY_SEPARATOR.$filename.".pdf");

        if(!$PDFData){

            header('Content-Type: text/html');
            echo("<h4>Error Getting PDF: $filename'</h4>");
            exit;
        }

        header('Content-Type: application/pdf');
        header('Cache-Control: public, must-revalidate, max-age=0'); // HTTP/1.1
        header('Pragma: public');
        header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
        header('Content-Length: '.strlen($PDFData));

        //not as a file download
        header('Content-Disposition: inline; filename="'.basename($filename).'";');

        ob_clean(); 
        flush();   

        echo($PDFData);
        exit;
    }

    public function jobWebhook(){

        $req = $this->getRequest();
        $post = json_decode($req->getBody());
    }

}