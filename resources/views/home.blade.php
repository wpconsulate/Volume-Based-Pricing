<!DOCTYPE html>
<html>
    <head>
        <title>VBP | Tanist</title>

        <script src="https://cdn.shopify.com/s/assets/external/app.js"></script>
        <script type="text/javascript">
          ShopifyApp.init({
            apiKey: '{{ $apiKey }}',
            shopOrigin: 'https://{{ $merchantData->shop_name }}',
            debug: {{ env('APP_DEBUG') }},
          });

          ShopifyApp.ready(function(){
            // ShopifyApp.Bar.initialize({
            //   // icon: 'http://localhost:3001/assets/header-icon.png',
            //   title: 'Volume Based Pricing by Tanist',
            //   buttons: {
            //     primary: {
            //       label: 'Save',
            //       message: 'save',
            //       callback: function(){
            //         // ShopifyApp.Bar.loadingOn();
            //         // doSomeCustomAction();
            //       }
            //     }
            //   }
            // });
          });
          ShopifyApp.Bar.loadingOff()
        </script>

        <!-- <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous"> -->
    </head>
    <body>
        <div class="container">
            <div class="content">
                <div class="title">Volume Based Pricing by Tanist</div>
            </div>
        </div>
    </body>
</html>
