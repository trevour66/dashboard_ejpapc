<?php

namespace App\Google;

use DateTimeImmutable;
use Error;
use Exception;

use Google\Client;
use Google\Service\Drive;
use Google\Service\Sheets;
use Google\Service\Drive\DriveFile;
use Google\Service\Sheets\ValueRange;
use Google\Service\Sheets\UpdateCellsRequest;
use Google\Service\Sheets\BatchUpdateSpreadsheetRequest;
use Google\Service\Sheets\BatchUpdateValuesRequest;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;


class GoogleSheetService {
    private $googleClient = null;

    private $sheetService = null;
    private $driveService = null;

    private $questionaireFilesContainerId = "1yE14dFH2eZ4aOqErwvy-WW4VbtJm86mY";

    private $credential_filename = "service_account.json";
    
    private $intakeSceduledSpreadsheetId = "1f41XEI3_glR0V1qCCusr8p91gspJNtMIh_pxVP_qFu0";

    private $sheetId = "110741365"; 
    private $sheetRange = "Pre-Consult Checklist"; //Just the name would do as this would automatically defualt to the last row
    private $emailColumnRange = "Pre-Consult Checklist!B2:B";
    private $fullSheetRange = "Pre-Consult Checklist!A2:M";
    private $options = [
        'valueInputOption' => 'USER_ENTERED'
    ];

    private $dataChunk = [];

    public $row_number = '';
    public $last_email_comm_date = '';
    public $status = '';
    public $possible_dup = false;

    public $emailUniquesId = '';
    public $submissionDate = '';
    public $leadName = '';
    public $leadEmail = '';
    public $leadPOE = '';
    public $leadAdminRems = '';
    public $leadTimeline = '';
    public $matter_name = '';
    public $communication_count = 0;
    public $agency_names = '';
    

    public function __construct(){
        $paths = config('filesystems.disks.local.credentials'). DIRECTORY_SEPARATOR . $this->credential_filename;
        
        $credential_json = file_get_contents($paths);         
        $credential_json_data = json_decode($credential_json,true); 

        $this->googleClient = new Client([
                'application_name' => "EJP APC File Sharing",
                'include_granted_scopes' => true,
                'scopes' => [
                    Drive::DRIVE, 
                    Sheets::SPREADSHEETS, 
                ]
            ]);
            
        $this->googleClient->setAccessType('offline');
        $this->googleClient->setAuthConfig($credential_json_data);

        $this->sheetService = new Sheets($this->googleClient);     
        $this->driveService = new Drive($this->googleClient);
    }

    public function set_emailId($id = '') {
        if($id ?? false){
            $this->emailUniquesId = $id;
        }else {
            $seed = uniqid('', true);
            $this->emailUniquesId = hash('md2', $seed);
        }
    }

    public function set_communication_count($count) {
        if($count ?? false){
            $this->communication_count = $count;
        }else {
            $this->communication_count = 0;
        }
    }

    public function increment_communication_count() {
        if($this->communication_count ?? false){
            $this->communication_count = (integer) $this->communication_count + 1;
        }else {
            $this->communication_count = 1;
        }
    }

    public function set_clio_matter_name($clio_matter_name = '') {
        if($clio_matter_name ?? false){
            $this->matter_name = $clio_matter_name;
        }
    }  
    
    public function set_agency_names($agency_names = '') {
        if($agency_names ?? false){
            $this->agency_names = $agency_names;
        }
    }  

    public function set_lastEmailCommDate($date = '') {
        if($date ?? false){            
            $this->last_email_comm_date = $date;
        }else {
            
            $this->last_email_comm_date = new DateTimeImmutable();
            $this->last_email_comm_date = $this->last_email_comm_date->format('d-m-Y') ?? '';
        }
    }

    public function set_submissionDate($date = ""){
        if($date ?? false){                        
            $this->submissionDate = $date;
        }else {                    
            $this->submissionDate = new DateTimeImmutable();
            $this->submissionDate = $this->submissionDate->format('d-m-Y') ?? '';
        }
        
    }

    public function set_leadName ($lead_firstname, $lead_lastname){
        $lead_name = ($lead_firstname . ' ' ?? '') . ($lead_lastname ?? '') ;
        $this->leadName = $lead_name;
    }

