# Upgrade Notes

## 1.1.0
- [CHORE] Replace Codeception with Pest and `open-dxp/test-foundation`
- [CHORE] Require `open-dxp/opendxp` ^1.5

### Migrate from `pimcore/advanced-object-search` to `open-dxp/advanced-object-search-bundle`
  * Renamed bundle to `open-dxp/advanced-object-search-bundle`
  * Changed top-level PHP namespace to `OpenDxp\Bundle\AdvancedObjectSearchBundle`
  * Changed bundle name to `OpenDxpAdvancedObjectSearchBundle`
  * Changed top-level config node to `opendxp_advanced_object_search`
