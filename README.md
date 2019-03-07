# realm

### Core process

Requests will be redirect to `index.php` by Apache2 Rewrite Module and the path tree will be converted in query string splitted as:

- domain
- subdomain
- request

Include Files, as images and scripts, that will be ignored in that rule:

- scripts: `.js`
- stylesheets: `css`, `less`
- images: `png`
