=== NASACRE RE Hubs ===
Contributors: matttullett
Requires at least: 6.0
Requires PHP: 7.4
Stable tag: 1.2.0

Provides a searchable RE Hubs directory backed by the NASACRE database API.

== Usage ==

Add this shortcode to a WordPress page:

[re_hubs]

Optional attributes:

[re_hubs type="places"]
[re_hubs type="speakers"]
[re_hubs type="all" show_filters="false"]

The type value can be all, places, or speakers.

== Region-first directory ==

List regions first, then show entries when a visitor selects a region:

[re_hub_regions]

Show the type filter and badges (default):

[re_hub_regions type="all" show_type_filter="true" show_type_badges="true"]

Fix the directory to Places of Interest without a type selector:

[re_hub_regions type="places" show_type_filter="false"]

Fix the directory to School Speakers without a type selector:

[re_hub_regions type="speakers" show_type_filter="false"]

Show all entries without a type selector or type badges:

[re_hub_regions type="all" show_type_filter="false" show_type_badges="false"]

Hide all search and type filters:

[re_hub_regions show_filters="false"]

Remove search but keep the type filter:

[re_hub_regions search_mode="none"]

Search entries. The region list is reduced to regions containing matching entries, and the search carries into the selected region:

[re_hub_regions search_mode="entries"]

Search region names only:

[re_hub_regions search_mode="regions"]

The search_mode value can be none, entries, or regions. It defaults to entries.

== Configuration ==

The API base URL can be changed with the nasacre_re_hubs_api_url filter.
