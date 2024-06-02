---
name: Top Contributors
title: Stay Informed, Join Thailand's Community Forum!
---

Hi @{{ mapper.displayName }},

Congratulations on being one of the [top OpenStreetMap contributors in Thailand](https://osmstats.neis-one.org/?item=countries&country=Thailand) this month! Your incredible contributions are making a big difference, and we truly appreciate it.

If you haven't already, please join [Thailand's Community Forum](https://community.openstreetmap.org/c/communities/th/53). Here, like-minded OSM enthusiasts and organizations discuss important issues, define country-specific guidelines to harmonize mapping across Thailand, and create a more welcoming and efficient environment for everyone, including new mappers.

As a significant contributor to Thailand, your voice and opinion matter greatly.

While I completely understand if you’re not interested in participating in these discussions, I encourage you to at least join the forum and stay informed. You can do this after signin up and logging in, by clicking on the notification icon [🔔] in the top right corner and selecting “Watching First Post” to get notified whenever a new topic is posted.

Please note that some decisions made on the forum, based on best practices and community consensus, may override your past tagging history. In case of conflicts with other mappers, the Thailand guidelines will always take precedence over global or personal interpretations.

Here are some notable changes and news from the past 18 months:

- Grab, after multiple complaints, has stopped hiring remote mappers and now follows the [Organised Editing Guidelines](https://osmfoundation.org/wiki/Organised_Editing_Guidelines) using local-based mappers.
- Administrative levels have been revamped to reflect the introduction of Subdistrict Administrative Organizations (SAO) and municipalities.
- Guidelines have been established to differentiate traditional gas stations from vending machines and shops selling fuel from drummed barrels.
- Major road descriptions have been upgraded to include specific features to determine their classifications.
- The use of `highway=service + service=alley` for narrow residential roads is being replaced by `highway=residential + lanes=1`.
- The tag `highway=living_street` has no legal usage in Thailand and has been mostly replaced with `highway=residential` or `highway=pedestrian`.
- [Default legal access restrictions](https://wiki.openstreetmap.org/wiki/OSM_tags_for_routing/Access_restrictions#Thailand) have been established, including the default `motorcycle=yes` on `highway=path`.
- Pathways less than 2 meters wide should be tagged based on their primary use:  `highway=cycleway` for cyclists,  `highway=footway` for pedestrians, or the general `highway=path` when there is no specific restriction.
- Unsigned legal access tags (`bicycle=no`, `foot=yes`, ...) should not be used to indicate the suitability of a vehicle based on the road's physical conditions; instead, use the appropriate scale tags (`smoothness`, `mtb:scale`, `dirtbike:scale`, `sac_scale`, ...).

Keep up the great mapping, and hope to see you in the forum!

On behalf of the OpenStreetMap Thailand community,

{{ app.user.displayName }}
