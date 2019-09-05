// File: post_form.php
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {

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

    // request token
    $path = 'credentials.json';
    $file = fopen($path, "r");
    $cred = json_decode(fread($file, filesize($path)));
    fclose($file);

    $auth = new HttpRequest();
    $auth->setUrl('https://auth.pdc-online.com/token');
    $auth->setMethod(HTTP_METH_POST);
    $auth->setQueryData($cred));
    $auth->setHeaders(array(
        'cache-control' => 'no-cache',
        'Content-Type' => 'application/x-www-url-formencoded'
    );
    
    // auth
    try {
        $auth_response = $auth->send();
        $token = json_decode($response->getBody())->{'access_token'};
    
        // send the lead
        $request = new HttpRequest();
        $request->setUrl('https://connectors.pdc-online.com/xdata/api/v1/leads');
        $request->setMethod(HTTP_METH_POST);

        $request->setHeaders(array(
        'cache-control' => 'no-cache',
        'Authorization' => 'Bearer '.$token,
        'Content-Type' => 'application/json'
        ));

        $request->setBody($lead);
        
        // lead
        try { 
            $response = $request->send();
            echo $response->getBody();
        } catch (HttpException $ex) {
            echo $ex;
        }
    } catch (HttpException $ex) {
        echo $ex;
    } // try auth
}
?>
 <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);" method="post">
    <input type="text" name="firstname" placeholder="First Name" />
    <input type="text" name="lastname" placeholder="Last Name" />
    <input type="text" name="telephone" placeholder="Telephone" />
    <input type="text" name="email" placeholder="Email" />
    <input type="submit" name="submit" />
</form>
