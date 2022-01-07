<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>::. DoorCalc .::</title>
  <style>
    p {
      font-size: 14px;
    }

    a:link {
      color: #05a2d6;
    }
  </style>
</head>

<body>
  <table width="640px" border="0" style="border-collapse: collapse;">
    <tr>
      <td style="padding:50px 10px;text-align: center;border-collapse: collapse;"><img src="{{url('uploads/mail/doorcalc-logo.png')}}"
          alt="doorcalc-logo" /></td>
    </tr>
    <tr>
    <tr>
      <td style="padding-left: 50px;box-sizing: border-box;">
        <img src="{{url('uploads/mail/line.png')}}">
      </td>
    </tr>
    <td style="text-align: justify;padding-left: 50px;box-sizing: border-box;"><br /><br />
      <p>Hi {{$name}},<br /><br /> Thank you for your inquiry. </p><br />
      <p style="line-height: 1.6;">Here’s quote {{$quote_id}} for USD ${{$price}}.<br /> You can view
        your quote online here:<br> 
        <?php
       $quote_id =  preg_replace('/[^0-9.]+/', '', $quote_id);
       ?> 
        <a
          href={{url('/')."/".$quote_id."/view-quote/"}}>{{url('/')."/".$quote_id."/view-quote/"}}</a><br />From there, you can accept, decline, comment or point.</p><br />
      <p> If you have any question, please let us know.</p><br />
      <p style="line-height: 1.6;">Thanks,<br />Barry Johnson,<br /><a
          href="mailto:barryjohnson@gmail.com">barryjohnson@gmail.com</a><br />m:
        512.694.6608<br />o: 512.964.6874</p><br />
    </td>
    </tr>
    <tr>
      <td style="padding-left: 50px;box-sizing: border-box;"><img src="{{url('uploads/mail/line.png')}}"></td>
    </tr>
  </table>

</body>
</html>