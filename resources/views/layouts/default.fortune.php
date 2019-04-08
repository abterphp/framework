<!doctype html>
<html lang="en">
<head>
    {{! charset("utf-8") !}}
    {{! pageTitle($title) !}}

    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{! metaName('description', $metaDescription) !}}
    {{! metaKeywords($metaKeywords) !}}
    {{! favicon('/favicon.png') !}}
    {{! author($metaAuthor) !}}

    {{! metaName('copyright', $metaCopyright) !}}
    {{! metaName('robots', $metaRobots) !}}
    {{! metaName('canonical', $pageUrl) !}}

    {{! metaProp('og:url', $pageUrl) !}}
    {{! metaProp('og:image', $metaOGImage) !}}
    {{! metaProp('og:description', $metaOGDescription, $metaDescription) !}}
    {{! metaProp('og:title', $metaOGTitle, $title) !}}
    {{! metaProp('og:site_name', $siteTitle) !}}
    {{! metaProp('og:see_also', $homepageUrl) !}}

    {{! metaName('twitter:card', 'summary') !}}
    {{! metaName('twitter:url', $pageUrl) !}}
    {{! metaName('twitter:title', $metaOGTitle, $title) !}}
    {{! metaName('twitter:description', $metaOGDescription, $metaDescription) !}}
    {{! metaName('twitter:image', $metaOGImage) !}}

    {{! $preHeader !}}
    {{! $header !}}
    {{! $postHeader !}}

    <% if ($layout) %>
    {{! assetCss($layout) !}}
    <% endif %>
    <% if ($page) %>
    {{! assetCss( $page ) !}}
    <% endif %>
</head>
<body>
    <% show("content") %>

    <!-- Optional JavaScript -->
    {{! $preFooter !}}
    {{! $footer !}}
    {{! $postFooter !}}

    <!-- Scripts Starts -->
    <% if ($layout) %>
    {{! assetJs( $layout ) !}}
    <% endif %>
    <% if ($page) %>
    {{! assetJs( $page ) !}}
    <% endif %>
    <!-- Scripts Ends -->
</body>
</html>