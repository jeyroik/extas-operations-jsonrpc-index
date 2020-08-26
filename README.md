![tests](https://github.com/jeyroik/extas-operations-jsonrpc-index/workflows/PHP%20Composer/badge.svg?branch=master&event=push)
![codecov.io](https://codecov.io/gh/jeyroik/extas-operations-jsonrpc-index/coverage.svg?branch=master)
<a href="https://github.com/phpstan/phpstan"><img src="https://img.shields.io/badge/PHPStan-enabled-brightgreen.svg?style=flat" alt="PHPStan Enabled"></a>
<a href="https://codeclimate.com/github/jeyroik/extas-operations-jsonrpc-index/maintainability"><img src="https://api.codeclimate.com/v1/badges/b29d6132aa60ea0e36ef/maintainability" /></a>
<a href="https://github.com/jeyroik/extas-installer/" title="Extas Installer v3"><img alt="Extas Installer v3" src="https://img.shields.io/badge/installer-v3-green"></a>
[![Latest Stable Version](https://poser.pugx.org/jeyroik/extas-operations-jsonrpc-index/v)](//packagist.org/packages/jeyroik/extas-jsonrpc)
[![Total Downloads](https://poser.pugx.org/jeyroik/extas-operations-jsonrpc-index/downloads)](//packagist.org/packages/jeyroik/extas-jsonrpc)
[![Dependents](https://poser.pugx.org/jeyroik/extas-operations-jsonrpc-index/dependents)](//packagist.org/packages/jeyroik/extas-jsonrpc)

# extas-operations-jsonrpc-index

Index operation for JSON RPC.

# Спецификация

```json
{
  "request": {
    "type": "object",
    "properties": {
      "select": {
      		"type": "array",
      		"items": {"type": "string"}
      	},
      	"filter": {
      		"type": "object",
      		"properties": {}
      	},
      	"sort": {
      		"type": "object",
      		"properties": {}
      	},
      	"limit": {
      		"type": "number"
      	},
      	"offset": {
      		"type": "number"
      	}
    }
  }
}
```

# Пример запроса

```json
{
  "id": "2f5d0719-5b82-4280-9b3b-10f23aff226b",
  "method": "snuff.index",
  "params": {
    "limit": 1,
    "offset": 0,
    "sort": {"name": -1},
    "select": ["name", "value"],
    "expand": ["snuff.item.description"]
  }
}
```

`Примечание` возможности `expand` можно изучить в документации пакета `extas-expands`.