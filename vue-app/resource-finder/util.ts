export function getRouteBaseByEnvironment() {
    return window.location.pathname;

    // Following code is not deleted only for reference purpose.
    if (window.location.pathname.indexOf("/components") !== -1) {
        if (
            window.location.pathname.endsWith("/resources/") ||
            window.location.pathname.endsWith("/resources") ||
            window.location.pathname.endsWith("/resources.html")
        ) {
            return window.location.pathname;
        }
    } else {
        return "/resources";
    }
}

export function getAPIBaseByEnvironment() {
    if (window.location.hostname === "localhost") {
        return "http://umd-ume.lndo.site/api/resource_topics";
    } else {
        return `${window.location.origin}/api/resource_topics`;
    }
}
