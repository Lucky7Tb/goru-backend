const mix = require('laravel-mix');

mix.setPublicPath("public")
    .js("resources/js/axios.js", "js")
    .js("resources/js/bootstrap.js", "js")
    .js("resources/js/jquery.js", "js")
    .sass("resources/scss/bootstarp.scss");

