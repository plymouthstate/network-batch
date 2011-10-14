WordPress Network Batch Processing
==================================

First pass at a plugin to run a callback on all blogs in a WordPress Network.
Hooks into the "Upgrade Network" functionality, using HTTP calls to upgrade
five blogs at a time.

1. Network Activate this plugin
2. Create a callback function on the backend (e.g. mu-plugins)
3. Visit "Network Batch" under the Network Admin "Plugins" menu
4. Specify the callback, and click "Batch Process"
