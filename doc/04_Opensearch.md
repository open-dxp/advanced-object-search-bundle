# OpenSearch Client Setup

:::info

This bundle requires minimum version of OpenSearch 2.7.

:::

Following configuration is required to set up OpenSearch. The OpenSearch client configuration takes place via [OpenDXP Opensearch Client](https://github.com/open-dxp/opensearch-client-bundle) and has two parts:
1) Configuring an OpenSearch client.
2) Define the client to be used by Advanced Object Search bundle.

```yaml
# Configuring an OpenSearch client
opendxp_open_search_client:
    clients:
        default:
            hosts: ['https://opensearch:9200']
            password: 'admin'
            username: 'admin'
            ssl_verification: false


# Define the client to be used by advanced object search
opendxp_advanced_object_search:
    client_name: default  # default is default value here, just need to be specified when other client should be used.
```

If nothing is configured, a default client connecting to `localhost:9200` is used.

For the further configuration of the client, please refer to the [OpenDXP OpenSearch Client documentation](https://github.com/open-dxp/opensearch-client-bundle/blob/1.x/doc/02_Configuration.md).
