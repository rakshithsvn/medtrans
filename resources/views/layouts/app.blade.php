<!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>MedTrans</title>
        <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="/assets/css/bootstrap.css">
        <link rel="stylesheet" href="/assets/vendors/bootstrap-icons/bootstrap-icons.css">
        <link rel="stylesheet" href="/assets/css/app.css">
        <link rel="stylesheet" href="/assets/css/pages/login.css">
        <link rel="shortcut icon" href="/assets/images/favicon.ico" type="image/x-icon">
        @yield('style')
    </head>

    <body>
        <div id="wrapper">
            <div class="row h-100">
                <div class="col-12">
                    <div id="login-form">
                        <div class="wrapper-logo">
                            <a href=""><img src="/assets/images/logo/medtrans.png" alt="Logo" class="img-fluid"></a>
                        </div>

                        <main>
                            @yield('content')
                        </main>

                    </div>
                </div>
            </div>
        </div>
        <script src="/assets/js/jquery.min.js"></script>
        @yield('script')
    </body>

</html>