    public function set_leadFullName ($lead_fullname){
        $lead_name = $lead_fullname ?? '';
        $this->leadName = $lead_name;
    }

    public function set_leadEmail ($lead_email){
        $lead_email = $lead_email ?? '' ;
        $this->leadEmail = $lead_email;
    }

    public function set_poe_proof_of_employment ($poe_proof_of_employment, $poe_proof_of_employment_upload_status){
        $poe_proof_of_employment = $poe_proof_of_employment ?? '' ;
        $poe_proof_of_employment_upload_status = $poe_proof_of_employment_upload_status ?? '';

        if(
            $poe_proof_of_employment == config('app.possible_poe_submission_values.proofNotAvailable')
        ){
            $this->leadPOE = config('app.possible_poe_column_values.unableToProvideReviewRequired');
        }else if(
            $poe_proof_of_employment == config('app.possible_poe_submission_values.canProvideProofLater')
        ){
            $this->leadPOE = config('app.possible_poe_column_values.pending');            
        }else if(
            $poe_proof_of_employment == config('app.possible_poe_submission_values.canProvideProofNow') 
        ){
            $this->leadPOE = config('app.possible_poe_column_values.provided');            
        }else{
            try {
                //code...
                $this->leadPOE = (string) $poe_proof_of_employment;
            } catch (\Throwable $th) {
                //throw $th;
            }
        }

        if(!$poe_proof_of_employment ){
            $this->leadPOE = config('app.possible_poe_column_values.unableToProvideReviewRequired');
        }
    }

    public function set_admin_rems_admin_agencies ($admin_rems_admin_agencies){
        $admin_rems_admin_agencies = $admin_rems_admin_agencies ?? false ;
        
        if($admin_rems_admin_agencies && $admin_rems_admin_agencies != "false" ){
            $this->leadAdminRems = config('app.possible_admin_rems_column_values.ApplicableDocumentNotUploaded');
        }else{
            $this->leadAdminRems = config('app.possible_admin_rems_column_values.notApplicable');
        }
    }

    public function set_timeline ($isAnyTimelineTiedToThisEmail, $isEntryWithSameEmailAlreadyInSheet) {
        $isAnyTimelineTiedToThisEmail = $isAnyTimelineTiedToThisEmail ?? false;
        $isEntryWithSameEmailAlreadyInSheet = $isEntryWithSameEmailAlreadyInSheet ?? [];

        if(! $isAnyTimelineTiedToThisEmail){
            $this->leadTimeline = config('app.possible_timeline_column_values.noSubmission');
        }else if(
            $isAnyTimelineTiedToThisEmail && ($isEntryWithSameEmailAlreadyInSheet['is_email_found'] ?? false)
        ){
            $this->leadTimeline = config('app.possible_timeline_column_values.submittedMultipleEntry');
        }else if(
            $isAnyTimelineTiedToThisEmail && !($isEntryWithSameEmailAlreadyInSheet['is_email_found'] ?? false)
        ){
            $this->leadTimeline = config('app.possible_timeline_column_values.submitted');
        }
    }

    public function set_possibleDup($isEntryWithSameEmailAlreadyInSheet, $isNew = false){
        // logger(print_r($isEntryWithSameEmailAlreadyInSheet, true));
        $columns_found_in = $isEntryWithSameEmailAlreadyInSheet["columns_found_in"] ?? [];

        switch ($isNew) {
            case true:                
                if(count ($columns_found_in) > 0){
                    $this->possible_dup = true;            
                }else {
                    $this->possible_dup = false;            
                }
                break;
            
            case false:                
                if(count ($columns_found_in) > 1){
                    $this->possible_dup = true;            
                }else {
                    $this->possible_dup = false;            
                }
                break;
        }
        
    }

    public function getRowInfo(){
        return [
            'row_number' => $this->row_number ?? '',
            'submission_date' => $this->submissionDate ?? '',
            'lead_email' => $this->leadEmail ?? '',
            'lead_name' => $this->leadName ?? '',
            'lead_POE' => $this->leadPOE ?? '',
            'lead_timeline' => $this->leadTimeline ?? '',
            'lead_adminRems' => $this->leadAdminRems ?? '',
            'email_link_id' => $this->emailUniquesId ?? '',
            'last_email_comm_date' => $this->last_email_comm_date ?? '',
            'status' => $this->status ?? '',
            'possible_dup' => $this->possible_dup ?? '',
            'matter_name' => $this->matter_name ?? '',
            'communication_count' => $this->communication_count ?? '',
            'agency_names' => $this->agency_names ?? '',
                        
        ];

    }

