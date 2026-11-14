// npm install --save critical

var http = require('http');
var fs = require('fs');
var critical = require('critical');

var request = http.request({
    host: 'dev.madwave.ru',
    path: '/catalog/gidrokostjumy-dlya-plavaniya'
}, function (res) {
    var data = '';
    res.on('data', function (chunk) {
        data += chunk;
    });
    res.on('end', function () {
        fs.writeFile("./critical.temp.html", data, function(err) {
            if(err) {
                return console.log(err);
            }
            console.log("The file was saved!");
        });
        critical.generate({
            base: './',
            src: 'critical.temp.html',
            dest: 'critical.css',
            minify: true,
            width: 1300,
            height: 900
        });
        // fs.unlinkSync('./critical.temp.html');
    });
});
request.on('error', function (e) {
    console.log(e.message);
});
request.end();