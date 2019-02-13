<?php
use think\facade\Route;

Route::domain('www', function(){
    Route::get('/', 'index/Index/index');
    Route::get('list', 'index/Index/index');
    Route::get('<action>', 'index/Index/index/<action>');
})
->bind('index');
