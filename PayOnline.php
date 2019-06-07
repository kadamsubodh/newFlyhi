<!DOCTYPE html>
<html lang="en">
    <head>
        <?php include('includes.php'); ?>
        <link href="css/bootstrap.min.css" rel="stylesheet">
        <script src="js/jquery.confirm.js"></script>
        <script src="js/bootstrap.min.js"></script>
        <script src="js/run_prettify.js"></script>
        <script type="text/javascript" >
            $(document).ready(function() {
                $(".alert-box-close").click(function(e) {
                    $(".alert-box").hide();
                });

            });
            function getFromData() {

                var data = $("#Pay").serializeArray();

                console.log(data);

                var currency = "";

                if (data[17]['value'] == 'INR') {
                    currency = '₹';
                } else if (data[17]['value'] == 'USD') {
                    currency = '$';
                } else {
                    currency = 'S$';
                }

                $('#client_name strong').text(data[6]['value'] + " " + data[7]['value']);

                $('#cli_address1').text(data[9]['value']);

                $('#cli_address2').text(data[10]['value']);

                $('#cli_town').text(data[11]['value']);

                $('#cli_region').text(data[12]['value']);

                $('#cli_zipcode').text(data[13]['value']);

                $('#Invoicedesc').text(data[16]['value']);

                $('#final_amount').text(currency + data[18]['value']);

                return $('#invoice').html();
            }

            $(document).ready(function() {

                $("#Pay").validationEngine('attach');

                $('#pqr').click(function() {

                    if ($("#Pay").validationEngine('validate'))
                    {
                        $.confirm({
                            text: getFromData(),
                            confirm: function(button) {
                                //console.log("form submit");
                                $('#Pay').submit();
                            },
                            cancel: function(button) {
                                //console.log("form cancel");
                                return false;
                            }
                        });

                    }

                });

                
            });

        </script>
        <style type="text/css">

            #invoice{
                display: none;
            }
        </style>
    </head>
    <body>
        <div id="Container">
            <!--wrapper start-->
            <div class="wrapper">

                <!--header start-->
                <?php include('header.php'); ?>
                <!--header end-->

                <!--content start-->
                <div id="Content">
                    <div class="content-wrapper">

                        <?php
                        if (isset($_GET['tranId']) && $_GET['tranId'] != "") {

                            $getData = "SELECT * from transactions WHERE tranId = '" . $_GET['tranId'] . "'";
                        } else {
                            $getData = "SELECT * from transactions WHERE paymentId = '" . $_GET['paymentId'] . "'";
                        }

                        $query = sql_Query($getData, $db);

                        $Data = sql_Fetch($query);

                        if (sql_NumRows($query) > 0) {

                            $message = $Data['status'];
                            $message = $Data['result'];

                            if (isset($Data['result']) && $Data['result'] == "CAPTURED" || $Data['result'] == "SUCCESS") {
                                $state = "Successfull";
                            } else {
                                $state = "Unsuccessfull";
                            }

                            $msg = "<div class='alert-box " . $state . "'><span><p style='color:#084B9B'>
                                            Transaction " . $state . "
                                    </p></span>
                                    <p style='color:#084B9B'>
                                            Transaction Message : " . $message . "
                                    </p>
                                    <p style='color:#084B9B'>
                                            Mail have been send to " . $Data['email'] . "
                                    </p><span class='alert-box-close'>x</span></div>";
                        }

                        if (isset($_GET['tranId']) && $_GET['tranId'] != "" || isset($_GET['paymentId']) && $_GET['paymentId'] != "") {
                            echo $msg;
                        }
                        ?>

                        <!--column2 start-->
                        <div class="column2">

                            <div class="enquiryform">
                                <h4>Pay Online</h4>
                                <div style="padding: 5px 15px;;"> 
                                    <span> Select Payment Gateway:</span><br>
                                     <input type="radio" name="getwayType" value="HDFC" checked>HDFC</input>
                                        <input type="radio" name="getwayType" value="AEXP">American Express</input>

                                    <form id="americanExpress" action="amexhosted.php" method="POST" onsubmit="return validateAMEXForm()">
                                        <div class="info">
                                            <div class="row">
                                                <div class="col-md-6 radio1">
                                                    <input type="radio" id="radio1" name="paymentType" value="lightbox" checked>Pay with Lightbox</input>
                                                </div>
                                                <div class="col-md-6 radio2">
                                                    <input type="radio" id="radio2" name="paymentType" value="paymentpage">Pay with Payment Page</input>
                                                </div>
                                            </div>
                                        </div>
                                             <br>
                                        <div class="info">
                                           <div id="errors" style="display: none;"></div>
                                        </div>

                                        <div class="info">
                                            <label for="FirstName" class="inputlabel">Payer First Name
                                                <div class="dot3">:</div>
                                                <div class="mandatory">*</div>
                                            </label>
                                            <input type="text" name="FirstName" id="FirstName" class="validate[required]" maxlength="100" >
                                        </div>

                                        <div class="info">
                                            <label for="LastName" class="inputlabel">Payer Last Name
                                                <div class="dot3">:</div>
                                                <div class="mandatory">*</div>
                                            </label>
                                            <input type="text" name="LastName" class="validate[required]" id="LastName" maxlength="100" >
                                        </div>
                                           
                                            <div class="info">
                                                <label for="email" class="inputlabel">Email
                                                    <div class="dot3">:</div>
                                                    <div class="mandatory">*</div>
                                                </label>
                                                <input type="email" name="email1" class="validate[required,custom[email]]" id="email1" maxlength="100" >
                                            </div>
                                            <div class="info">
                                            <label for="phone" class="inputlabel">Mobile No.
                                                <div class="dot3">:</div>
                                                <div class="mandatory">*</div>
                                            </label>
                                            <input type="text" name="phone1" class="validate[required]" id="phone1" maxlength="12" >
                                        </div>
                                            <div class="info">
                                                <label for="Price" class="inputlabel">Currency
                                                    <div class="dot3">:</div>
                                                    <div class="mandatory">*</div>
                                                </label>
                                                <select name="currency1" id="currency1" class="validate[required]">
                                                    <option value=""><-- SELECT CURRENCY --></option>
                                                    <option value="INR">INR</option>
                                                    <option value="USD">USD</option>
                                                    <!--<option value="3">SGD</option>-->
                                                </select>

                                            </div>

                                            <div class="info">
                                                <label for="amount1" class="inputlabel">Total Price
                                                    <div class="dot3">:</div>
                                                    <div class="mandatory">*</div>
                                                </label>
                                                <input type="text" name="amount1" class="validate[required]" id="amount1">
                                            </div>
                                            <div class="info">
                                                <label for="description1" class="inputlabel">Payment Description
                                                    <div class="dot3">:</div>
                                                    <div class="mandatory">*</div>
                                                    <!-- (Please include PNR No / Ticket No / Invoice No / SOA No and Period in Payment Description) -->
                                                    (Please include PNR No / Ticket No / Invoice No / SOA No and Period / Or any other payment details in above field)
                                                </label>
                                                <textarea name="description1" class="validate[required]" id="description1" ></textarea>
                                            </div>
                                            <span>
                                                <input type="submit" class="careersubmit" name="aexp" id="aexp" value="Proceed" />
                                            </span>

                                        </div>

                                    </form>

                                    <form id="Pay" action="sendRequest.php" method="post">
                                        <input type="hidden" name="channel" value="10">
                                        <input type="hidden" name="account_id" value="20581">
                                        <input type="hidden" name="reference_no" value="<?php echo time();?>">
                                        <input type="hidden" name="secretkey" value="4fda4c4225c6f9e75ffa9438e7308b32">
                                        <input type="hidden" name="return_url" value="http://www.flyhi.in/getResponse.php">
                                        <input type="hidden" name="mode" value="LIVE">
                                        <div class="info">
                                            <label for="txtFirstName" class="inputlabel">Payer First Name
                                                <div class="dot3">:</div>
                                                <div class="mandatory">*</div>
                                            </label>
                                            <input type="text" name="txtFirstName" id="txtFirstName" class="validate[required]" maxlength="100" >
                                        </div>

                                        <div class="info">
                                            <label for="txtLastName" class="inputlabel">Payer Last Name
                                                <div class="dot3">:</div>
                                                <div class="mandatory">*</div>
                                            </label>
                                            <input type="text" name="txtLastName" class="validate[required]" id="txtLastName" maxlength="100" >
                                        </div>

                                        <div class="info">
                                            <label for="email" class="inputlabel">Email
                                                <div class="dot3">:</div>
                                                <div class="mandatory">*</div>
                                            </label>
                                            <input type="text" name="email" class="validate[required,custom[email]]" id="email" maxlength="100" >
                                        </div>

                                        <div class="info">
                                            <label for="txtAddLine1" class="inputlabel">Address Line 1
                                                <div class="dot3">:</div>
                                                <div class="mandatory">*</div>
                                            </label>
                                            <textarea name="address" class="validate[required]" id="address"></textarea>

                                        </div>


                                        <div class="info">
                                            <label for="txtLandmark" class="inputlabel">Landmark (Optional)
                                                <div class="dot3">:</div>
                                            </label>
                                            <input type="text" name="txtLandmark" id="txtLandmark" maxlength="150" >
                                        </div>

                                        <div class="info">
                                            <label for="city" class="inputlabel">City
                                                <div class="dot3">:</div>
                                                <div class="mandatory">*</div>
                                            </label>
                                            <input type="text" name="city" class="validate[required]" id="city" maxlength="150" >
                                        </div>

                                        <div class="info">
                                            <label for="state" class="inputlabel">State
                                                <div class="dot3">:</div>
                                                <div class="mandatory">*</div>
                                            </label>
                                            <input type="text" name="state" class="validate[required]" id="state" maxlength="150" >
                                        </div>

                                        <div class="info">
                                            <label for="postal_code" class="inputlabel">Pincode
                                                <div class="dot3">:</div>
                                                <div class="mandatory">*</div>
                                            </label>
                                            <input type="text" name="postal_code" class="validate[required]" id="postal_code" maxlength="6" >
                                        </div>

                                        <!--<div class="info">
                                            <label for="country" class="inputlabel">Country
                                                <div class="dot3">:</div>
                                                <div class="mandatory">*</div>
                                            </label>-->
                                            <input type="hidden" name="country" value="IND" class="validate[required]"  id="country" maxlength="150" >
                                        <!--</div>-->

                                        <div class="info">
                                            <label for="phone" class="inputlabel">Mobile No.
                                                <div class="dot3">:</div>
                                                <div class="mandatory">*</div>
                                            </label>
                                            <input type="text" name="phone" class="validate[required]" id="phone" maxlength="12" >
                                        </div>

                                        <div class="info">
                                            <label for="description" class="inputlabel">Payment Description
                                                <div class="dot3">:</div>
                                                <div class="mandatory">*</div>
                                                <!-- (Please include PNR No / Ticket No / Invoice No / SOA No and Period in Payment Description) -->
                                                (Please include PNR No / Ticket No / Invoice No / SOA No and Period / Or any other payment details in above field)
                                            </label>
                                            <textarea name="description" class="validate[required]" id="description" ></textarea>
                                        </div>

                                        <div class="info">
                                            <label for="txtPrice" class="inputlabel">Currency
                                                <div class="dot3">:</div>
                                                <div class="mandatory">*</div>
                                            </label>
                                            <select name="currency" id="currency" class="validate[required]">
                                                <option value=""><-- SELECT CURRENCY --></option>
                                                <option value="INR">INR</option>
                                                <option value="USD">USD</option>
                                                <!--<option value="3">SGD</option>-->
                                            </select>

                                        </div>

                                        <div class="info">
                                            <label for="amount" class="inputlabel">Total Price
                                                <div class="dot3">:</div>
                                                <div class="mandatory">*</div>
                                            </label>
                                            <input type="text" name="amount" class="validate[required]" id="amount">
                                        </div>
                                        <!-- <div class="info">
                                            <label for="amount" class="inputlabel">Select Bank
                                                <div class="dot3">:</div>
                                                <div class="mandatory">*</div>
                                            </label>
                                            <input type="radio" name="bank" value="hdfc">HDFC
                                            <input type="radio" name="bank" value="amex">American Express
                                        </div> -->
                                        <div class="info">
                                            <span>
                                                <input type="button" class="careersubmit" name="btnConfirm" id="pqr" value="Proceed" />
                                            </span>

                                        </div>

                                    </form>

                                   
                                </div>

                            </div>	

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--content end-->
        <!--footer start-->
        <!--footer start-->
            <?php include('footer.php'); ?>
            <!--footer end-->
        <!--footer end-->

        <!-- Invoice Popup -->
        <div class="row-fluid" id="invoice">
            <div class="row">
                <div style="font-size:17pt;border-bottom:4px solid black;color:orange;">Payment Details</div>
            </div>

            <div class="row">
                <div style="padding:5px;padding-bottom:0px;border-bottom:1px solid orange;"><?php echo date('d F Y'); ?></div>
            </div>

            <div class="row bold" style="padding:5px;border-bottom:1px solid grey;">ONLINE PAYMENT</div>
            <br />

            <div class="row">
                <!-- <div class="col-lg-6 pull-left">
                    <p id="company_name"><strong>Fly Hi Travels</strong></p>
                    <p id="comp_address1">310, Shalimar Morya Park,</p>
                    <p id="comp_address2">Opposite Infiniti Mall,</p>
                    <p id="comp_town">Andheri West</p>
                    <p id="comp_region">Maharashtra</p>
                    <p id="comp_zipcode">400053</p>
                </div> -->
                <div class="col-lg-6 pull-left">
                    <p id="company_name"><strong>Fly Hi Travels</strong></p>
                    <p id="comp_address1">Art Guild House, LG 5A,</p>
                    <p id="comp_address2">Phoenix Market city, L.B.S. Marg,</p>
                    <p id="comp_town">Kurla (W),</p>
                    <p id="comp_region">Mumbai</p>
                    <p id="comp_zipcode">400070</p>
                </div>

                <div class="col-lg-4 pull-right">
                    <p id="client_name"><strong></strong></p>
                    <p id="cli_address1"></p>
                    <p id="cli_address2"></p>
                    <p id="cli_town"></p>
                    <p id="cli_region"></p>
                    <p id="cli_zipcode"></p>
                </div>
            </div>

            <br />

            <div class="row">
                <table class="table table-striped table-bordered table-hover">
                    <tbody>
                        <tr>
                            <th width="55%">Description</th>
                            <th width="10%" style="text-align:right;">Amount</th>
                        </tr>
                        <tr>
                            <td id="Invoicedesc"></td>
                            <td id="final_amount" style="text-align:right;"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <!-- Invoice Popup -->
        <script type="text/javascript">
            $(document).ready(function(){
                var abc= $('input[type=radio][name=getwayType]').val();
                if($abc = "HDFC") {
                    $("#Pay").css("display", "block");
                    $("#americanExpress").css("display", "none");

                }
                
                $('input[type=radio][name=getwayType]').change(function() {
                    if (this.value == 'HDFC') {
                        
                      $("#Pay").css("display", "block");
                        $("#americanExpress").css("display", "none");

                    }
                    else if (this.value == 'AEXP') {
                       $("#Pay").css("display", "none");
                        $("#americanExpress").css("display", "block");
                    }
                });
            });
            function validateAMEXForm() {

               var FirstName = $("#FirstName").val();
               var LastName = $("#LastName").val();
               var email = $("#email1").val();
               var phone = $("#phone1").val();
               var currency = $("#currency1").val();
               var amount = $("#amount1").val();
               var description = $("#description1").val();
               var errors=[];
               if(FirstName == "") {
                errors.push("Firstname is required");
               }
               if(LastName ==""){
                errors.push("Last name is required");
               }
               if(email == "") {
                errors.push("email is required");
               }
               if(phone == "") {
                errors.push("phone number is required");
               }
               else if(!phone.match('[0-9]{10}')){
                errors.push("please enter valid 10 digit mobile number.");

               }
               if(currency == "") {
                errors.push("currency is required");
               }
               if(amount == "") {
                errors.push("amount  is required");
               }
               if(description == "") {
                errors.push("description is required");
               }
                $("#errors").empty();
               var errorMessage = "";
               if(errors.length != 0){

                for(var i=0; i<errors.length; i++){
                    var srNo = i + 1;                  
                    errorMessage = errorMessage + "<div class='alert-danger'>"+srNo + ")" + errors[i] + ".</div>";
                }
                 $("#errors").append(errorMessage);
                
                alert($("#errors").text());
              
                return false;
               }

            }
        </script>
    </body>
</html>
