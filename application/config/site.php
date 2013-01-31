<?php
    /* Dynamically alter title by using title methods in the view object of the controllers */
    $config['title'] = 'Change the title in application/config/config';

    /* The meta array can contain as many custom meta tags as you want it to. They all get rendered. */
    $config['meta'] = array(
        'keywords' => array('attr' => 'name', 'content' => 'Change us in application/config/config'),
        'description' => array('attr' => 'name', 'content' => 'Change me in application/config/config'),
        'og:type' => array('attr' => 'property', 'content' => 'website'),
        'viewport' => array('attr' => 'name', 'content' => 'width=device-width'),
        'X-UA-Compatible' => array('attr' => 'http-equiv', 'content' => 'IE=edge,chrome=1')
    );