<?

$client = new Google_Client();
$client->setScopes(array('https://www.googleapis.com/auth/analytics.readonly'));
$client->setAuthConfig(__DIR__.'/MadWave-9c555fc9ca9b.json');
$analytics = new Google_Service_Analytics($client);

?>

<pre>
    <?= print_r(
        $analytics
            ->data_ga
            ->get(
                'ga:123550906',
                date('Y-m-d', strtotime("-1 month")),
                date('Y-m-d'),
                'ga:correlationScore',
                [
                    'dimensions' => 'ga:queryProductId,ga:relatedProductId',
                    'max-results' => 3000
                ]
            )
    ); ?>
</pre>