    public function saveNewSubmission(){
        try {
            $valueRangeWrapper = new ValueRange();

            $row = [[
                $this->submissionDate,
                $this->leadEmail,
                $this->leadName,
                $this->leadPOE,
                $this->leadTimeline,
                $this->leadAdminRems,
                $this->emailUniquesId,
                $this->last_email_comm_date, //last comm date
                $this->status,
                $this->possible_dup,
                $this->matter_name,
                $this->communication_count,
                $this->agency_names,
            ]];

            $valueRangeWrapper->setValues($row);
            
            $this->sheetService->spreadsheets_values->append($this->intakeSceduledSpreadsheetId, $this->sheetRange, $valueRangeWrapper, $this->options);   
            
            return true;
        } catch (Exception $e) {
            logger(print_r($e->getMessage(), true));
            return  false;
        }
    }

    public function searchSheetByEmail($email){
        $email = (string) $email ?? '';
        
        $search_result = [
            'email' => $email,
            'columns_found_in' => [],
            'is_email_found' => false
        ];

        if($email == ''){
            return $search_result ;
        }

        $response = $this->sheetService->spreadsheets_values->get($this->intakeSceduledSpreadsheetId, $this->emailColumnRange);

        $response_values = $response->getValues();
        
        if (empty($response_values)) {
           return $search_result ;
        }    
        
        $row_number = 2;

        foreach ($response_values as $row) {
            if($row[0] === $email){
                array_push($search_result['columns_found_in'], $row_number);    
                $search_result['is_email_found'] = true            ;
            }
            
            $row_number++;            
        }

        // logger(print_r($search_result, true));
        return $search_result ;

    }

    public function searchSheet_getRowByEmail($email){
        $email = (string) $email ?? '';
        
        $search_result = null;

        if($email == ''){
            return $search_result ;
        }

        $response = $this->sheetService->spreadsheets_values->get($this->intakeSceduledSpreadsheetId, $this->fullSheetRange);

        $response_values = $response->getValues();
        
        if (empty($response_values)) {
           return $search_result ;
        }    
        
        $row_number = 2;

        foreach ($response_values as $row) {
            if($row[1] === $email){
                $search_result = [                     
                    'row_number' => $row_number ?? '',
                    'submission_date' => $row[0] ?? '',
                    'lead_email' => $row[1] ?? '',
                    'lead_name' => $row[2] ?? '',
                    'lead_POE' => $row[3] ?? '',
                    'lead_timeline' => $row[4] ?? '',
                    'lead_adminRems' => $row[5] ?? '',
                    'email_link_id' => $row[6] ?? '',
                    'last_email_comm_date' => $row[7] ?? '',
                    'status' => $row[8] ?? '',
                    'possible_dup' => $row[9] ?? '',
                    'matter_name' => $row[10] ?? '', 
                    'communication_count' => $row[11] ?? '', 
                    'agency_names' => $row[12] ?? '',                     
                                       
                ];

                break;
            }
            $row_number++;
        }

        return $search_result ;

    }

    public function searchSheetByEmailLinkId($emailLinkId){
        $emailLinkId = (string) $emailLinkId ?? '';
        
        $search_result = null;

        if($emailLinkId == ''){
            return $search_result ;
        }

        $response = $this->sheetService->spreadsheets_values->get($this->intakeSceduledSpreadsheetId, $this->fullSheetRange);

        $response_values = $response->getValues();
        
        if (empty($response_values)) {
           return $search_result ;
        }    
        
        $row_number = 2;

        foreach ($response_values as $row) {
            if($row[6] === $emailLinkId){
                $search_result = [                     
                    'row_number' => $row_number ?? '',
                    'submission_date' => $row[0] ?? '',
                    'lead_email' => $row[1] ?? '',
                    'lead_name' => $row[2] ?? '',
                    'lead_POE' => $row[3] ?? '',
                    'lead_timeline' => $row[4] ?? '',
                    'lead_adminRems' => $row[5] ?? '',
                    'email_link_id' => $row[6] ?? '',
                    'last_email_comm_date' => $row[7] ?? '',
                    'status' => $row[8] ?? '',
                    'possible_dup' => $row[9] ?? '',
                    'matter_name' => $row[10] ?? '',                    
                    'communication_count' => $row[11] ?? '',   
                    'agency_names' => $row[12] ?? ''                    
                ];

                break;
            }
            $row_number++;
        }

        return $search_result ;

    }

