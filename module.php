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
        $results = $api->query("SELECT id, Name, (SELECT Contact__r.Title, Contact__r.Name, Role__c, Contact__r.Email, Contact__r.Phone FROM Relationships__r) FROM Committee__c");
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
        $resp = $api->query("SELECT Id, Name, (SELECT Contact__r.Title, Contact__r.Name, Role__c, Contact__r.Phone, Contact__r.Email FROM Relationships__r) FROM Committee__c");
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
                    "Title" => $rec["Contact__r"]["Title"],
                    "Role" => $rec["Role__c"],
                    "Name" => $rec["Contact__r"]["Name"],
                    "Phone" => $rec["Contact__r"]["Phone"],
                    "Email" => $rec["Contact__r"]["Email"]
                );
                $committee["members"][] = $member; // adding a member entry to the 'members' array
            }
            $committees[] = $committee; // filling 'committees' array with committee/members data after every itireation
        }
        //var_dump($committees); // TESTING
        //exit;
        return $committees;
    }
}