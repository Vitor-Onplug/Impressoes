<?php
// This file was auto-generated from sdk-root/src/data/marketplace-reporting/2018-05-10/api-2.json
return [ 'version' => '2.0', 'metadata' => [ 'apiVersion' => '2018-05-10', 'auth' => [ 'aws.auth#sigv4', ], 'endpointPrefix' => 'reporting-marketplace', 'protocol' => 'rest-json', 'protocols' => [ 'rest-json', ], 'serviceFullName' => 'AWS Marketplace Reporting Service', 'serviceId' => 'Marketplace Reporting', 'signatureVersion' => 'v4', 'signingName' => 'aws-marketplace', 'uid' => 'marketplace-reporting-2018-05-10', ], 'operations' => [ 'GetBuyerDashboard' => [ 'name' => 'GetBuyerDashboard', 'http' => [ 'method' => 'POST', 'requestUri' => '/getBuyerDashboard', 'responseCode' => 200, ], 'input' => [ 'shape' => 'GetBuyerDashboardInput', ], 'output' => [ 'shape' => 'GetBuyerDashboardOutput', ], 'errors' => [ [ 'shape' => 'InternalServerException', ], [ 'shape' => 'AccessDeniedException', ], [ 'shape' => 'BadRequestException', ], [ 'shape' => 'UnauthorizedException', ], ], ], ], 'shapes' => [ 'AccessDeniedException' => [ 'type' => 'structure', 'members' => [ 'message' => [ 'shape' => 'String', ], ], 'error' => [ 'httpStatusCode' => 403, 'senderFault' => true, ], 'exception' => true, ], 'BadRequestException' => [ 'type' => 'structure', 'members' => [ 'message' => [ 'shape' => 'String', ], ], 'error' => [ 'httpStatusCode' => 400, 'senderFault' => true, ], 'exception' => true, ], 'DashboardIdentifier' => [ 'type' => 'string', 'max' => 1023, 'min' => 1, 'pattern' => 'arn:aws:aws-marketplace::[0-9]{12}:AWSMarketplace/ReportingData/(Agreement_V1/Dashboard/AgreementSummary_V1|BillingEvent_V1/Dashboard/CostAnalysis_V1)', ], 'EmbeddingDomain' => [ 'type' => 'string', 'max' => 2000, 'min' => 1, 'pattern' => '(https://[a-zA-Z\\.\\*0-9\\-_]+[\\.]{1}[a-zA-Z]{1,}[a-zA-Z0-9&?/-_=]*[a-zA-Z\\*0-9/]+|http[s]*://localhost(:[0-9]{1,5})?)', ], 'EmbeddingDomains' => [ 'type' => 'list', 'member' => [ 'shape' => 'EmbeddingDomain', ], 'max' => 2, 'min' => 1, ], 'GetBuyerDashboardInput' => [ 'type' => 'structure', 'required' => [ 'dashboardIdentifier', 'embeddingDomains', ], 'members' => [ 'dashboardIdentifier' => [ 'shape' => 'DashboardIdentifier', ], 'embeddingDomains' => [ 'shape' => 'EmbeddingDomains', ], ], ], 'GetBuyerDashboardOutput' => [ 'type' => 'structure', 'required' => [ 'embedUrl', 'dashboardIdentifier', 'embeddingDomains', ], 'members' => [ 'embedUrl' => [ 'shape' => 'String', ], 'dashboardIdentifier' => [ 'shape' => 'DashboardIdentifier', ], 'embeddingDomains' => [ 'shape' => 'EmbeddingDomains', ], ], ], 'InternalServerException' => [ 'type' => 'structure', 'members' => [ 'message' => [ 'shape' => 'String', ], ], 'error' => [ 'httpStatusCode' => 500, ], 'exception' => true, 'fault' => true, ], 'String' => [ 'type' => 'string', ], 'UnauthorizedException' => [ 'type' => 'structure', 'members' => [ 'message' => [ 'shape' => 'String', ], ], 'error' => [ 'httpStatusCode' => 401, 'senderFault' => true, ], 'exception' => true, ], ],];
