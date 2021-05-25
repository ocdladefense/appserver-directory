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

    public function __construct()
    {
        parent::__construct();
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

    public function getMembersAlphaPDF(){
        $modulePath = BASE_PATH. module_path();
        $body = file_get_contents($modulePath .DIRECTORY_SEPARATOR. "membersAlpha.json");
        $api_key = "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIxIiwianRpIjoiNGY3MTBiYjQ2MDRkZjUwZDM5ZGRiMzZhZTU5YTI5NWRhYjA3ZDAyMTU4MDRkNmYwMmRiYzdhZTYwMGRlMDQ2N2NlNjMzMTRmNTdiNDBmNjMiLCJpYXQiOjE2MjE5ODQyMjguMTQxMDgsIm5iZiI6MTYyMTk4NDIyOC4xNDEwODMsImV4cCI6NDc3NzY1NzgyOC4xMTQzMTcsInN1YiI6IjUwMzY3MDY0Iiwic2NvcGVzIjpbInVzZXIud3JpdGUiLCJ0YXNrLnJlYWQiLCJ0YXNrLndyaXRlIiwid2ViaG9vay5yZWFkIiwid2ViaG9vay53cml0ZSIsInByZXNldC5yZWFkIiwicHJlc2V0LndyaXRlIiwidXNlci5yZWFkIl19.GoWuTjZxWwnADd2RIxIMuUJDFr3qs-x48hrsQhVjMCOorm8VYsbQW8ja3wcPfjzeBgE-hdYXs6mQ1fWnibCczH8fd7vheMPbJcPrlhqlaTFB8usGxT3a4-mAdKySYJtZUzcrrQyIONY9AfmoBHOftzjXt-UyUWuNVkyMgmWeHI5a3XPg5Lt0vvbJuweOhQ-tDSs_adzeQzumCz9AS8MPAdleeQU3SlY0zcqqN6A2IDffHTOhafNczb-j1zF7sF30whjCR8ZwvIEdetLbZMzsGbIQ3H6t-YwM0cHXuECbOMZgGMxl729Yd0NHmRtIsDSlT90bUIsl6NnvC3HFq0-YeE-b3aIh9goskLUCv0tmq_cMTqg4LM3LbbzTT_9I9sAeyGDiYtGusqNGP0oSvL-Geu4zCQTRBRESNtET2NY0iZbqrjjBuavXO28LIIIXZvy73vsEOEDR9SIR8-pr-8KxXiNVl4QwIu8GLsTiVTQsVc57cKv_as38Y6sQlcdPHzx-jBK9vgWmAfTApp6BySS8-9-edxQ4YmWZqJZOIfc-GDTUfzY8lVHCBFV8X447wEkQvIvkhTEMY5vb1qLa5EUENhYr-sha1XbOhidzTIaeuOVskME4_djRViqWDAxWKPLicukcr5zUJ8t5zGQGmO-5QjPPp_Lqtm6DgG15eMboRoY";



        $req = new \Http\HttpRequest();
        $req->setUrl("https://api.sandbox.cloudconvert.com/v2/jobs");
        $req->setMethod("POST");
        $req->addHeader(new HttpHeader("Authorization","Bearer ".$api_key));
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
            var_dump($resp->getBody());
            exit;
            
        }
        var_dump($resp);
        exit;
    }

}