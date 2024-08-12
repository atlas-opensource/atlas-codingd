<div>
Distribution (M) Zero or DMZ is a framework which can embody existing opensource and proprietary technologies (or new products).

The DMZ framework is little more than a legal (license) which allows individuals to maintain ownership of resources (described below) which 
rightfully belong to them.

Specifically, DMZ allows individuals and organizations to package, license and commoditize their compute, memory,
bandwidth, metadata, behavioral data, carbon offsets, power (in watts), variable caital (labor) and land capital (intellectual and physical property).

By simply packaging their technology under this project the GNU license framework enables the user to allow or deny the sale of meta and behavioral data 
(and other resources which belong to the user) to  vendors of the user's choice.

As an example, another open source project called Coding(d) can be added to this project. Coding(d) can be found here: https://github.com/atlas-opensource/atlas-codingd.
A user can then use Coding(d) to generate their own analytic schema's whch can be applied to social media from Twitter. 

For example if a user is analyzing a Tweet from the United Nations High Commissioner for Human Rights they could create the following schema consisting of five categoires:

<ol>
    <li>Media</li>
    <ul>
        <li>No media</li>
        <li>Video(s)</li>
        <li>Image(s)</li>
        <li>Audio(s)</li>    
    </ul>
    <ul>
        <li>Language</li>
        <li>French</li>
        <li>Arabic</li>
        <li>Japaneese</li>
        <li>English</li>>
    </ul>
</ol>
3. Topic
    A. Upcoming Public Event
    B. Past Public Event
    C. Human Rights Incident
    D. Human Rights Legislation
4. Audience
    A. General Public
    B. American Public
    C. Canadian Government
    D. French Public
5. Speaker
    A. High Commissioner
    B. Staff
    C. Other Public Figure
    D. A Random Cat

Another example might include an analysis of a Tweet by a random individual with a video of a young lady:

1. Gender
    A. Male
    B. Female
    C. Non-Binary
    D. Other
2. Hair Color
    A. Red
    B. Brown
    C. Blonde
    D. Green
3. Activity
    A. Walking
    B. Running
    C. Sitting Down
    D. Standing Still
4. Emotion from Facial Expression
    A. Distraught
    B. Happy
    C. Sad
    D. Morose
5. Probable Next Behavior
    A. Running
    B. Resting
    C. Standing Up
    D. Starting moving
</div>
Coding(d) can also be used to code nearly any other information set (with a little bit of easy coding) and further used to train machine learning algorithms to speed up the
process of analyzing information. This further allows users to quickly generate their own analytic products for sale to advertisers, 
content creators and other interested people. 

Keep in mind that Coding(d) is just one example of a software package which can be housed under the DMZ framework to which the GNU License is applied.
The DMZ project is however primarily concerned with software and other technologies which are capable of allowing individuals and
organizations to commoditize their compute, memory, bandwidth, metadata, behavioral data, carbon offsets, power (in watts), variable caital (labor) 
and land capital (intellectual and physical property).






<hr>
<p align="center">
    <a href="https://github.com/yiisoft" target="_blank">
        <img src="https://avatars0.githubusercontent.com/u/993323" height="100px">
    </a>
    <h1 align="center">Yii 2 Advanced Project Template</h1>
    <br>
</p>

Yii 2 Advanced Project Template is a skeleton [Yii 2](http://www.yiiframework.com/) application best for
developing complex Web applications with multiple tiers.

The template includes three tiers: front end, back end, and console, each of which
is a separate Yii application.

The template is designed to work in a team development environment. It supports
deploying the application in different environments.

Documentation is at [docs/guide/README.md](docs/guide/README.md).

[![Latest Stable Version](https://img.shields.io/packagist/v/yiisoft/yii2-app-advanced.svg)](https://packagist.org/packages/yiisoft/yii2-app-advanced)
[![Total Downloads](https://img.shields.io/packagist/dt/yiisoft/yii2-app-advanced.svg)](https://packagist.org/packages/yiisoft/yii2-app-advanced)
[![build](https://github.com/yiisoft/yii2-app-advanced/workflows/build/badge.svg)](https://github.com/yiisoft/yii2-app-advanced/actions?query=workflow%3Abuild)

DIRECTORY STRUCTURE
-------------------

```
common
    config/              contains shared configurations
    mail/                contains view files for e-mails
    models/              contains model classes used in both backend and frontend
    tests/               contains tests for common classes    
console
    config/              contains console configurations
    controllers/         contains console controllers (commands)
    migrations/          contains database migrations
    models/              contains console-specific model classes
    runtime/             contains files generated during runtime
backend
    assets/              contains application assets such as JavaScript and CSS
    config/              contains backend configurations
    controllers/         contains Web controller classes
    models/              contains backend-specific model classes
    runtime/             contains files generated during runtime
    tests/               contains tests for backend application    
    views/               contains view files for the Web application
    web/                 contains the entry script and Web resources
frontend
    assets/              contains application assets such as JavaScript and CSS
    config/              contains frontend configurations
    controllers/         contains Web controller classes
    models/              contains frontend-specific model classes
    runtime/             contains files generated during runtime
    tests/               contains tests for frontend application
    views/               contains view files for the Web application
    web/                 contains the entry script and Web resources
    widgets/             contains frontend widgets
vendor/                  contains dependent 3rd-party packages
environments/            contains environment-based overrides
```
