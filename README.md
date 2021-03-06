# pdc Leads API

## Purpose of this document

This document defines the request protocol and data structure for `SalesLeads` that **XCar** sends to **pdc Marketing & Information Technology AG** for processing.

> THE SCHEMA PRESENTED IN THIS DOCUMENT IS NOT FINAL AND WILL BE REDUCED OR EXPANDED ACCORDING TO THE REQUIREMENTS OF XCAR SUISSE SA.

## Document history

|Version|Datum|Autor|Beschreib|
|-|-|-|-|
| 0.1 |17.05.2019| aklee | Setup document |
| 0.2 |24.05.2019| aklee | Added OPTIONS samples |
| 0.3 |15.06.2019| aklee | Added XML samples |

## Environment

The `pdc Leads API` consists of a single `leads` ressource, which is available on two `environments`.

|Environment|Endpoint|
|-|-|
|`TEST` | `https://connectors-d.pdc-online.com/xdata/api/{version}` |
|`PROD` | `https://connectors.pdc-online.com/xdata/api/{version}` |

To access the resources described in this document, you need to authenticate your request with *username* and *password* using the `Basic` authentication scheme. Additionally you need to provide an `access_token` as query parameter in each of your requests.

> **User credentials** and **access_token** can be requested through your marketing contact. Upon this request you might receive additional headers that you need to add to your requests to `pdc Leads API`.

### Wildcards

In an attempt to keep the documentation clean and as simple as possible, there are several `{wildcards}` used throughout this document.

|Wildcard|Description|Value|
|-|-|-|
|`{version}` |The version of the API. | `v1` |
|`{base_url}` |The base url for your requests| `https://connectors-d.pdc-online.com/xdata/api/{version}` |
|`{resource}` |The specific resource you send your request to. | `leads` or `leads/{id}` |
|`{id}` |The lead id generated by PDC. | *provided with user credentials* |
|`{token}` |The access_token used for your requests. | *provided with user credentials* |
|`{appl}` |Integer value used by *pdc* to route your request. | *provided with user credentials* |

### Headers

`pdc Leads API` uses headers to route incoming data from different sources.

#### Required headers

|Header|Value|
|-|-|
|`Content-Type` | `application/json` or `application/xml` |
|`Authorization` | *provided upon request* |
|`X-Appl` | `{appl}` |

#### Additional headers

Based upon your specific requirements there might be additional information required to successfully route your data into our Lead Management System. ***The following list is not conclusive.***

|Header|Value|
|-|-|
|`X-ORIGIN` | *provided if needed* |
|`X-MAND` | *provided if needed* |
|`X-ZONE` | *provided if needed* |

## Using the `/leads` ressource

`pdc Leads API` exposes a single `leads` resource. It is used to send data and also check successful transmission to **pdc**.

### URL Template

`{base_url}/{resource}?access_token={token}`

### Available Resource Methods

| Method    | Resource     | Version | Description                    |
|-----------|--------------|---------|--------------------------------|
| `POST`    | `leads`      | v1      | Send leads                     |
| `GET`     | `leads`      | v1      | Get all transmitted leads      |
| `GET`     | `leads/{id}` | v1      | Get a single transmitted lead  |
| `OPTIONS` | `leads`      | v1      | Displays validation info       |

### General responses

Applies to all responses from the API.

|Code|Description|
|-|-|
|`401 (Unauthorized)` | Request rejected (Invalid credentials). <br><br>*Sample response:*<br>`No content`  |
|`404 (Not found)`| The requested resource was not found.<br><br>*Sample response:*<br>`No content`|
|`409 (Conflict)` | Request rejected (duplicate entry). <br><br>*Sample response:*<br>`No content`  |
|`500 (Internal Server Error)` | Something went wrong on our side.<br><br>*Sample response:*<br>`Error message created by the server` |
|||

### POST /leads

#### Description

Transmits leads to pdc.

#### Parameters

|Name <sup>(context)</sup>|Type|Description|
|-|-|-|
|`access_token` &nbsp;<sup>(query)</sup> | `string` | The token that grants access to the `leads` resource. |
|`payload` &nbsp;<sup>(body)</sup> | *see Appendix C: Schema* | The payload you send in your request body. |

#### Sample HTTP Request

```http
POST /xdata/api/v1/leads?access_token=abcdef12345 HTTP/1.1
X-Appl: {appl}
Content-Type: application/json
Authorization: Basic dGVzdDp0ZXN0
Host: connectors-d.pdc-online.com
Content-Length: 15

{"test":"test"}
```


