<?php

return [    
    "ACTIONSTEP_CLIENT_ID" => env('ACTIONSTEP_CLIENT_ID'),
    "ACTIONSTEP_CLIENT_SECRET" => env('ACTIONSTEP_CLIENT_SECRET'),
    "ACTIONSTEP_TOKEN_URL" => env('ACTIONSTEP_TOKEN_URL'),
    "ACTIONSTEP_AUTHORIZATION_URL" => env('ACTIONSTEP_AUTHORIZATION_URL'),

    "NEXT_STEP_DATA" => [
        "ACTION_TYPE" => 10,
        "DATA_COLLECTION" => 81,
        "DATA_COLLECTION_FIELDS" => [
            // GOTTEN THROUGH: {{api_endpoint}}/api/rest/datacollectionfields?include=dataCollection,tag&dataCollection={{DATA_COLLECTION}}
            // MARCH 7TH 2024

            "NEXT_STEP" => [
                "ID" => "81--NextStep",
                "ACTIVE" => TRUE,
                "LABEL" => "Next Step Details",
                "DATA_TYPE" => "TEXTAREA",
                "DESCRIPTION" => "",
                "ORDER" => 1,
                "CUSTOM_HTML_ABOVE" => "",
                "CUSTOM_HTML_BELOW" => "",
            ],
            "NEXT_STEP_ASSIGNED_TO" => [
                "ID" => "81--NextStepAssignedTo",
                "ACTIVE" => TRUE,
                "LABEL" => "Next Step Assigned To",
                "DATA_TYPE" => "DROPDOWN",
                "DESCRIPTION" => "",
                "ORDER" => 2,
                "CUSTOM_HTML_ABOVE" => "",
                "CUSTOM_HTML_BELOW" => "",
                "OPTIONS" => [
                    "JenEece",
                    "Miriam",
                    "Miriam and JenEece",
                    "Mycah",
                    "Other",
                    "Rachel",
                    "Taylr",                    
                ]
            ],
            "NEXT_STEP_DATE" => [
                "ID" => "81--NextStepDate",
                "ACTIVE" => TRUE,
                "LABEL" => "Next Step Date",
                "DATA_TYPE" => "DATE",
                "DESCRIPTION" => "",
                "ORDER" => 3,
                "CUSTOM_HTML_ABOVE" => "",
                "CUSTOM_HTML_BELOW" => "",
            ],
            "BATNA" => [
                "ID" => "81--BATNA",
                "ACTIVE" => FALSE,
                "LABEL" => "BATNA",
                "DATA_TYPE" => "STRING",
                "DESCRIPTION" => "What is client's BATNA - Best Alternative to Negotiated Agreement?\r\nMediation, Arbitration, File, or Close?",
                "ORDER" => 4,
                "CUSTOM_HTML_ABOVE" => "",
                "CUSTOM_HTML_BELOW" => "",
            ],
            "CASETEMP" => [
                "ID" => "81--CASETEMP",
                "ACTIVE" => FALSE,
                "LABEL" => "Temp Check",
                "DATA_TYPE" => "DROPDOWN",
                "DESCRIPTION" => "",
                "ORDER" => 5,
                "CUSTOM_HTML_ABOVE" => "",
                "CUSTOM_HTML_BELOW" => "<p><strong><span style=\"color: #ff0000;\">ON FIRE</span></strong> - Likely to settle w/in next 30 days<br><br><span style=\"color: #ff9900;\"><strong>HOT</strong></span> - Active Negotiation with offers back and forth heading towards settlement range<br><br><span style=\"color: #ffcc00;\"><strong>WARM</strong></span> - Primary Defendant Contact established and agreement to negotiate, pending offer or low offer proposed, severance negotiation case<br><br><strong><span style=\"background-color: #33cccc;\">Tepid</span></strong> - New Case, No Demand or severance offer, no indication yet of a willingness to negotiate,  No PDC with authority. <em>This is a neutral status for cases that haven't yet been worked</em><br><br><b><span style=\"color: #3366ff;\">COLD</span></b>- No PDC, lost contact, negotiations have significantly stalled, bad/infrequently communication, refusal to negotiate, lb sand<br><br><strong><span style=\"color: #99ccff;\">FRIGID</span></strong> - Holding pattern, cannot identify a PDC, Admin Remedies only, old case with little or significantly stalled progress</p>",
                "OPTIONS" => [
                    "ON FIRE",
                    "Hot",
                    "Tepid",
                    "Warm",
                    "Cold",
                    "Frigid"
                ]
            ],
            
        ],
    ]
];