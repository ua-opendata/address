# Ukranian Addresses Dataset

- [Demo](https://ua-opendata.github.io/address/)
- [API](https://ua-opendata.github.io/address/regions.json)

## Files
### Full Dataset
- [CSV](./docs/houses.csv)
- [JSON](./docs/houses.json)
### Partial Dataset
Every file contains child list with `file` property
```json
{
  "name": "Child",
  "file": "parent/child.json"
}
```
- [regions.json](./docs/regions.json)

## Generate
```bash
php parse.php
```