#### Responses

|Code|Description|
|-|-|
|`201 (Created)` |Successful submission <br><br>*Sample response:* <code><br>`"e84de886-776c-4dfa-a31a-dd550884fd94"`</code>|
|`400 (Bad request)` | Request rejected. The payload did not match the schema for the submitted `access_token`. <br><br>*Sample response (Validation errors)*: <code><br>{<br>&nbsp;&nbsp;"Success": false,<br>&nbsp;&nbsp;"StatusCode": 400,<br>&nbsp;&nbsp;"Message": "Validation failed!",<br>&nbsp;&nbsp;"Errors": [<br>&nbsp;&nbsp;&nbsp;&nbsp;{<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"Message": "Required properties are missing from object: Origin.",<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"LineNumber": 2,<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"LinePosition": 5,<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"Path": "[0]",<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"Value": [<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"Origin"<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;],<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"SchemaId": "#/definitions/ProcessSalesLeadElement",<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"SchemaBaseUri": null,<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"ErrorType": "required",<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"ChildErrors": []<br>&nbsp;&nbsp;&nbsp;&nbsp;}<br>&nbsp;&nbsp;]<br>}</code>|

### OPTIONS /leads

#### Description

Displays the schema your leads are validated against.

#### Parameters

|Name <sup>(context)</sup>|Type|Description|
|-|-|-|
|`access_token` &nbsp;<sup>(query)</sup> | `string` | The token that grants access to the `leads` resource. |

#### Responses

|Code|Description|
|-|-|
|`200 (OK)` |Displays the schema that is used to validate your payload.<br><br>*Sample response:* <code><br>{<br>&nbsp;&nbsp;"definitions": {<br>&nbsp;&nbsp;&nbsp;&nbsp;[[REDACTED]]<br>&nbsp;&nbsp;}<br>}</code>|
|`400 (Bad request)` | No Schema configured for [[YOUR_ACCESS_TOKEN]]. <br><br>*Sample response*: <code><br>{<br>&nbsp;&nbsp;"Message": "No schema configured",<br>}</code>|
|`400 (Bad request)` | The Schema could not be parsed. Happens if the Content-Type of the request does not match the schema language. <br><br>*Sample response*: <code><br>{<br>&nbsp;&nbsp;"Message": "Error parsing schema for token '[[YOUR_ACCESS_TOKEN]]'.",<br>&nbsp;&nbsp;"MediaType": "application/xml",<br>&nbsp;&nbsp;"Error": "Data at the root level is invalid. Line 1, position 1.",<br>&nbsp;&nbsp;"Schema": "[[REDACTED]]"<br>}</code>|
|||

### GET /leads

#### Description

Display all leads - using pagination - that have been transmitted.

#### Parameters

|Name <sup>(context)</sup>|Type|Description|
|-|-|-|
|`access_token` &nbsp;<sup>(query)</sup> | `string` | The Guid returned on successful `POST` to the `leads` resource. |
|`pageSize` &nbsp;<sup>(query)</sup> | `integer` | The number of leads per page. (max 25) |
|`pageNumber` &nbsp;<sup>(query)</sup> | `integer` | The pageNumber. |

#### Responses

|Code|Description|
|-|-|
|`200 (OK)` |OK<br><br>*Sample response:*<br>`Your transmitted payloads`|
|||

### GET /leads/{id}

#### Parameters

|Name <sup>(context)</sup>|Type|Description|
|-|-|-|
|`id` &nbsp;<sup>(path)</sup> | `Guid` | The Guid returned on successful `POST` to the `leads` resource. |
|`access_token` &nbsp;<sup>(query)</sup> | `string` |The token that grants access to the `leads` resource. |
#### Responses

|Code|Description|
|-|-|
|`200 (OK)` |OK<br><br>*Sample response:*<br>`Your transmitted payload`|
|||

## Appendix A: Samples

> We **very strongly** advice against sending user data from the client. (For example using Ajax POST on form submit). Always access the leads API from your server.
>
> Also, CORS is disabled on PDC servers.

### JSON

#### Post From Server (PHP)

```html
 <form action="post-method.php" method="post">
    <input type="text" name="firstname" placeholder="First Name" />
    <input type="text" name="lastname" placeholder="Last Name" />
    <input type="text" name="telephone" placeholder="Telephone" />
    <input type="text" name="email" placeholder="Email" />
    <input type="submit" name="submit" />
</form>
```

