<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <style>
      body{width: 35%; margin: 0 auto; background-image: url(https://medlead.atechmlr.com/assets/images/bg.jpg); display: flex; justify-content: center; align-items: center; height: 100vh; background-color: #f0f0f0;}
      .logo{height: 100px}
      .img-fluid{max-width: 100%}
      .text-center{text-align: center;}
      .bg-white{background: #f8f9fb; padding: 30px; margin: 30px}
      @media only screen and (max-width: 800px){body{width: 100%}}
    </style>
  </head>
  <body>
    <div class="bg-white">
      <div class="text-center">
        <img src="https://medlead.atechmlr.com/assets/images/logo/medtrans.png" class="img-fluid logo"/></div>
        <h3>Dear {{ @$doctor->name }}</h3>
        <p>I hope this letter finds you in good health and spirits.On behalf of MeadLead, We would like
        to extend our sincerest gratitude for yur continued support and for referring your patient <b>{{@$patient->name}}</b>
        to our hospital for thier medical needs. The Patient was seen and admitted under <b>{{@$patient->doctor}}.</b>
        </p>

        <p>Your trust in our medical team and facilities is greatly appreciated, and we are honored to have the opportunity to
        care for your patients. Yout referral speaks volumes about your confidence in our ability to provide high-quality and
        compassionate care.</p>

        <p>Should you or your patients have any queries or in need of assistance, please feel free to get in touch with our 
        <b>Doctor relations Manager</b></p>

        <div class="text-center"><img src="https://medlead.atechmlr.com/assets/images/thank.jpg" class="img-fluid"/>
        <p><b style="color: #711310;">Marketing Department <br> MeadLead</b></p>
      </div>
    </div>    
  </body>
</html>
