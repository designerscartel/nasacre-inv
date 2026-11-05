=== NASACRE RE Hubs ===
Contributors: matttullett
Requires at least: 6.0
Requires PHP: 7.4
Stable tag: 1.0.0

Provides a searchable RE Hubs directory backed by the NASACRE database API.

== Usage ==

Add this shortcode to a WordPress page:

[re_hubs]

Optional attributes:

[re_hubs type="places"]
[re_hubs type="speakers"]
[re_hubs type="all" show_filters="false"]

The type value can be all, places, or speakers.

== Configuration ==

The API base URL can be changed with the nasacre_re_hubs_api_url filter.
