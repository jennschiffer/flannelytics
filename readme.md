# flannelytics
## using the google api php client to get the number of page views of a given page

### configure

You'll need to create a service account and then update `config.php` with your service account email and location of your .p12 key file. [See google's documentation on how to do get started with the api and create service accounts ok!](https://developers.google.com/analytics/devguides/reporting/core/v3/quickstart/service-php). Also important - you need to add that service account email as a read-only user to your site's admin in Analytics.

You can also set the start and end date from when you want views shown in the same `config.php` file. ~* yeehaw *~

### how to use

Load `flannelytics.php` into your app, it will take care of loading the google api php client itself. Now you can call `getPageViews( $slug )` where `$slug` is the unique page link that you want the page view count of. You will be returned a number of views if all goes well. If all doesn't go well, then, *sucks brother*.

### note

I made this for a WordPress site where the clients wanted to show post view count. Beyond that and some terminal scripts, this hasn't been tested much. Let me know if something goes horribly wrong in the issues!

xoxo [j$](http://jennmoney.biz)
