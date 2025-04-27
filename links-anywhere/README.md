# Links anywhere
This plugin for Bludit is based on the default [Links](https://github.com/bludit/bludit/tree/v3.0/bl-plugins/links) plugin. It allows you to set a list of links in the admin area and then display them anywhere you want in your theme via a custom hook, `linksHere`.

## How to
1. Define a list of links in the admin area
2. Copy `<?php Theme::plugins('linksHere'); ?>` in the PHP page of you theme where you wish to display the links. It will generate an unstyled unordered list:
```
<ul>
  <li><a href="https://linkURL" target="_blank">Link name</a></li>
  <li><a href="https://linkURL" target="_blank">Link name</a></li>
</ul>
```
