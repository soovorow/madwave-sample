Screaming Crawl
===============

Yii2 component for comparing screaming frog crawl results


TODO
----

* list of exceptions for avoiding links in comparing
* showing redirects
* showing pages with 404 and 500+ errors
* export tables into .xls


INSTALLATION
------------

You can then install the application using the following command from your application root:

~~~
$ cd common/components
$ git clone https://github.com/soovorow/screaming-crawl.git
~~~


GETTING STARTED
---------------

Add following code in your controller 

```PHP
    public function actions() 
    {
        return [
            // ...
            'screaming-crawl' => [
                'class' => 'common\components\screaming_crawl\ScreamingCrawlAction'
            ]
            // ...
        ];
    }
```

Then component will be available by this route: %your-domain%/%your-controller%/screaming-crawl