```php
<?php

$firstname = $_POST['firstname'];
$lastname = $_POST['lastname'];
$telephone = $_POST['telephone'];
$email = $_POST['email'];

$lead = '[
  {
    [[REDACTED FOR BREVITY]]
      "Customer": {
        "FirstName1": "%firstname%",
        "LastName1": "%lastname%",
        "TelP1": "%telephone%",
        "EmailP1": "%email&"
      }
    [[REDACTED FOR BREVITY]]
  }
]';

$lead = str_replace("%firstname%", $firstname, $lead);
$lead = str_replace("%lastname%", $lastname, $lead);
$lead = str_replace("%telephone%", $telephone, $lead);
$lead = str_replace("%email%", $email, $lead);

$request = new HttpRequest();
$request->setUrl('https://connectors.pdc-online.com/xdata/api/v1/leads');
$request->setMethod(HTTP_METH_POST);

$request->setQueryData(array(
  'access_token' => '[[REDACTED]]'
));

$request->setHeaders(array(
  'cache-control' => 'no-cache',
  'X-APPL' => '80',
  'Authorization' => 'Basic [[REDACTED]]',
  'Content-Type' => 'application/json'
));

$request->setBody($lead);

try {
  $response = $request->send();

  echo $response->getBody();
} catch (HttpException $ex) {
  echo $ex;
}
```

#### POST from Client (Discouraged)

Out of **security considerations** it is highly discouraged to send user data / leads from the client (ie Webbrowser).

## Appendix B: Message Content Objects

### Privacy-Object

The Privacy-Node is an array which contains 0 to n Purposes.

#### Definition

```json
{
    "Privacy": {
        "type": "array",
        "items": {
            "$ref": "#/definitions/Purpose"
        }
    }
}
```

### Purpose-Object

A Purpose-Object defines the reason of consent and 0 to n **explicit consents** for which the customer-data can be used (`OptIn`) / can not be used (`OptOut`).  

#### Purpose-Definition

```json
{
    "Purpose": {
        "type": "object",
        "additionalProperties": false,
        "properties": {
            "Reason": {
                "type": "string"
            },
            "Disclaimer": {
                "anyOf": [
                    {
                        "type": "null"
                    },
                    {
                        "type": "string"
                    }
                ]
            },
            "Consents": {
                "type": "array",
                "items": {
                    "$ref": "#/definitions/Consent"
                }
            }
        },
        "required": [
            "Consents",
            "Disclaimer",
            "Reason"
        ],
        "title": "Purpose"
    }
}
```

#### Sample Purpose

A complete Purpose for für den Type `Marketing` looks as follows:

```json
{
    "Reason": "Marketing",
    "Disclaimer": "Text / Id / Url",
    "Consents": [
        {
            "Type": "Email",
            "Value": "OptIn"
        },
        {
            "Type": "SMS",
            "Value": "OptOut"
        },
        {
            "Type": "Post",
            "Value": "OptIn"
        },
        {
            "Type": "Telephone",
            "Value": "None"
        }
    ]
}
```

As stated above, Consents are to be given explicitly by the customer. `null` or `""` does not equal to an `OptOut`!

### Vehicle Object

The Vehicle Object contains the most important data about the vehicle.

> **NOTE**: On the test environment the `VIN` is optional. In production this property is required.

#### Vehicle Definition

```json
{
    "Vehicle": {
        "type": "object",
        "additionalProperties": false,
        "properties": {
            "VehicleID": {
                "type": "integer"
            },
            "MakeString": {
                "type": "string"
            },
            "ModelString": {
                "type": "string"
            },
            "ModelDescription": {
                "type": "string"
            },
            "VIN": {
                "type": "string"
            },
            "Url": {
                "type": "string",
                "format": "uri",
                "qt-uri-protocols": [
                    "https"
                ]
            }
        },
        "required": [
            "MakeString",
            "ModelDescription",
            "ModelString",
            "Url",
            "VehicleID"
        ],
        "title": "Vehicle"
    }
}
```

#### Sample Vehicle

```json
{
    "Vehicle": {
        "VehicleID": 6339162,
        "MakeString": "XCar",
        "ModelString": "V8 Superspeed",
        "ModelDescription": "XCar V8 Superspeed",
        "Url": "https://int.xcar.ch/6339162",
        "VIN": "12345678901234567"
    }
}
```


