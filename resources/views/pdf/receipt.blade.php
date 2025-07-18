<!DOCTYPE html>
<html lang="en">
<head>
 <meta charset="utf-8">
 <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

 {{-- <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet"> --}}

    {{-- <link rel="stylesheet" href="{{ public_path('assets/vendors/bootstrap-icons/bootstrap-icons.css') }}">
    <link rel="stylesheet" href="{{ public_path('assets/css/app.css') }}">
    <link rel="stylesheet" href="{{ public_path('assets/css/app.css') }}"> --}}
    <link rel="stylesheet" href="{{ public_path('assets/css/bootstrap.css') }}">

    <style type="text/css">    
    @media print {
        @page {size: landscape}
        .light-blue {background:#e3f8ff !important;-webkit-print-color-adjust: exact; }
        table {background:#e3f8ff !important;-webkit-print-color-adjust: exact; }
        table td,table.exam-popup td {background:#e3f8ff !important;-webkit-print-color-adjust: exact; }
        table.exam-popup th {background: #164966 !important;color:#fff;-webkit-print-color-adjust: exact;}
        tr {background: #164966;-webkit-print-color-adjust: exact;}
        table.exam-popup td {border:1px solid #000;}
        table.top-table td {border:0;}
        table.table-bordered td { border-width: 0 1px; border-color: #d7d7d7;-webkit-print-color-adjust: exact; }
    }

    /*p,td,th{ font-size: 18px; }
    h5{ font-size: 25px; }
    h6{ font-size: 18px; }*/

   /* table, tr, td, th, tbody, thead, tfoot { page-break-inside: avoid !important; }
    thead{display: table-header-group;}
    tfoot {display: table-row-group;}
    tr {page-break-inside: avoid;}*/

    h4,h6{ margin:0; }
    table td{ padding: 4px !important }
    table.top-table td{ border: 0;  }
    th{background-color: #164966 !important; color: #fff;}    
    .light-blue{background: #e3f8ff;}
    .marksheet{width: 90px !important; margin-right: 10px !important; position: relative !important; top: 16px !important; }
    .card .card-title { font-size: 2rem; width: 40%; display: inline-block; }
    .marks { font-size: 18px !important; font-weight: 200;}
    .dataTable-table { max-width: 2000px; width: 960px; border-spacing: 0; border-collapse: separate; }
    .dataTable-top > div:last-child, .dataTable-bottom > div:last-child{float: left; margin-top: 10px;}
}
</style>

</head>

<body>
    <div class="container-fluid light-blue">
        <table class="table border-bottom logo-table" style="text-align: center; margin: 0">
            <tr>
               <td width="100%">
                <div class="d-flex justify-content-center">
                    <div><img src="{{ public_path('assets/images/logo/medtrans.png') }}" class="img-fluid mb-3 marksheet"></div>
                    <div class="mt-3"><h5>MedTrans</h5>
                       
                   </div>
               </div>
           </td>
       </tr>
   </table>

   <table class="table mb-3 top-table">
    <tr>
        <td colspan="4" align="center"><h5 class="my-1">Receipt Statement</h5></td>
    </tr>               
    <tr>
        <td><b class="text-dark p-3">Roll Number:</b> {{ @$data->RollNumber }}</td>
        <td><b class="text-dark">Name:</b> {{ @$data->FirstName }} {{ @$data->LastName }}</td>
        <td><b class="text-dark">Campus ID:</b> {{ @$data->CampusID }}</td>
    </tr>               
</table>

<div class="table-responsive">
    <table class="table table-bordered exam-popup" style="border:1px solid #d7d7d7">
        <thead>
            <tr>
                <th>SL NO</th>
                <th>Receipt Number</th>
                <th>Payment Date</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach(@$past_receipt as $key=>$receipt)
            <tr>
               <td>{{ @$key+1 }}</td>
               <td>{{ @$receipt->ReceiptNumber }}</td>
               <td>{{ Carbon\Carbon::parse(@$receipt->PaymentDate)->format('d/m/Y') }}</td>
               <td align="right">{{ number_format(@$receipt->Amount,2) }}</td>
           </tr>
           @endforeach
       </tbody>
   </table>
</div>
<br/>
<b class="text-dark p-3">College Name:</b> MedTrans Medical College
</div>
</div>
</body>
</html>