    private function searchFolder_createNewIfNotFound($emailAsName){
        try {
            $emailAsName = $emailAsName ?? false;

            if(!$emailAsName){
                return false;
            }

            $folderAsAFile = null;
            $pageToken = null;

            do {
                $response = $this->driveService->files->listFiles(array(
                    'q' => "mimeType='application/vnd.google-apps.folder' and name='". $emailAsName ."' and '".$this->questionaireFilesContainerId."' in parents",
                    'spaces' => 'drive',
                    'pageToken' => $pageToken,
                    'fields' => 'nextPageToken, files(id, name)',
                ));            

                $folderAsAFile = $response->files[0] ?? false;

                $pageToken = $response->pageToken;
            } while (
                ! $folderAsAFile &&
                $pageToken != null
            );

            $folderAsAFile = $folderAsAFile ?? false;

            if($folderAsAFile){
                return $folderAsAFile;
            }             


            $file = new DriveFile();
            $file->setName($emailAsName);
            $file->setParents([$this->questionaireFilesContainerId]);
            $file->setMimeType('application/vnd.google-apps.folder');

            $folderAsAFile = $this->driveService->files->create($file);

            return $folderAsAFile;

        } catch(Exception $e) {
            return false;
        }
    }

    public function uploadToDrive($emailAsName, $fileName, $filePath, $extension){
        try {            
            if(
                !$emailAsName ||
                !$fileName ||
                !$filePath
            ){
                throw new Error('Asset needed before upload not complete');
            }

            $userFolder = $this->searchFolder_createNewIfNotFound($emailAsName) ?? false;

            if(!$userFolder){
                throw new Exception('User folder not available');
            }

            if(! (isset($userFolder->id) ?? false)){
                throw new Exception('User folder not available');
            }

            $fileMetadata = new Drive\DriveFile(
                array(
                    'name' => $fileName,
                    'parents' => [
                        $userFolder->id
                    ]
                )
            );

            $content = Storage::get($filePath);

            $file = $this->driveService->files->create($fileMetadata, array(
                'data' => $content,
                'uploadType' => 'multipart',
                'fields' => 'id,webViewLink'));

            if( $file->id ?? false){
                return $file;
            }else{
                return false;
            }
        } catch(Exception $e) {
            return false;            
        } 

    }

    public function isAnyTimelineTiedToThisEmail($email){

        try {
            $email = $email ?? '' ;
            
            if($email == ''){
                throw new Error('email not provided for use by getTimelineByEmail');
            }

            $response = Http::post('https://ejpapc.com/api/get-timeline.php', [
                'email' => $email
            ]);

            if(!$response->ok()){
                return false;
            }

            $timeline_exist = $response["timeline_exist"] ?? false;

            // logger(print_r($response->body(), true));

            return $timeline_exist;

        } catch (\Throwable $th) {
            return false;
        }

    }

    public function updateSubmissionRow(){
        try {
            //code...
            if(!($this->row_number ?? false)){
                return false;
            }

            // Update values in the row
            $updatedValues = [[
                $this->submissionDate,
                $this->leadEmail,
                $this->leadName,
                $this->leadPOE,
                $this->leadTimeline,
                $this->leadAdminRems,
                $this->emailUniquesId,
                $this->last_email_comm_date, //last comm date
                $this->status,
                $this->possible_dup,
                $this->matter_name,
                $this->communication_count,
                $this->agency_names,
            ]];

            $range = $this->sheetRange . "!A".$this->row_number.":M".$this->row_number;

            $valueRangeWrapper = new ValueRange();
            $valueRangeWrapper->setValues($updatedValues);

            // $valueRangeWrapper->setRange($range);
            
            
            $this->sheetService->spreadsheets_values->update(
                $this->intakeSceduledSpreadsheetId, $range, $valueRangeWrapper, $this->options
            );
        } catch (Exception $e) {
            logger($e->getMessage());
        }

        


    }
}


?>