## Appendix C: Schema

```json
{
    "$schema": "http://json-schema.org/draft-06/schema#",
    "type": "array",
    "items": {
        "$ref": "#/definitions/ProcessSalesLeadElement"
    },
    "definitions": {
        "ProcessSalesLeadElement": {
            "type": "object",
            "additionalProperties": false,
            "properties": {
                "Origin": {
                    "type": "string"
                },
                "Sender": {
                    "$ref": "#/definitions/Sender"
                },
                "CreationDateTime": {
                    "type": "string"
                },
                "Destination": {
                    "$ref": "#/definitions/Destination"
                },
                "SalesLead": {
                    "$ref": "#/definitions/SalesLead"
                }
            },
            "required": [
                "Origin",
                "CreationDateTime",
                "SalesLead",
                "Sender"
            ],
            "title": "ProcessSalesLeadElement"
        },
        "Destination": {
            "type": "object",
            "additionalProperties": false,
            "properties": {
                "DealerNumberID": {
                    "type": "integer"
                },
                "DealerTargetCountry": {
                    "type": "string"
                }
            },
            "required": [
                "DealerNumberID",
                "DealerTargetCountry"
            ],
            "title": "Destination"
        },
        "SalesLead": {
            "type": "object",
            "additionalProperties": false,
            "properties": {
                "ID": {
                    "type": "integer"
                },
                "CustomerComments": {
                    "type": "string"
                },
                "LeadCreationsDateTime": {
                    "type": "string"
                },
                "LeadComments": {
                    "type": "string"
                },
                "LeadTypeCode": {
                    "type": "string"
                },
                "PreferredLanguageCode": {
                    "type": "string"
                },
                "LeadRequestTypeString": {
                    "type": "string"
                },
                "Customer": {
                    "$ref": "#/definitions/Customer"
                },
                "Privacy": {
                    "type": "array",
                    "items": {
                        "$ref": "#/definitions/Purpose"
                    }
                },
                "Vehicle": {
                    "$ref": "#/definitions/Vehicle"
                }
            },
            "required": [
                "Customer",
                "CustomerComments",
                "ID",
                "LeadComments",
                "LeadCreationsDateTime",
                "LeadRequestTypeString",
                "LeadTypeCode",
                "PreferredLanguageCode",
                "Privacy",
                "Vehicle"
            ],
            "title": "SalesLead"
        },
        "Customer": {
            "type": "object",
            "additionalProperties": false,
            "properties": {
                "Title": {
                    "type": [ "string", "null" ]
                },
                "AcademicTitle": {
                    "type": [ "string", "null" ]
                },
                "Firstname1": {
                    "type": [ "string", "null" ]
                },
                "MiddleName1": {
                    "type": [ "string", "null" ]
                },
                "LastName1": {
                    "type": "string"
                },
                "Nickname1": {
                    "type": [ "string", "null" ]
                },
                "GenderCode": {
                    "type": [ "string", "null" ]
                },
                "Street": {
                    "type": [ "string", "null" ]
                },
                "CityName": {
                    "type": [ "string", "null" ]
                },
                "Additional": {
                    "type": [ "string", "null" ]
                },
                "POBox": {
                    "type": [ "string", "null" ]
                },
                "PostCode": {
                    "type": [ "string", "null" ]
                },
                "Country": {
                    "type": [ "string", "null" ]
                },
                "DateOfBirth": {
                    "type": [ "string", "null" ]
                },
                "TelP1": {
                    "type": "string"
                },
                "TelP2": {
                    "type": [ "string", "null" ]
                },
                "TelG1": {
                    "type": [ "string", "null" ]
                },
                "Fax1": {
                    "type": [ "string", "null" ]
                },
                "Fax2": {
                    "type": [ "string", "null" ]
                },
                "EmailP1": {
                    "type": "string"
                },
                "EmailP2": {
                    "type": [ "string", "null" ]
                },
                "EmailG1": {
                    "type": [ "string", "null" ]
                },
                "EmailG2": {
                    "type": [ "string", "null" ]
                }
            },
            "required": [
                "EmailP1",
                "LastName1",
                "TelP1"
            ],
            "title": "Customer"
        },
        "Purpose": {
            "type": "object",
            "additionalProperties": false,
            "properties": {
                "Reason": {
                    "type": "string"
                },
                "Disclaimer": {
                  	"type": [ "string", "null" ]
                },
                "Consents": {
                    "type": "array",
                    "items": {
                        "$ref": "#/definitions/Consent"
                    }
                }
            },
            "required": [
                "Consents",
                "Disclaimer",
                "Reason"
            ],
            "title": "Purpose"
        },
        "Consent": {
            "type": "object",
            "additionalProperties": false,
            "properties": {
                "Type": {
                    "$ref": "#/definitions/TypeOfConsent"
                },
                "Value": {
                    "$ref": "#/definitions/ValueOfConsent"
                }
            },
            "required": [
                "Type",
                "Value"
            ],
            "title": "Consent"
        },
        "Vehicle": {
            "type": "object",
            "additionalProperties": false,
            "properties": {
                "VehicleID": {
                    "type": "integer"
                },
                "MakeString": {
                    "type": "string"
                },
                "ModelString": {
                    "type": "string"
                },
                "ModelDescription": {
                    "type": "string"
                },
                "VIN": {
                    "type": "string"
                },
                "Url": {
                    "type": "string",
                    "format": "uri",
                    "qt-uri-protocols": [
                        "https"
                    ]
                }
            },
            "required": [
                "MakeString",
                "ModelDescription",
                "ModelString",
                "Url",
                "VehicleID"
            ],
            "title": "Vehicle"
        },
        "Sender": {
            "type": "object",
            "additionalProperties": false,
            "properties": {
                "TaskID": {
                    "type": "string"
                },
                "ReferenceID": {
                    "type": "string"
                },
                "CreatorNameCode": {
                    "type": "string"
                },
                "SenderNameCode": {
                    "type": "string"
                },
                "Url": {
                    "type": "string",
                    "format": "uri",
                    "qt-uri-protocols": [
                        "https"
                    ]
                }
            },
            "required": [
                "CreatorNameCode",
                "ReferenceID",
                "SenderNameCode",
                "TaskID",
                "Url"
            ],
            "title": "Sender"
        },
        "TypeOfConsent": {
            "type": "string",
            "enum": [
                "Email",
                "SMS",
                "Post",
                "Telephone"
            ],
            "title": "Type"
        },
        "ValueOfConsent": {
            "type": [ "string", "null" ],
            "enum": [
                "OptIn",
                "OptOut",
                "None",
                "",
                null
            ],
            "title": "Value"
        }
    }
}
```

