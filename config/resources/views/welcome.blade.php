<!DOCTYPE html>
<html>
    <head>
        <title>VBP | Tanist</title>

        <script src="https://cdn.shopify.com/s/assets/external/app.js"></script>
        <script type="text/javascript">
          ShopifyApp.init({
            apiKey: '{{ $apiKey }}',
            shopOrigin: 'https://{{ $merchantData->shop_name }}'
          });

          ShopifyApp.ready(function(){
  ShopifyApp.Bar.initialize({
    icon: 'http://localhost:3001/assets/header-icon.png',
    title: 'The App Title',
    buttons: {
      primary: {
        label: 'Save',
        message: 'save',
        callback: function(){
          ShopifyApp.Bar.loadingOn();
          doSomeCustomAction();
        }
      }
    }
  });
});
        </script>
    </head>
    <body>
        <div class="container">
            <div class="content">
                <div class="title">Volume Based Pricing by Tanist</div>
            </div>
        </div>
    </body>
</html>
