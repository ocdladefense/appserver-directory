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
        $results = $api->query("SELECT id, name from committee__c");
        print "<pre>";
        print print_r($results, true);
        print "</pre>";

        // Then display all the members of a specific committee (links)
        // Then display the personal info for a single member
    }
}