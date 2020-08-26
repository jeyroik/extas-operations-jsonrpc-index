

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