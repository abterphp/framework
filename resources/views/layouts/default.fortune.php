<!doctype html>
<html lang="en">
<head>
    {{! charset("utf-8") !}}
    {{! pageTitle($title) !}}

    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{! meta('description', $metaDescription) !}}
    {{! metaKeywords($metaKeywords) !}}
    {{! favicon('/favicon.png') !}}
    {{! authorName($authorName) !}}

    {{! meta('copyright', $metaCopyright) !}}
    {{! meta('robots', $metaRobots) !}}
    {{! meta('canonical', $pageUrl) !}}

    {{! meta('og:url', $pageUrl) !}}
    {{! meta('og:image', $metaOGImage) !}}
    {{! meta('og:description', $metaOGDescription, $metaDescription) !}}
    {{! meta('og:title', $metaOGTitle, $title) !}}
    {{! meta('og:site_name', $siteTitle) !}}
    {{! meta('og:see_also', $homepageUrl) !}}

    {{! meta('twitter:card', 'summary') !}}
    {{! meta('twitter:url', $pageUrl) !}}
    {{! meta('twitter:title', $metaOGTitle, $title) !}}
    {{! meta('twitter:description', $metaOGDescription, $metaDescription) !}}
    {{! meta('twitter:image', $metaOGImage) !}}

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