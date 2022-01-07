<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <table border="0" width="20%">
        <tr>
	        <td width="7%" align="right" style="padding-right: 20px;"><label for="user_id">User_id :</label></td>
	        <td width="25%">{{$quotation->user_id}}</td>
        </tr>
        <tr>
            <td align="right" style="padding-right: 20px;"><label for="sub_total">Sub_total :</label></td>
            <td>{{$quotation->sub_total}}</td>
        </tr>
        <tr>
	        <td align="right" style="padding-right: 20px;"><label for="total">Total :</label></td>
	        <td>{{$quotation->total}}</td>
        </tr>
        <tr>
	        <td align="right" style="padding-right: 20px;"><label for="total">Tax :</label></td>
	        <td>{{$quotation->tax}}</td>
        </tr>
    </table>
</body>
</html>