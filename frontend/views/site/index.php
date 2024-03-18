<?php

/* @var $this yii\web\View */

# Include useful namespaces
use yii\helpers\Url;

# Set the title
$this->title = 'Coding(d) @ ATLAS: UTSA Political Science Analysis Platform';

# Begin page content
?>
<div class="site-index">

    <div class="jumbotron">
        <h1>Welcome to Coding(d) @ ATLAS Research & Development</h1>

        <p class="lead">ATLAS is UNIX like environment designed for social sciences academic research at the University of Texas at San Antonio.</p>
        <p class="lead">Coding(d) @ ATLAS Research & Development is an open sources analysis tool for academic and OSINT researchers and professionals.</p>

        <p><a class="btn btn-lg btn-success" href="<?php echo Url::toRoute('site/signup'); ?>">Get started with Coding(d)</a></p>
        <!-- <p><a class="btn btn-lg btn-success" href="site/signup">Get started with ATLAS</a></p> -->

    </div>

    <div class="body-content">
    </div>
</div>
