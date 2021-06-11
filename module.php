<?php

use Http\Http as Http;
use File\FileList as FileList;
use File\File as File;
use Salesforce\SalesforceAttachment as SalesforceAttachment;
use Salesforce\OAuthRequest as OAuthRequest;
use Http\HttpRequest as HttpRequest;
use Salesforce\OAuthResponse as OAuthResponse;
use Salesforce\RestApiRequest as RestApiRequest;
use Http\HttpHeader as HttpHeader;

// Test module name should match it's corresponding module name, ex.: 'appserver-directory'
class DirectoryModule extends Module
{
    private static $sandboxUrl = "https://api.sandbox.cloudconvert.com";
    private static $productionUrl = "https://api.cloudconvert.com";
    private static $whichToUse;
    private static $apiKey;
    private static $configPath; 
    private static $uploadsPath; 


    public function __construct()
    {

        parent::__construct();
        self::$whichToUse = self::$sandboxUrl;
        if ( self::$whichToUse == self::$sandboxUrl){
            self::$apiKey = CLOUD_CONVERT_SANDBOX_API_KEY;
        }else{
            self::$apiKey = CLOUD_CONVERT_API_KEY;
        }
        //C:\wamp64\www\appserver\content\uploads\modules\directory\CurrentMembersAlpha.pdf
        //C:\wamp64\www\appserver\content\uploads\modules\directory
        self::$configPath = path_to_modules_config().DIRECTORY_SEPARATOR."directory";
        self::$uploadsPath = path_to_modules_upload().DIRECTORY_SEPARATOR."directory";
        if(!file_exists(self::$uploadsPath)){
            mkdir(self::$uploadsPath, 0777, true);
        }
        if(!file_exists(self::$configPath)){
            mkdir(self::$uploadsPath, 0777, true);
        }
        
    }

    // New callback that will dislay committees and committee members
    public function testDisplayCommitteeMembers()
    {
        // List all the committees
        $api = $this->loadForceApi();
        //$results = $api->query("SELECT id, name from committee__c");
        // List commiittees and related contact info for each member
        $results = $api->query("SELECT id, Name, (SELECT Contact__r.Id, Contact__r.Title, Contact__r.Name, Role__c, Contact__r.Email, Contact__r.Phone FROM Relationships__r) FROM Committee__c");
        print "<pre>";
        print print_r($results, true);
        print "</pre>";
    }

    // TODO:
    // Then display all the members of a specific committee (links)
    // Then display the personal info for a single member

    // Queries salesforce for all "Committee" objects and related members, and renders the objects in a template.
    public function home()
    {
        $tpl = new Template("committee-list");
        $tpl->addPath(__DIR__ . "/templates");

        $api = $this->loadForceApi();

        // Query for committee records and members belonging to each committe
        $resp = $api->query("SELECT Id, Name, (SELECT Contact__r.Id, Contact__r.Title, Contact__r.Name, Role__c, Contact__r.Phone, Contact__r.Email FROM Relationships__r) FROM Committee__c");
        if (!$resp->isSuccess()) {

            var_dump($resp);
            exit;
        }
        // Creates an array for holding "Committee__c" objects.
        $committeeRecords = $resp->getRecords();
        //var_dump($committeeRecords);
        //exit;

        $formattedCommitteeRecords = $this->includeMemberInfo($committeeRecords);
        //$testContactPath = $committeeRecords[0]['members'][0]['Relationships__r']['records']; //[0]['Contact__r'];
        //var_dump($committeeRecords); // TESTING
        //exit;

        return $tpl->render(array(
            "committees" => $formattedCommitteeRecords,
            "isAdmin" => false,
            "isMember" => true // is_authenticated()
        ));
    }

    // TESTING API FROM WP
    public function home2()
    {
        //$tpl = new Template("committee-list");
        //$tpl->addPath(__DIR__ . "/templates");

        $api = $this->loadForceApi();

        // Query for committee records and members belonging to each committe
        $resp = $api->query("SELECT Id, Name, (SELECT Contact__r.Id, Contact__r.Title, Contact__r.Name, Role__c, Contact__r.Phone, Contact__r.Email FROM Relationships__r) FROM Committee__c");
        if (!$resp->isSuccess()) {

            var_dump($resp);
            exit;
        }
        // Creates an array for holding "Committee__c" objects.
        $committeeRecords = $resp->getRecords();
        //var_dump($committeeRecords);
        //exit;

        $formattedCommitteeRecords = $this->includeMemberInfo($committeeRecords);
        //$testContactPath = $committeeRecords[0]['members'][0]['Relationships__r']['records']; //[0]['Contact__r'];
        //var_dump($committeeRecords); // TESTING
        //exit;

        $committees = $formattedCommitteeRecords;

        return $committees; // array("committees" => $formattedCommitteeRecords); // Returning a formatted array that I can use to test my WP plugin
    }



    // This function parses an array with 'raw' data containing committee and member information
    // It then takes necessary attributes and puts them into a new formatted 'human-friendly' array
    public function includeMemberInfo($committeeRecords)
    {
        $committees = array(); // Initializing an empty array that will hold all the necessary data

        foreach ($committeeRecords as $record) {
            $committee = []; // Initializing an empty array for a single committee object (local to the loop)
            // creating and assigning an array that contains all members (raw data) of the committee that is being added
            $members = $record['Relationships__r']['records'];

            $committee["Name"] = $record["Name"]; // getting a committee name
            foreach ($members as $rec) {
                $member = array( // Settting each member's attributes for the committee
                    "Id" => $rec["Contact__r"]["Id"],
                    "Title" => $rec["Contact__r"]["Title"],
                    "Role" => $rec["Role__c"],
                    "Name" => $rec["Contact__r"]["Name"],
                    "Phone" => $rec["Contact__r"]["Phone"],
                    "Email" => $rec["Contact__r"]["Email"]
                );
                $committee["members"][] = $member; // adding a member entry to the 'members' array
                //var_dump($member);
                //exit;
            }
            $committees[] = $committee; // filling 'committees' array with committee/members data after every itireation
        }
        //var_dump($committees); // TESTING
        //exit;
        return $committees;
    }
    

    /******************CLoud Convert API Implementation***********************************/


    public function getDirectoryLinks(){

        //$directoryLinks = array();
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
            //mkdir($path, 0777, true);
            throw new Exception(" no files found in server");
        }
        
        $filenames = scandir($path);

        if($filenames === false){
            throw new Exception(" no files found in server");
        }//elseif()count($filenames) == 2

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
            if($body->data->status == "finished"){
                $files = $body->data->tasks[0]->result->files;
                $this->getCloudConvertPDF($files[0]->url,$files[0]->filename);
            }else{
                var_dump($resp);
            }
            
        }else{
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

        if($result){
            echo("<h4>Added PDF: $filename</h4>");
            
        }

        else            
            echo("<h4>Error Adding PDF: $filename'</h4>");
        exit;
    }

    //users story: download alpha pdf or city pdf
    //admin manualy click off city or alpha pdf
        //minimal setup is var_dump of results

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
            //
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