## Apendix C: Sample Data

### ProcessSalesLead Sample data

```json
[
  {
    "Origin":"xcar-v8-superspeed-COS-IPROS-0419",
    "Sender": {
      "TaskID": "ProcessSalesLead",
      "ReferenceID": "MSB4Vj8zfsbW/t8THdyFfbGQ==",
      "CreatorNameCode": "XCar",
      "SenderNameCode": "XCar",
      "Url": "https://int.xcar.ch/de/d/xcar-v8-superspeed?vehid=6339162"
    },
    "CreationDateTime": "2019-03-20T11:19:36.686Z",
    "Destination": {
      "DealerNumberID": 35228,
      "DealerTargetCountry": "CH"
    },
    "SalesLead": {
      "ID": 31740371,
      "CustomerComments": "test",
      "LeadCreationsDateTime": "2019-03-20T11:19:36.686Z",
      "LeadComments": "#ms24.publicweb.DealerPage.Text.TextResource#",
      "LeadTypeCode": "I",
      "PreferredLanguageCode": "de-CH",
      "LeadRequestTypeString": "Contact Request",
      "Customer": {
        "LastName1": "test",
        "TelP1": "+41799999999",
        "EmailP1": "test@test.com",
        [[ADDITIONAL FIELDS REMOVED FOR BREVITY]]
      },
      "Privacy": [
        {
          "Reason": "Marketing",
          "Disclaimer": null,
          "Consents": [
            {
              "Type": "Email",
              "Value": "None"
            },
            {
              "Type": "SMS",
              "Value": "OptOut"
            },
            {
              "Type": "Email",
              "Value": "OptIn"
            }
          ]
        },
        {
          "Reason": "Profiling",
          "Disclaimer": null,
          "Consents": []
        }
      ],
      "Vehicle": {
        "VehicleID": 6339162,
        "MakeString": "XCar",
        "ModelString": "V8 Superspeed",
        "ModelDescription": "XCar V8 Superspeed",
        "Url": "https://int.xcar.ch/6339162",
        "VIN": "12345678901234567"
      }
    }
  }
]
```
